<?php

final class GeminiClient
{
    private string $apiKey;
    private string $model;

    public function __construct(?string $apiKey = null, ?string $model = null)
    {
        $apiKey = $apiKey
            ?? (defined('GEMINI_API_KEY') ? (string) GEMINI_API_KEY : null)
            ?? (string) getenv('GEMINI_API_KEY');
        $apiKey = trim($apiKey);
        if ($apiKey === '') {
            throw new RuntimeException('GEMINI_API_KEY is not configured. Create projet/config.local.php from config.local.php.example and set your real key.');
        }
        if (preg_match('/\s/', $apiKey) === 1) {
            throw new RuntimeException('GEMINI_API_KEY looks invalid (contains spaces/newlines). Paste the key exactly as provided.');
        }

        $this->apiKey = $apiKey;

        $this->model = trim((string) (
            $model
            ?? (defined('GEMINI_MODEL') ? (string) GEMINI_MODEL : null)
            ?? getenv('GEMINI_MODEL')
        ));
        if ($this->model === '') {
            $this->model = 'gemini-2.5-flash-lite';
        }
    }

    /**
     * @param array<int, array{role:string, content:string}> $messages (system/user/assistant)
     */
    public function chat(array $messages, int $maxTokens = 450, float $temperature = 0.2, ?float $topP = 0.9): string
    {
        $systemText = '';
        if (isset($messages[0]) && is_array($messages[0]) && ($messages[0]['role'] ?? '') === 'system') {
            $systemText = trim((string) ($messages[0]['content'] ?? ''));
        }

        $contents = [];
        foreach ($messages as $m) {
            if (!is_array($m)) {
                continue;
            }
            $role = (string) ($m['role'] ?? '');
            $content = trim((string) ($m['content'] ?? ''));
            if ($content === '' || $role === 'system') {
                continue;
            }

            $geminiRole = null;
            if ($role === 'user') {
                $geminiRole = 'user';
            } elseif ($role === 'assistant') {
                $geminiRole = 'model';
            }
            if ($geminiRole === null) {
                continue;
            }

            $contents[] = [
                'role' => $geminiRole,
                'parts' => [
                    ['text' => $content],
                ],
            ];
        }

        if (count($contents) === 0) {
            throw new RuntimeException('No user message to send to Gemini');
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $temperature,
                'maxOutputTokens' => $maxTokens,
                'topP' => $topP,
                'topK' => 40,
            ],
        ];

        // v1beta accepts system_instruction; if the API rejects it, the error will be surfaced.
        if ($systemText !== '') {
            $payload['system_instruction'] = [
                'parts' => [
                    ['text' => $systemText],
                ],
            ];
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/'
            . rawurlencode($this->model)
            . ':generateContent?key='
            . rawurlencode($this->apiKey);

        $res = $this->postJson($url, $payload);

        $parts = $res['candidates'][0]['content']['parts'] ?? null;
        if (!is_array($parts)) {
            throw new RuntimeException('Empty AI response');
        }

        $texts = [];
        foreach ($parts as $p) {
            if (is_array($p) && isset($p['text']) && is_string($p['text'])) {
                $t = trim($p['text']);
                if ($t !== '') {
                    $texts[] = $t;
                }
            }
        }

        $text = trim(implode("\n", $texts));
        if ($text === '') {
            throw new RuntimeException('Empty AI response');
        }

        return $text;
    }

    /**
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    private function postJson(string $url, array $payload): array
    {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new RuntimeException('Failed to init cURL');
        }

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new RuntimeException('Failed to encode JSON payload');
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $raw = curl_exec($ch);
        $errno = curl_errno($ch);
        $err = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false) {
            throw new RuntimeException('cURL error (' . $errno . '): ' . $err);
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            throw new RuntimeException('Non-JSON response from Gemini (HTTP ' . $status . ')');
        }

        if ($status < 200 || $status >= 300) {
            $msg = $data['error']['message'] ?? ('Gemini error HTTP ' . $status);
            if (!is_string($msg)) {
                $msg = 'Gemini error HTTP ' . $status;
            }

            // Normalize common errors
            $m = mb_strtolower($msg);
            if ($status === 401 || str_contains($m, 'api key') || str_contains($m, 'permission')) {
                throw new RuntimeException('Gemini rejected the API key or permissions. Check GEMINI_API_KEY and that the Generative Language API is enabled.');
            }
            if ($status === 429 || str_contains($m, 'quota') || str_contains($m, 'rate')) {
                throw new RuntimeException('Gemini quota/rate limit reached. Check quota/billing in Google and retry.');
            }

            throw new RuntimeException($msg);
        }

        return $data;
    }
}
