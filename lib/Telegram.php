<?php
declare(strict_types=1);

/**
 * Schlanker Client für die Telegram Bot API (nur cURL, keine Abhängigkeiten).
 */
final class Telegram
{
    private string $apiUrl;

    public function __construct(string $token)
    {
        $this->apiUrl = 'https://api.telegram.org/bot' . $token . '/';
    }

    /**
     * Ruft eine beliebige Bot-API-Methode auf.
     *
     * @param array<string,mixed> $params
     * @return array<string,mixed>
     */
    public function call(string $method, array $params = []): array
    {
        $ch = curl_init($this->apiUrl . $method);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($params, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
        ]);
        $raw = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new RuntimeException('Telegram nicht erreichbar: ' . $error);
        }
        $result = json_decode((string) $raw, true);
        if (!is_array($result) || empty($result['ok'])) {
            $desc = is_array($result) ? ($result['description'] ?? 'unbekannter Fehler') : 'ungültige Antwort';
            throw new RuntimeException("Telegram-Fehler bei {$method}: {$desc}");
        }
        return $result;
    }

    /** Sendet eine Textnachricht (HTML-Formatierung erlaubt). */
    public function sendMessage(string|int $chatId, string $text, array $extra = []): array
    {
        return $this->call('sendMessage', array_merge([
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'HTML',
            'link_preview_options' => ['is_disabled' => true],
        ], $extra));
    }
}
