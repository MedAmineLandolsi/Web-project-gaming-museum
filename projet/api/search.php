<?php
// api/search.php
// Endpoint de recherche intelligente pour le projet Gaming Museum

header('Content-Type: application/json');

// Récupérer la requête utilisateur
$input = json_decode(file_get_contents('php://input'), true);
$query = isset($input['query']) ? $input['query'] : '';

if (!$query) {
    echo json_encode(['error' => 'Aucune requête fournie']);
    exit;
}

// Logging temporaire pour debug
$logFile = __DIR__ . '/search_debug.log';
file_put_contents($logFile, "\n----\n" . date('c') . " - Incoming query: " . $query . "\n", FILE_APPEND);

// Appel à l'API OpenAI pour extraire les mots-clés (la fonction gère les erreurs)
$keywords = callOpenAI($query, $logFile);

// Charger les communautés depuis la base de données si possible
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Communaute.php';

$data = [];
try {
    $database = new Database();
    $db = $database->connect();
    if ($db) {
        $commModel = new Communaute($db);
        $stmt = $commModel->read();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        file_put_contents($logFile, "Loaded communities from DB: " . count($data) . "\n", FILE_APPEND);
    }
} catch (Exception $e) {
    file_put_contents($logFile, "DB load failed: " . $e->getMessage() . "\n", FILE_APPEND);
}

// Si la base de données est inaccessible ou vide, fallback sur le fichier JSON (si présent)
if (empty($data)) {
    $dataPath = __DIR__ . '/user_community_data.json';
    if (file_exists($dataPath)) {
        $raw = file_get_contents($dataPath);
        $jsonData = json_decode($raw, true);
        if (is_array($jsonData) && !empty($jsonData)) {
            $data = $jsonData;
            file_put_contents($logFile, "Fallback: loaded data from JSON (count=" . count($data) . ")\n", FILE_APPEND);
        } else {
            file_put_contents($logFile, "Fallback JSON invalid or empty\n", FILE_APPEND);
        }
    } else {
        file_put_contents($logFile, "No community data available (DB and JSON missing)\n", FILE_APPEND);
    }
}

$FUZZY_PERCENT_THRESHOLD = 70; // percent similarity threshold for similar_text (raised to be stricter)
$LEVENSHTEIN_THRESHOLD = 2; // max levenshtein distance for short strings

// Log thresholds for easier debugging
file_put_contents($logFile, "Fuzzy thresholds - similar_text%={$FUZZY_PERCENT_THRESHOLD}, levenshtein<= {$LEVENSHTEIN_THRESHOLD}\n", FILE_APPEND);

