<?php

namespace App\Core;

class Env
{
    public static function load(string $filePath): void
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            if ($trimmedLine === '' || str_starts_with($trimmedLine, '#')) {
                continue;
            }

            $separatorPosition = strpos($trimmedLine, '=');

            if ($separatorPosition === false) {
                continue;
            }

            $key = trim(substr($trimmedLine, 0, $separatorPosition));
            $value = trim(substr($trimmedLine, $separatorPosition + 1));
            $value = trim($value, "\"'");

            if ($key === '') {
                continue;
            }

            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === null || $value === '') {
            return $default;
        }

        return $value;
    }
}
