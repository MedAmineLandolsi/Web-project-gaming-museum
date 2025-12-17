<?php

final class OpenAIClient
{
    private string $apiKey;
    private string $model;

    public function __construct(?string $apiKey = null, ?string $model = null)
    {
        $apiKey = $apiKey
            ?? (defined('OPENAI_API_KEY') ? (string) OPENAI_API_KEY : null)
            ?? (string) getenv('OPENAI_API_KEY');
        $apiKey = trim($apiKey);
        if ($apiKey === '') {
            throw new RuntimeException('OPENAI_API_KEY is not configured. Create projet/config.local.php from config.local.php.example and set your real key.');
        }
        if (preg_match('/\s/', $apiKey) === 1) {
            throw new RuntimeException('OPENAI_API_KEY looks invalid (contains spaces/newlines). Paste the key exactly as provided by OpenAI.');
        }
        if (stripos($apiKey, '_ici') !== false || stripos($apiKey, 'your-real-openai-api-key-here') !== false) {
            throw new RuntimeException('OPENAI_API_KEY is a placeholder ("_ici"). Replace it with your real OpenAI API key in projet/config.local.php.');
        }
        if (strpos($apiKey, 'sk-') !== 0) {
            throw new RuntimeException('OPENAI_API_KEY looks invalid (expected to start with "sk-").');
        }

        $this->apiKey = $apiKey;
        $this->model = trim((string) (
            $model
            ?? (defined('OPENAI_MODEL') ? (string) OPENAI_MODEL : null)
            ?? getenv('OPENAI_MODEL')
        ));
        if ($this->model === '') {
            // Keep configurable; choose a sensible default.
            $this->model = 'gpt-4o-mini';
        }
    }

    /**
     * @param array<int, array{role:string, content:string}> $messages
     */
    public function chat(array $messages, int $maxTokens = 450, float $temperature = 0.2): string
    {
        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
        ];

        $res = $this->postJson('https://api.openai.com/v1/chat/completions', $payload);

        $text = $res['choices'][0]['message']['content'] ?? '';
        $text = is_string($text) ? trim($text) : '';
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
                'Authorization: Bearer ' . $this->apiKey,
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
            throw new RuntimeException('Non-JSON response from OpenAI (HTTP ' . $status . ')');
        }

        if ($status < 200 || $status >= 300) {
            $msg = $data['error']['message'] ?? ('OpenAI error HTTP ' . $status);
            if (!is_string($msg)) {
                $msg = 'OpenAI error HTTP ' . $status;
            }

            // Normalize common OpenAI errors into actionable messages
            $errorCode = $data['error']['code'] ?? null;
            if (!is_string($errorCode)) {
                $errorCode = '';
            }

            if ($status === 401 && stripos($msg, 'Incorrect API key') !== false) {
                throw new RuntimeException(
                    'OpenAI rejected the API key. Set a valid OPENAI_API_KEY in projet/config.local.php (no spaces/newlines, no "_ici" placeholder), then retry.'
                );
            }

            if ($status === 429 && ($errorCode === 'insufficient_quota' || stripos($msg, 'exceeded your current quota') !== false)) {
                throw new RuntimeException(
                    'OpenAI quota/billing issue: your account has no available credits or billing is not enabled. Check your plan/billing on platform.openai.com, then retry.'
                );
            }

            if ($status === 429 && (stripos($msg, 'rate limit') !== false || $errorCode === 'rate_limit_exceeded')) {
                throw new RuntimeException(
                    'OpenAI rate limit reached. Wait a bit and retry, or reduce message frequency.'
                );
            }
            throw new RuntimeException($msg);
        }

        return $data;
    }
}
