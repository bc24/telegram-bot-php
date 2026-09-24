<?php
declare(strict_types=1);

/**
 * Minimaler .env-Loader ohne Composer.
 * Liest KEY=VALUE-Zeilen, ignoriert Kommentare (#) und entfernt Anführungszeichen.
 */
final class Env
{
    /** @var array<string,string> */
    private static array $values = [];

    public static function load(string $file): void
    {
        if (!is_readable($file)) {
            throw new RuntimeException("Konfigurationsdatei nicht gefunden: {$file} (Vorlage: .env.example)");
        }
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = array_map('trim', explode('=', $line, 2));
            if (strlen($value) >= 2 && ($value[0] === '"' || $value[0] === "'") && $value[-1] === $value[0]) {
                $value = substr($value, 1, -1);
            }
            self::$values[$key] = $value;
        }
    }

    public static function get(string $key, ?string $default = null): string
    {
        $value = self::$values[$key] ?? getenv($key);
        if ($value === false || $value === null || $value === '') {
            if ($default === null) {
                throw new RuntimeException("Pflichtwert fehlt in .env: {$key}");
            }
            return $default;
        }
        return (string) $value;
    }
}
