<?php

declare(strict_types=1);

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('post_string')) {
    function post_string(string $key, string $default = ''): string
    {
        return trim((string) ($_POST[$key] ?? $default));
    }
}

if (!function_exists('post_bool')) {
    function post_bool(string $key): int
    {
        return isset($_POST[$key]) ? 1 : 0;
    }
}

if (!function_exists('post_int')) {
    function post_int(string $key, int $default = 0): int
    {
        return (int) ($_POST[$key] ?? $default);
    }
}
