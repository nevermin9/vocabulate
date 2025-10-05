<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Application;

final class AuthService
{
    private const USER_KEY = "user_id";
    private const CSRF_TOKEN_KEY = "csrf_token";

    public function isAuthenticated(): bool
    {
        $session = Application::session();
        return ! empty($session->get(static::USER_KEY));
    }

    public function login(string $userId): void
    {
        $session = Application::session();
        $session->restart();
        $this->generateCSRF(true);
        $session->set(static::USER_KEY, $userId);
    }

    public function logout(): void
    {
        $session = Application::session();
        $session->clear();
    }

    public function getUserId(): string
    {
        return Application::session()->get(static::USER_KEY);
    }

    public function getCSRF(): string
    {
        self::generateCSRF();
        return Application::session()->get(static::CSRF_TOKEN_KEY);
    }

    public function checkCSRF(string $userToken): bool
    {
        $session = Application::session();
        return hash_equals($session->get(static::CSRF_TOKEN_KEY), $userToken);
    }

    public function unsetCSRF()
    {
        Application::session()->remove(static::CSRF_TOKEN_KEY);
    }

    private function generateCSRF(bool $isForce = false): void
    {
        $session = Application::session();
        if ($isForce || empty($session->get(static::CSRF_TOKEN_KEY))) {
            $session->set(static::CSRF_TOKEN_KEY, bin2hex(random_bytes(32)));
        }
    }
}
