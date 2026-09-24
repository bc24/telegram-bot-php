<?php
declare(strict_types=1);

require __DIR__ . '/lib/Env.php';
require __DIR__ . '/lib/Telegram.php';
require __DIR__ . '/lib/Ruhezeit.php';

Env::load(__DIR__ . '/.env');
date_default_timezone_set(Env::get('TIMEZONE', 'Europe/Berlin'));

$telegram = new Telegram(Env::get('BOT_TOKEN'));
