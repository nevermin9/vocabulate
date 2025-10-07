<?php
namespace App\Repositories\Token;

use App\Models\AbstractToken;
use App\Models\User;

interface TokenRepositoryInterface
{
    public function getTokenByUserId(string $userId, string $tokenClass): ?AbstractToken;
    public function getTokenByHash(string $tokenHash, string $tokenClass): ?AbstractToken;
    public function saveToken(AbstractToken $token): bool;
    public function deleteToken(AbstractToken $token): void;
    public function checkToken(string $rawToken, string $tokenClass): bool;

    /**
     * Fetches a User and their associated Token by the token hash, using the provided Token class.
     *
     * @template T of AbstractToken
     * @param string $tokenHash The hash of the token to look up.
     * @param class-string<T> $tokenClass The fully qualified class name of the token model (e.g., ForgotPasswordToken::class).
     * @return array{user: User, token: T}|null An array containing User and Token instances, or null if not found.
     */
    public function getUserAndToken(string $tokenHash, string $tokenClass): ?array;
}
