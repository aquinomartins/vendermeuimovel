<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/db.php';

function ensure_session_started(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function current_user(): ?array
{
    ensure_session_started();
    return $_SESSION['auth_user'] ?? null;
}

function attempt_login(string $email, string $password): bool
{
    ensure_session_started();

    $normalizedEmail = function_exists('mb_strtolower')
        ? mb_strtolower(trim($email))
        : strtolower(trim($email));
    if ($normalizedEmail === '' || $password === '') {
        return false;
    }

    $stmt = db()->prepare('SELECT id, name, email, password_hash, role FROM users WHERE LOWER(TRIM(email)) = :email LIMIT 1');
    $stmt->execute(['email' => $normalizedEmail]);
    $user = $stmt->fetch();

    if (!$user) {
        return false;
    }

    $storedHash = (string) ($user['password_hash'] ?? '');
    $isPasswordValid = password_verify($password, $storedHash);

    if (!$isPasswordValid && $storedHash !== '' && !str_starts_with($storedHash, '$')) {
        $isPasswordValid = hash_equals($storedHash, $password);
        if ($isPasswordValid) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = db()->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
            $updateStmt->execute([
                'password_hash' => $newHash,
                'id' => (int) $user['id'],
            ]);
        }
    }

    if (!$isPasswordValid) {
        return false;
    }

    session_regenerate_id(true);
    unset($user['password_hash']);
    $_SESSION['auth_user'] = $user;

    return true;
}

function require_login(): void
{
    if (!current_user()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function logout_user(): void
{
    ensure_session_started();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }

    session_destroy();
}
