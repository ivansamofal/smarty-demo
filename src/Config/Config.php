<?php

declare(strict_types=1);

namespace App\Config;

use App\Exception\ConfigurationException;

final class Config
{
    /** @var array<string, string> */
    private static array $values = [];

    private static bool $loaded = false;

    public static function get(string $key, ?string $default = null): ?string
    {
        self::load();

        return self::$values[$key] ?? $default;
    }

    public static function require(string $key): string
    {
        self::load();

        $value = self::$values[$key] ?? null;

        if ($value === null || $value === '') {
            throw ConfigurationException::missingKey($key);
        }

        return $value;
    }

    private static function load(): void
    {
        if (self::$loaded) {
            return;
        }

        foreach ($_SERVER as $key => $value) {
            if (is_string($key) && is_string($value)) {
                self::$values[$key] = $value;
            }
        }

        $envFile = dirname(__DIR__, 2) . '/.env';

        if (is_file($envFile)) {
            self::loadEnvFile($envFile);
        }

        self::$loaded = true;
    }

    private static function loadEnvFile(string $path): void
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            throw ConfigurationException::unreadableFile($path);
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");

            if ($key !== '' && !array_key_exists($key, self::$values)) {
                self::$values[$key] = $value;
            }
        }
    }
}
