<?php
declare(strict_types=1);

namespace App\Models\Associations;

use App\Core\Application;
use App\Models\AbstractToken;
use App\Models\User;

class UserTokenAssociation
{
    /**
     * Splits a flat database result array (containing prefixed columns) into two 
     * clean arrays, one for User and one for the Token.
     *
     * @param array $prefixedData The flat associative array from the database join result.
     * @return array An associative array containing 'user' and 'token' data arrays.
     */
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

    /**
     * Fetches a User and their associated Token by the token hash, using the provided Token class.
     *
     * @template T of AbstractToken
     * @param string $tokenHash The hash of the token to look up.
     * @param class-string<T> $tokenClass The fully qualified class name of the token model (e.g., ForgotPasswordToken::class).
     * @return array{user: User, token: T}|null An array containing User and Token instances, or null if not found.
     */
    public static function getByUserAndTokenHash(string $tokenHash, string $tokenClass): ?array
    {
        if (!is_subclass_of($tokenClass, AbstractToken::class)) {
             throw new \InvalidArgumentException("The provided token class must inherit from AbstractToken.");
        }

        /** @var T $tokenClass */
        $tokenTableName = $tokenClass::getTableName();
        
        $db = Application::db();
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

        $unprefixedData = self::filterAndUnprefixData($data);
        
        $userInstance = User::fromDatabase($unprefixedData['user']);
        
        $tokenInstance = $tokenClass::fromDatabase($unprefixedData['token']);
        
        return [
            'user' => $userInstance,
            'token' => $tokenInstance,
        ];
    }
}
