<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\User;

final class AuthService
{
    private const USER_KEY = "user_id";
    private const CSRF_TOKEN_KEY = "csrf_token";
    private ?User $user = null;

    public function __construct(private Session $session)
    {
        $this->generateCSRF();
        
        if ($this->isAuthenticated()) {
            $this->user = User::findOne([User::primaryKey() => $this->getUserId()]);
            
            if ($this->user === null) {
                $this->logout();
            }
        }
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function isAuthenticated(): bool
    {
        return !empty($this->session->get(static::USER_KEY));
    }

    public function login(User $user): void
    {
        $this->session->restart();
        $this->session->set(static::USER_KEY, $user->getIdString());
        $this->user = $user;
    }

    public function logout(): void
    {
        $this->session->clear();
        $this->user = null;
    }

    public function getUserId(): ?string
    {
        return $this->session->get(static::USER_KEY);
    }

    public function getCSRF(): string
    {
        return $this->session->get(static::CSRF_TOKEN_KEY);
    }

    public function checkCSRF(string $userToken): bool
    {
        $sessionToken = $this->session->get(static::CSRF_TOKEN_KEY);
        
        if ($sessionToken === null) {
            return false;
        }
        
        return hash_equals($sessionToken, $userToken);
    }

    public function unsetCSRF(): void
    {
        $this->session->remove(static::CSRF_TOKEN_KEY);
    }

    /**
     * Generates a CSRF token if one doesn't exist.
     * One token per session is sufficient for CSRF protection.
     */
    private function generateCSRF(): void
    {
        if (empty($this->session->get(static::CSRF_TOKEN_KEY))) {
            $this->session->set(static::CSRF_TOKEN_KEY, bin2hex(random_bytes(32)));
        }
    }
}