// Filtrer les données selon les mots-clés (matching partiel sur nom/description/categorie)
$results = [];
$matchedNames = [];
// Helper: match keyword as whole word (unicode-aware) inside text to avoid matching substrings like 'art' in 'partage'
function keyword_in_text($keyword, $text) {
    if ($keyword === '' || $text === '') return false;
    // Use Unicode-aware boundaries: ensure keyword is not inside other letters/numbers
    $pattern = '/(?<=^|[^\p{L}\p{N}])' . preg_quote($keyword, '/') . '(?=$|[^\p{L}\p{N}])/iu';
    return preg_match($pattern, $text) === 1;
}
// Category alias map - map simple user keywords to category name fragments
// Category alias map - map simple user keywords to category name fragments
$categoryAliasMap = [
    'jeux' => ['jeux','jeu','gaming','game','gamer'],
    'art' => ['art','artist','artiste','artistique','illustration','design','drawing','peinture'],
    // Separate photography as its own canonical category so searching 'art' won't match 'photographie'
    'photographie' => ['photographie','photo','photography','photographie'],
    'technologie' => ['technologie','tech','technology','informatique','dev','development','web','framework'],
    'musique' => ['musique','music','musicien','musicians','composer']
];
// Build a flattened list of category alias tokens based on returned keywords
$categoryTokens = [];
foreach ($keywords as $k) {
    $lk = mb_strtolower($k);
    foreach ($categoryAliasMap as $canonical => $aliases) {
        if ($lk === $canonical || in_array($lk, $aliases)) {
            foreach ($aliases as $a) $categoryTokens[] = $a;
            // also include canonical
            $categoryTokens[] = $canonical;
        }
    }
}
$categoryTokens = array_values(array_unique($categoryTokens));
if ($logFile) file_put_contents($logFile, "Category tokens derived from keywords: " . json_encode($categoryTokens) . "\n", FILE_APPEND);
// Determine if the query is a single category keyword => enforce category-only matching
$categoryQueryMode = false;
$canonicalCategoriesMatched = [];
if (count($keywords) === 1) {
    $k0 = mb_strtolower(trim($keywords[0]));
    foreach ($categoryAliasMap as $canonical => $aliases) {
        if ($k0 === $canonical || in_array($k0, $aliases)) {
            $categoryQueryMode = true;
            $canonicalCategoriesMatched[] = $canonical;
            // remember the original keyword to match exact category text in category-only mode
            $categoryQueryKeyword = $k0;
            break;
        }
    }
}
if ($logFile) file_put_contents($logFile, "Category query mode=" . ($categoryQueryMode ? 'yes' : 'no') . " canonical=" . json_encode($canonicalCategoriesMatched) . "\n", FILE_APPEND);
foreach ($data as $item) {
    $haystackName = isset($item['nom']) ? mb_strtolower($item['nom']) : '';
    $haystackDesc = isset($item['description']) ? mb_strtolower($item['description']) : '';
    $haystackCat = isset($item['categorie']) ? mb_strtolower($item['categorie']) : '';

    $matched = false;
    // If category-only mode is active, only match by category tokens
    if ($categoryQueryMode) {
        // In category-only mode, match the exact keyword against the category (whole-word)
        $ck = isset($categoryQueryKeyword) ? $categoryQueryKeyword : null;
        if ($ck !== null && $haystackCat !== '' && keyword_in_text($ck, $haystackCat)) {
            $matched = true;
        }
        if ($matched) {
            $results[] = $item;
            $matchedNames[] = isset($item['nom']) ? $item['nom'] : json_encode($item);
        }
        // move to next item
        continue;
    }
    foreach ($keywords as $kw) {
        $kw = mb_strtolower($kw);
        if ($kw === '') continue;

        // Use whole-word matching to avoid substring matches (e.g., 'art' in 'partage')
        if ($haystackName !== '' && keyword_in_text($kw, $haystackName)) { $matched = true; break; }
        if ($haystackDesc !== '' && keyword_in_text($kw, $haystackDesc)) { $matched = true; break; }
        if ($haystackCat !== '' && keyword_in_text($kw, $haystackCat)) { $matched = true; break; }

        // Fallback: check entire item JSON using whole-word matching
        $jsonHay = mb_strtolower(json_encode($item));
        if (keyword_in_text($kw, $jsonHay)) { $matched = true; break; }
    }

    // If not matched by direct keyword in fields, but category tokens include this item's category, match it
    if (!$matched && !empty($categoryTokens)) {
        foreach ($categoryTokens as $ct) {
            if ($ct === '') continue;
            if ($haystackCat !== '' && mb_stripos($haystackCat, $ct) !== false) { $matched = true; break; }
        }
        if ($matched && $logFile) file_put_contents($logFile, "Matched by category token for item: " . ($item['nom'] ?? json_encode($item)) . "\n", FILE_APPEND);
    }

    // If not matched by direct keywords, try fuzzy matching against name/description
    if (!$matched) {
        $searchString = trim(implode(' ', $keywords));
        $searchString = mb_strtolower($searchString);
        if ($searchString !== '') {
                // Decide whether to allow fuzzy matching based on query length (ignore fuzzy for very short queries)
                $compactSearch = preg_replace('/\s+/u', '', $searchString);
                $searchLen = mb_strlen($compactSearch);
                $allowFuzzy = $searchLen >= 4; // require at least 4 characters (excluding spaces) to enable fuzzy matching
                if ($logFile) file_put_contents($logFile, "Search string compact='" . $compactSearch . "' len={$searchLen} allowFuzzy=" . ($allowFuzzy ? 'yes' : 'no') . "\n", FILE_APPEND);

                if ($allowFuzzy) {
                    // similar_text gives percentage similarity
                    $percentName = 0; $percentDesc = 0; $percentCat = 0;
                    similar_text($searchString, $haystackName, $percentName);
                    similar_text($searchString, $haystackDesc, $percentDesc);
                    similar_text($searchString, $haystackCat, $percentCat);

                    if ($percentName >= $FUZZY_PERCENT_THRESHOLD || $percentDesc >= $FUZZY_PERCENT_THRESHOLD || $percentCat >= $FUZZY_PERCENT_THRESHOLD) {
                        $matched = true;
                    } else {
                        // levenshtein for short comparisons
                        $levName = levenshtein($searchString, $haystackName);
                        $levDesc = levenshtein($searchString, $haystackDesc);
                        if (($levName !== false && $levName <= $LEVENSHTEIN_THRESHOLD) || ($levDesc !== false && $levDesc <= $LEVENSHTEIN_THRESHOLD)) {
                            $matched = true;
                        }
                    }
                } else {
                    // Fuzzy disabled for short queries; do nothing (no match)
                }
        }
    }

    if ($matched) {
        $results[] = $item;
        $matchedNames[] = isset($item['nom']) ? $item['nom'] : json_encode($item);
    }
}

