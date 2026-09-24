<?php
declare(strict_types=1);

/**
 * Prüft, ob gerade Ruhezeit ist – z. B. 22:00 bis 07:00 Uhr.
 * Unterstützt Zeiträume über Mitternacht.
 */
final class Ruhezeit
{
    public static function aktiv(string $von, string $bis, ?DateTimeImmutable $jetzt = null): bool
    {
        $jetzt ??= new DateTimeImmutable('now');
        $uhrzeit = $jetzt->format('H:i');

        if ($von === $bis) {
            return false; // keine Ruhezeit konfiguriert
        }
        if ($von < $bis) {
            return $uhrzeit >= $von && $uhrzeit < $bis;   // z. B. 12:00–14:00
        }
        return $uhrzeit >= $von || $uhrzeit < $bis;       // z. B. 22:00–07:00
    }
}
