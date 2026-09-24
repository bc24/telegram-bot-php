<?php
declare(strict_types=1);

/**
 * Webhook-Endpunkt: Telegram schickt jede neue Nachricht per POST hierher.
 * Einrichtung: siehe README (php setup-webhook.php).
 */

require __DIR__ . '/bootstrap.php';

// Nur Anfragen von Telegram annehmen (Secret wird beim setWebhook mitgegeben).
$secret = Env::get('WEBHOOK_SECRET');
if (!hash_equals($secret, $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? '')) {
    http_response_code(403);
    exit;
}

$update = json_decode((string) file_get_contents('php://input'), true);
$message = $update['message'] ?? null;
if (!is_array($message) || !isset($message['text'], $message['chat']['id'])) {
    exit; // andere Update-Typen ignorieren
}

$chatId = $message['chat']['id'];
$text = trim((string) $message['text']);
$vorname = htmlspecialchars($message['from']['first_name'] ?? 'du', ENT_QUOTES);

// "/befehl@BotName argumente" -> "befehl"
$befehl = strtolower(ltrim(explode('@', explode(' ', $text)[0])[0], '/'));

$antwort = match ($befehl) {
    'start' => "Hallo {$vorname}! 👋\nIch bin ein Bot auf Basis der PHP-Vorlage von Panzer IT.\nMit /hilfe siehst du alle Befehle.",
    'hilfe', 'help' => implode("\n", [
        '<b>Befehle</b>',
        '/start – Begrüßung',
        '/hilfe – diese Übersicht',
        '/zeit – aktuelle Uhrzeit',
        '/ping – ist der Bot erreichbar?',
        '/id – Chat-ID anzeigen (für die .env)',
    ]),
    'zeit' => '🕒 Es ist ' . date('H:i') . ' Uhr am ' . date('d.m.Y') . '.',
    'ping' => '🏓 Pong!',
    'id' => 'Die ID dieses Chats: <code>' . $chatId . '</code>',
    default => str_starts_with($text, '/') ? 'Den Befehl kenne ich nicht. Tipp: /hilfe' : null,
};

if ($antwort !== null) {
    try {
        $telegram->sendMessage($chatId, $antwort);
    } catch (RuntimeException $e) {
        error_log($e->getMessage());
    }
}

// Telegram erwartet immer 200, sonst wird das Update wiederholt.
http_response_code(200);
