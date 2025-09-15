<?php
declare(strict_types=1);

namespace App\Services;

final class AuthService
{
    public static function isAuthenticated(): bool
    {
        return isset($_SESSION['user-id']);
    }

    public static function login(string $userId)
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(35));
        $_SESSION['user-id'] = $userId;
    }

    public static function logout()
    {
        session_unset();
        session_destroy();
        setcookie('PHPSESSID', '', time() - 3600, '/');
        header('Location: /login');
        die();
    }

    public static function getUserId(): string
    {
        return $_SESSION['user-id'];
    }
}
