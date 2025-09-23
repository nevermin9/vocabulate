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
        static::generateCSRF(true);
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

    public static function getCSRF(): string
    {
        self::ensureSession();
        self::generateCSRF();
        return $_SESSION['csrf_token'];
    }

    public static function checkCSRF(string $userToken): bool
    {
        self::ensureSession();
        return hash_equals($_SESSION['csrf_token'], $userToken);
    }

    private static function generateCSRF(bool $isForce = false): void
    {
        static::ensureSession();
        if ($isForce || ! isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}
