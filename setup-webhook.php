<?php
declare(strict_types=1);

/**
 * Registriert den Webhook bei Telegram. Einmalig auf der Kommandozeile ausführen:
 *   php setup-webhook.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/bootstrap.php';

$url = Env::get('WEBHOOK_URL');

$telegram->call('setWebhook', [
    'url'             => $url,
    'secret_token'    => Env::get('WEBHOOK_SECRET'),
    'allowed_updates' => ['message'],
    'drop_pending_updates' => true,
]);

$telegram->call('setMyCommands', ['commands' => [
    ['command' => 'start', 'description' => 'Begrüßung'],
    ['command' => 'hilfe', 'description' => 'Alle Befehle'],
    ['command' => 'zeit', 'description' => 'Aktuelle Uhrzeit'],
    ['command' => 'ping', 'description' => 'Erreichbarkeit prüfen'],
    ['command' => 'id', 'description' => 'Chat-ID anzeigen'],
]]);

echo "Webhook gesetzt: {$url}\n";