// Retourner les résultats et logger
file_put_contents($logFile, "Results count: " . count($results) . "\n", FILE_APPEND);
file_put_contents($logFile, "Matched items: " . json_encode($matchedNames) . "\n", FILE_APPEND);

// Préparer la réponse (structure uniforme)
$responseOut = [
    'results' => array_values($results),
    'Count' => count($results)
];

// Mode debug si demandé via GET ?debug=1 ou dans le body {debug:1}
$debugMode = (isset($_GET['debug']) && $_GET['debug'] == '1') || (isset($input['debug']) && $input['debug'] == 1);
if ($debugMode) {
    $responseOut['debug'] = [
        'keywords' => $keywords,
        'used_openai' => !empty(getenv('OPENAI_API_KEY')),
        'matched_items' => $matchedNames
    ];
}

echo json_encode($responseOut);
exit;


// Fonction d'appel OpenAI
function callOpenAI($query, $logFile = null) {
    // NOTE: read API key from environment variable (safer than hardcoding)
    $apiKey = getenv('OPENAI_API_KEY') ?: '';

    // Si la clé est vide, on utilise le fallback local
    if (empty($apiKey)) {
        if ($logFile) file_put_contents($logFile, "OPENAI_API_KEY not set — using fallback keywords.\n", FILE_APPEND);
        $parts = preg_split('/[\s,]+/', $query, -1, PREG_SPLIT_NO_EMPTY);
        $clean = array_map(function($s){ return mb_strtolower(trim(preg_replace('/[^\p{L}\p{N}]/u','',$s))); }, $parts);
        $clean = array_values(array_filter($clean));
        return $clean ?: [$query];
    }

    $url = "https://api.openai.com/v1/chat/completions";
    $payload = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "system", "content" => "Extrais les mots-clés principaux de la requête utilisateur pour une recherche de communauté gaming."],
            ["role" => "user", "content" => $query]
        ],
        "max_tokens" => 60,
        "temperature" => 0.0
    ];

    // Use cURL for better error handling
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $result = curl_exec($ch);
    $curlErr = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($logFile) {
        file_put_contents($logFile, "OpenAI http_code: " . $httpCode . "\n", FILE_APPEND);
        file_put_contents($logFile, "OpenAI raw response: " . ($result === false ? 'FALSE' : $result) . "\n", FILE_APPEND);
        if ($curlErr) file_put_contents($logFile, "OpenAI curl error: " . $curlErr . "\n", FILE_APPEND);
    }

    if ($result === false || $httpCode < 200 || $httpCode >= 300) {
        if ($logFile) file_put_contents($logFile, "OpenAI request failed or returned non-2xx. Using fallback keywords.\n", FILE_APPEND);
        $parts = preg_split('/[\s,]+/', $query, -1, PREG_SPLIT_NO_EMPTY);
        $clean = array_map(function($s){ return mb_strtolower(trim(preg_replace('/[^\p{L}\p{N}]/u','',$s))); }, $parts);
        $clean = array_values(array_filter($clean));
        return $clean ?: [$query];
    }

    $response = json_decode($result, true);
    if (!isset($response['choices'][0]['message']['content'])) {
        if ($logFile) file_put_contents($logFile, "OpenAI response missing content, using fallback.\n", FILE_APPEND);
        $parts = preg_split('/[\s,]+/', $query, -1, PREG_SPLIT_NO_EMPTY);
        $clean = array_map(function($s){ return mb_strtolower(trim(preg_replace('/[^\p{L}\p{N}]/u','',$s))); }, $parts);
        $clean = array_values(array_filter($clean));
        return $clean ?: [$query];
    }

    $keywords = $response['choices'][0]['message']['content'];
    // Nettoyage basique : enlever ponctuation non utile
    $keywords = preg_replace('/[^\p{L}\p{N},\s]/u', '', $keywords);
    $parts = preg_split('/[,\s]+/', $keywords, -1, PREG_SPLIT_NO_EMPTY);
    $clean = array_map(function($s){ return mb_strtolower(trim($s)); }, $parts);
    return array_values(array_unique($clean));
}
