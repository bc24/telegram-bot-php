<?php
declare(strict_types=1);

/**
 * Geplante Nachricht per Cronjob, z. B. täglich um 7 Uhr:
 *   0 7 * * * php /pfad/zum/bot/cron/guten-morgen.php
 *
 * Sendet nichts während der Ruhezeit aus der .env.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/bootstrap.php';

if (Ruhezeit::aktiv(Env::get('RUHEZEIT_VON', '22:00'), Env::get('RUHEZEIT_BIS', '07:00'))) {
    echo "Ruhezeit – keine Nachricht gesendet.\n";
    exit;
}

$gruesse = [
    'Guten Morgen! ☀️ Starte gut in den Tag.',
    'Moin moin! ☕ Einen schönen Tag dir.',
    'Guten Morgen! 🚀 Heute wird ein guter Tag.',
];

$wochentage = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
$text = $gruesse[array_rand($gruesse)] . "\n\nHeute ist " . $wochentage[(int) date('w')] . ', der ' . date('d.m.Y') . '.';

$telegram->sendMessage(Env::get('CHAT_ID'), $text);
echo "Gesendet.\n";
