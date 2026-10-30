<?php
declare(strict_types=1);
namespace App\Core;

final class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
            session_start();
        }
    }

    public static function login(string $username): void
    {
        session_regenerate_id(true); // evita session fixation
        $_SESSION['user'] = $username;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?string
    {
        return $_SESSION['user'] ?? null;
    }

    public static function csrfToken(): string
    {
        return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    }

    public static function verifyCsrf(?string $token): bool
    {
        return is_string($token) && hash_equals($_SESSION['csrf'] ?? '', $token);
    }
}
