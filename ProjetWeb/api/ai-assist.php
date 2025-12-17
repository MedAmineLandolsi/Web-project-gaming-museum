<?php
// Simple AI assistant endpoint to help users fill the complaint form.
// POST JSON: { "text": "...", "lang": "fr|en|..." }
// Response JSON: { ok: bool, data?: { typeReclamation, titre, description }, error?: string }

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = file_get_contents('php://input');
$payload = [];
if (is_string($raw) && trim($raw) !== '') {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $payload = $decoded;
    }
}

$text = trim((string)($payload['text'] ?? ''));
$lang = strtolower(trim((string)($payload['lang'] ?? '')));

if ($text === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing text'], JSON_UNESCAPED_UNICODE);
    exit;
}

$allowedTypes = [
    'problème de commande',
    'produit défectueux',
    'retard de livraison',
    'service client',
    'autre',
];

function ai_fallback_suggest(string $text, array $allowedTypes): array
{
    $t = mb_strtolower($text);

    $type = 'autre';
    if (str_contains($t, 'commande') || str_contains($t, 'paiement') || str_contains($t, 'facture') || str_contains($t, 'annul')) {
        $type = 'problème de commande';
    } elseif (str_contains($t, 'défect') || str_contains($t, 'cass') || str_contains($t, 'panne') || str_contains($t, 'ne marche pas')) {
        $type = 'produit défectueux';
    } elseif (str_contains($t, 'retard') || str_contains($t, 'livraison') || str_contains($t, 'colis') || str_contains($t, 'expédition')) {
        $type = 'retard de livraison';
    } elseif (str_contains($t, 'support') || str_contains($t, 'service client') || str_contains($t, 'répond') || str_contains($t, 'appel')) {
        $type = 'service client';
    }

    if (!in_array($type, $allowedTypes, true)) {
        $type = 'autre';
    }

    $firstLine = trim(preg_split('/\R/u', $text)[0] ?? '');
    $titre = $firstLine;
    if ($titre === '' || mb_strlen($titre) < 5) {
        $titre = match ($type) {
            'problème de commande' => 'Problème avec ma commande',
            'produit défectueux' => 'Produit défectueux',
            'retard de livraison' => 'Retard de livraison',
            'service client' => 'Problème avec le service client',
            default => 'Demande d’assistance',
        };
    }
    if (mb_strlen($titre) > 80) {
        $titre = mb_substr($titre, 0, 80);
    }

    $description = $text;
    if (mb_strlen($description) < 10) {
        $description = $text . "\n\n" . "Merci de traiter ma réclamation.";
    }

    return [
        'typeReclamation' => $type,
        'titre' => $titre,
        'description' => $description,
    ];
}

function extract_json_object(string $text): ?array
{
    $start = strpos($text, '{');
    $end = strrpos($text, '}');
    if ($start === false || $end === false || $end <= $start) {
        return null;
    }

    $candidate = substr($text, $start, $end - $start + 1);
    $decoded = json_decode($candidate, true);
    return is_array($decoded) ? $decoded : null;
}

try {
    // Try to use the already-present AI clients in /projet.
    $projetRoot = realpath(__DIR__ . '/../../projet');
    if ($projetRoot === false) {
        throw new RuntimeException('AI tools not found');
    }

    $configLocal = $projetRoot . '/config.local.php';
    $config = $projetRoot . '/config.php';

    if (file_exists($config)) {
        require_once $config;
    }
    if (file_exists($configLocal)) {
        require_once $configLocal;
    }

    // Force Gemini for this feature (requested).
    $provider = 'gemini';

    $system = "You are a retro-gaming themed assistant (like an NPC / quest helper).\n"
        . "Your job: convert the user's story into a well-written complaint form.\n"
        . "Return ONLY valid JSON (no markdown, no extra text).\n"
        . "Allowed values for typeReclamation are exactly: " . implode(', ', $allowedTypes) . ".\n"
        . "Output format: {\"typeReclamation\": string, \"titre\": string, \"description\": string }.\n"
        . "Rules: title 5-80 chars; description >= 10 chars; keep the user's language.\n"
        . "Keep it clear and actionable (facts, dates if provided, what the user wants).";

    $user = "User language hint: " . ($lang !== '' ? $lang : 'unknown') . "\n\nUser text:\n" . $text;

    $messages = [
        ['role' => 'system', 'content' => $system],
        ['role' => 'user', 'content' => $user],
    ];

    $aiText = '';
    require_once $projetRoot . '/tools/GeminiClient.php';
    // Force model explicitly to gemini-2.5-flash-lite.
    $client = new GeminiClient(null, 'gemini-2.5-flash-lite');
    $aiText = $client->chat($messages, 220, 0.2, 0.9);

    $data = extract_json_object($aiText);
    if (!is_array($data)) {
        // Some models might output extra text; fallback if parsing fails.
        $data = ai_fallback_suggest($text, $allowedTypes);
    }

    $type = (string)($data['typeReclamation'] ?? '');
    $titre = trim((string)($data['titre'] ?? ''));
    $description = trim((string)($data['description'] ?? ''));

    if (!in_array($type, $allowedTypes, true)) {
        $type = 'autre';
    }
    if ($titre === '' || mb_strlen($titre) < 5) {
        $titre = ai_fallback_suggest($text, $allowedTypes)['titre'];
    }
    if (mb_strlen($titre) > 80) {
        $titre = mb_substr($titre, 0, 80);
    }
    if ($description === '' || mb_strlen($description) < 10) {
        $description = ai_fallback_suggest($text, $allowedTypes)['description'];
    }

    echo json_encode([
        'ok' => true,
        'data' => [
            'typeReclamation' => $type,
            'titre' => $titre,
            'description' => $description,
        ],
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    // Always keep the feature usable even without configured keys.
    $fallback = ai_fallback_suggest($text, $allowedTypes);
    echo json_encode([
        'ok' => true,
        'data' => $fallback,
        'meta' => [
            'mode' => 'fallback',
        ],
    ], JSON_UNESCAPED_UNICODE);
}
