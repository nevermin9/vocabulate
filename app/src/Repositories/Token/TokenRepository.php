<?php
declare(strict_types=1);

namespace App\Repositories\Token;

use App\Models\AbstractToken;
use App\Models\ForgotPasswordToken;
use App\Models\VerificationToken;
use App\Core\DB;
use App\Models\User;

class TokenRepository implements TokenRepositoryInterface
{
    public function __construct(
        protected DB $db,
    )
    {
    }

    public function getTokenByUserId(string $userId, string $tokenClass): ?AbstractToken
    {
        return $tokenClass::findOne(["user_id" => $userId]);
    }

    public function getTokenByHash(string $tokenHash, string $tokenClass): ?AbstractToken
    {
        return $tokenClass::findOne(["token_hash" => $tokenHash]);
    }

    public function saveToken(AbstractToken $token): bool
    {
        return $token->save();
    }

    public function deleteToken(AbstractToken $token): void
    {
        $token->delete();
    }

    public function getUserAndToken(string $tokenHash, string $tokenClass): ?array
    {
        if (!is_subclass_of($tokenClass, AbstractToken::class)) {
             throw new \InvalidArgumentException("The provided token class must inherit from AbstractToken.");
        }

        /** @var T $tokenClass */
        $tokenTableName = $tokenClass::getTableName();
        
        $userColumns = User::getColumns();
        $tokenTableCols = $tokenClass::getColumns();

        $userColumns = implode(", ", array_map(static fn($c) => "u.{$c} AS user_{$c}", $userColumns));
        $tokenTableCols = implode(", ", array_map(static fn($c) => "t.{$c} AS token_{$c}", $tokenTableCols));

        $stmt = $db->prepare(
            "SELECT {$userColumns}, {$tokenTableCols}
            FROM users AS u
            INNER JOIN
            {$tokenTableName} AS t
            ON u.id = t.user_id
            WHERE t.token_hash = :token_hash"
        );
        
        if (! $stmt->execute(["token_hash" => $tokenHash])) {
            throw new \Exception("Error getting the user on join with token table '{$tokenTableName}'");
        }

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        $unprefixedData = static::filterAndUnprefixData($data);
        
        $userInstance = User::fromDatabase($unprefixedData['user']);
        
        $tokenInstance = $tokenClass::fromDatabase($unprefixedData['token']);
        
        return [
            'user' => $userInstance,
            'token' => $tokenInstance,
        ];
    }

    public function checkToken(string $rawToken, string $tokenClass): bool
    {
        $tokenHash  = $tokenClass::generateTokenHash($rawToken);
        $token = $this->getTokenByHash($rawToken, $tokenClass);

        if (! $token) {
            return false;
        }

        $isExpired = $token->isExpired();

        if ($isExpired) {
            $this->deleteToken($token);
        }

        return ! $isExpired;
    }

    protected static function filterAndUnprefixData(array $prefixedData): array
    {
        $userData = [];
        $tokenData = [];

        foreach ($prefixedData as $prefixedKey => $value) {
            if (str_starts_with($prefixedKey, 'user_')) {
                $cleanKey = substr($prefixedKey, 5); 
                $userData[$cleanKey] = $value;
            }
            else if (str_starts_with($prefixedKey, 'token_')) {
                $cleanKey = substr($prefixedKey, 6); 
                $tokenData[$cleanKey] = $value;
            }
        }

        return [
            'user' => $userData,
            'token' => $tokenData
        ];
    }
}
