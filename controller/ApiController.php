<?php

require_once dirname(__DIR__) . '/tools/OpenAIClient.php';
require_once dirname(__DIR__) . '/tools/GeminiClient.php';

final class ApiController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function search(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $q = trim((string) ($_GET['q'] ?? ''));
        $context = (string) ($_GET['context'] ?? 'front');
        $context = ($context === 'admin') ? 'admin' : 'front';
        if ($context === 'admin' && empty($_SESSION['is_admin'])) {
            $context = 'front';
        }

        if ($q === '' || mb_strlen($q) < 2) {
            echo json_encode(['results' => []], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $like = '%' . $q . '%';
        $limitEach = 5;

        $results = [];

        // Communities
        $stmt = $this->db->prepare(
            "SELECT id, nom, categorie
             FROM communaute
             WHERE nom LIKE :like OR categorie LIKE :like
             ORDER BY (nom LIKE :prefix) DESC, nom ASC
             LIMIT {$limitEach}"
        );
        $prefix = $q . '%';
        $stmt->bindValue(':like', $like, PDO::PARAM_STR);
        $stmt->bindValue(':prefix', $prefix, PDO::PARAM_STR);
        $stmt->execute();
        $communautes = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        foreach ($communautes as $row) {
            $id = (int) ($row['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $title = (string) ($row['nom'] ?? 'Communauté');
            $subtitle = (string) ($row['categorie'] ?? '');
            $url = $this->buildUrl($context, 'communaute', $id);

            $results[] = [
                'type' => 'communaute',
                'id' => $id,
                'title' => $title,
                'subtitle' => $subtitle,
                'url' => $url,
            ];
        }

        // Publications
        $stmt = $this->db->prepare(
            "SELECT p.id, p.contenu, c.nom AS communaute_nom
             FROM publication p
             LEFT JOIN communaute c ON c.id = p.communaute_id
             WHERE p.contenu LIKE :like
             ORDER BY p.date_publication DESC
             LIMIT {$limitEach}"
        );
        $stmt->bindValue(':like', $like, PDO::PARAM_STR);
        $stmt->execute();
        $publications = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        foreach ($publications as $row) {
            $id = (int) ($row['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $content = trim((string) ($row['contenu'] ?? ''));
            $content = preg_replace('/\s+/u', ' ', $content) ?? $content;
            if (mb_strlen($content) > 80) {
                $content = rtrim(mb_substr($content, 0, 77)) . '...';
            }

            $communityName = (string) ($row['communaute_nom'] ?? '');
            $subtitle = $communityName !== '' ? ('Dans: ' . $communityName) : '';
            $url = $this->buildUrl($context, 'publication', $id);

            $results[] = [
                'type' => 'publication',
                'id' => $id,
                'title' => $content !== '' ? $content : ('Publication #' . $id),
                'subtitle' => $subtitle,
                'url' => $url,
            ];
        }

        // Hard cap to keep UI simple
        $results = array_slice($results, 0, 10);

        echo json_encode(['results' => $results], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function chat(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $raw = file_get_contents('php://input');
        $payload = json_decode($raw ?: '', true);
        if (!is_array($payload)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON body'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $userMessage = trim((string) ($payload['message'] ?? ''));
        $messages = $payload['messages'] ?? null;

        $chatMessages = [];

        $system = "Tu es l'assistant d'aide du site Communauté (projet). "
            . "Réponds en français, de façon concise et pratique. "
            . "Tu aides à utiliser le site (communautés, publications, navigation, dashboard admin). "
            . "Si on te demande des infos privées ou des données personnelles, refuse poliment. "
            . "Si une question est ambiguë, pose 1 question de clarification.";

        $chatMessages[] = ['role' => 'system', 'content' => $system];

        if (is_array($messages)) {
            foreach ($messages as $m) {
                if (!is_array($m)) {
                    continue;
                }
                $role = (string) ($m['role'] ?? '');
                $content = trim((string) ($m['content'] ?? ''));
                if ($content === '') {
                    continue;
                }
                if (!in_array($role, ['user', 'assistant'], true)) {
                    continue;
                }
                $chatMessages[] = ['role' => $role, 'content' => $content];
            }
        } elseif ($userMessage !== '') {
            $chatMessages[] = ['role' => 'user', 'content' => $userMessage];
        }

        // Keep last N messages to control cost/latency
        $maxHistory = 14;
        if (count($chatMessages) > ($maxHistory + 1)) {
            $chatMessages = array_merge([
                $chatMessages[0],
            ], array_slice($chatMessages, -$maxHistory));
        }

        // Basic guardrails
        $lastUser = '';
        for ($i = count($chatMessages) - 1; $i >= 0; $i--) {
            if ($chatMessages[$i]['role'] === 'user') {
                $lastUser = $chatMessages[$i]['content'];
                break;
            }
        }
        if ($lastUser === '' || mb_strlen($lastUser) < 2) {
            http_response_code(400);
            echo json_encode(['error' => 'Message vide'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (mb_strlen($lastUser) > 1500) {
            http_response_code(413);
            echo json_encode(['error' => 'Message trop long'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            $provider = '';
            if (defined('AI_PROVIDER')) {
                $provider = strtolower(trim((string) AI_PROVIDER));
            } else {
                $provider = strtolower(trim((string) getenv('AI_PROVIDER')));
            }

            $hasGeminiKey = (defined('GEMINI_API_KEY') && trim((string) GEMINI_API_KEY) !== '')
                || (trim((string) getenv('GEMINI_API_KEY')) !== '');

            if ($provider === '') {
                // Auto-pick Gemini when available, else OpenAI
                $provider = $hasGeminiKey ? 'gemini' : 'openai';
            }

            if ($provider === 'gemini') {
                $client = new GeminiClient();
                $answer = $client->chat($chatMessages);
            } else {
                $client = new OpenAIClient();
                $answer = $client->chat($chatMessages);
            }

            echo json_encode([
                'answer' => $answer,
            ], JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Throwable $e) {
            $msg = $e->getMessage();
            $status = 500;
            if (is_string($msg)) {
                $m = mb_strtolower($msg);
                if (str_contains($m, 'rejected the api key') || str_contains($m, 'incorrect api key')) {
                    $status = 401;
                } elseif (str_contains($m, 'gemini rejected the api key')) {
                    $status = 401;
                } elseif (str_contains($m, 'quota') || str_contains($m, 'billing') || str_contains($m, 'insufficient')) {
                    $status = 402;
                } elseif (str_contains($m, 'rate limit')) {
                    $status = 429;
                } elseif (str_contains($m, 'gemini quota') || str_contains($m, 'gemini quota/rate')) {
                    $status = 429;
                }
            }
            http_response_code($status);
            echo json_encode([
                'error' => $msg,
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    private function buildUrl(string $context, string $type, int $id): string
    {
        $base = defined('BASE_URL') ? (string) BASE_URL : '';

        if ($context === 'admin') {
            if ($type === 'communaute') {
                return $base . '/admin/communautes/' . $id;
            }
            return $base . '/admin/publications/' . $id;
        }

        if ($type === 'communaute') {
            return $base . '/communautes/' . $id;
        }
        return $base . '/publications/' . $id;
    }
}
