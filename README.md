# Telegram-Bot-Vorlage in PHP

Schlanke Vorlage für einen eigenen Telegram-Bot, der auf jedem normalen PHP-Webhosting läuft. Du brauchst weder Composer noch ein Framework oder einen Node-Server, nur PHP mit cURL.

**Enthalten**

- Webhook-Endpunkt `bot.php` mit Befehlen `/start`, `/hilfe`, `/zeit`, `/ping`, `/id`
- Absicherung über das Telegram-Secret-Token: fremde Aufrufe bekommen 403
- Geplante Nachrichten per Cronjob (`cron/guten-morgen.php`) mit **Ruhezeiten**, z. B. 22–7 Uhr
- Konfiguration über `.env`, die Zugangsdaten liegen also nicht im Code
- `.htaccess`, die unter Apache alles außer `bot.php` sperrt

## Voraussetzungen

- PHP 8.1+ mit cURL
- HTTPS-Domain (Telegram verlangt HTTPS für Webhooks)
- Einen Bot-Token von [@BotFather](https://t.me/BotFather)

## Einrichtung

```bash
git clone https://github.com/bc24/telegram-bot-php.git
cd telegram-bot-php
cp .env.example .env        # Werte eintragen
php setup-webhook.php       # Webhook + Befehlsmenü bei Telegram registrieren
```

Danach dem Bot in Telegram `/start` schreiben. Mit `/id` bekommst du die Chat-ID für `CHAT_ID` in der `.env`.

**Geplante Nachricht** per Cronjob, z. B. in Plesk unter „Geplante Aufgaben“:

```
0 7 * * * php /pfad/zum/bot/cron/guten-morgen.php
```

## Aufbau

```
bot.php               Webhook – verarbeitet eingehende Nachrichten
setup-webhook.php     einmalige Einrichtung (nur CLI)
bootstrap.php         lädt .env und Klassen
cron/guten-morgen.php Beispiel für geplante Nachrichten (nur CLI)
lib/Env.php           .env-Loader
lib/Telegram.php      API-Client (sendMessage + beliebige Methoden über call())
lib/Ruhezeit.php      Ruhezeit-Prüfung, auch über Mitternacht
```

## Eigene Befehle hinzufügen

In `bot.php` einen weiteren Zweig im `match` ergänzen:

```php
'oeffnungszeiten' => "Mo–Fr 8–17 Uhr, Sa nach Vereinbarung.",
```

und in `setup-webhook.php` den Befehl für das Menü eintragen.

## Sicherheit

- `.env` ist in `.gitignore` eingetragen und wird nicht committet. Den Bot-Token bitte niemals veröffentlichen.
- Auf Nginx musst du die Regeln aus der `.htaccess` selbst nachbauen oder den Ordner außerhalb des Webroots ablegen und nur `bot.php` freigeben.

## Lizenz

MIT – siehe [LICENSE](LICENSE).

---

Von [Frank Panzer – Panzer IT](https://panzerit.de), Webentwicklung aus Bremen.
