<?php

declare(strict_types=1);

function env_value(string $key, ?string $default = null): ?string
{
    static $loaded = false;
    static $vars = [];

    if (!$loaded) {
        $loaded = true;
        $envPath = dirname(__DIR__, 2) . '/.env';
        if (is_file($envPath)) {
            $parsed = parse_ini_file($envPath, false, INI_SCANNER_TYPED);
            if (is_array($parsed)) {
                $vars = $parsed;
            }
        }
    }

    if (array_key_exists($key, $vars)) {
        return (string) $vars[$key];
    }

    $value = getenv($key);
    return $value !== false ? (string) $value : $default;
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = env_value('DB_HOST', '127.0.0.1');
    $port = env_value('DB_PORT', '3306');
    $name = env_value('DB_NAME', 'vendermeuimovel');
    $user = env_value('DB_USER', 'root');
    $pass = env_value('DB_PASS', '');

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
