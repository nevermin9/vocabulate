<?php
declare(strict_types=1);

namespace App\Services;

final class AuthService
{
    public static function isAuthenticated(): bool
    {
        static::ensureSession();
        return isset($_SESSION['user-id']);
    }

    public static function login(string $userId): void
    {
        static::ensureSession();
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['user-id'] = $userId;
    }

    public static function logout(): void
    {
        self::ensureSession();

        session_unset();
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
        session_destroy();
    }

    public static function getUserId(): string
    {
        self::ensureSession();
        return $_SESSION['user-id'];
    }

    public static function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}
