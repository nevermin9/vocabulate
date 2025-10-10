<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Application;
use DateTimeImmutable;

abstract class AbstractToken extends AbstractModel
{
    protected const EXPIRES_IN_SECONDS = 60 * 60;

    public readonly int $id;
    public readonly string $token;
    public readonly string $token_hash;
    public readonly \DateTimeImmutable $expires_at;
    public readonly \DateTimeImmutable $created_at;

    public function __construct(
        public readonly string $user_id,
        ?int $expiresInSeconds = null
    ) {
        $this->token = bin2hex(random_bytes(16));
        $this->token_hash = static::generateTokenHash($this->token);
        $expiry = $expiresInSeconds ?? static::EXPIRES_IN_SECONDS;
        $this->created_at = new \DateTimeImmutable('now');
        $this->expires_at = $this->created_at->modify("+{$expiry} seconds");
    }

    public static function getColumns(): array
    {
        return ["id", "user_id", "token_hash", "expires_at", "created_at"];
    }

    /**
     * Hydrates a Token instance from database data.
     * Uses reflection to set readonly properties.
     */
    public static function fromDatabase(array $data): static
    {
        $instance = (new \ReflectionClass(static::class))->newInstanceWithoutConstructor();
        
        $instance->id = (int)$data['id'];
        $instance->user_id = $data['user_id'];
        $instance->token_hash = $data['token_hash'];
        $instance->created_at = new \DateTimeImmutable($data['created_at']);
        $instance->expires_at = new \DateTimeImmutable($data['expires_at']);

        return $instance;
    }

    /**
     * Generates a SHA-256 hash for the raw token.
     */
    public static function generateTokenHash(string $rawToken): string
    {
        return hash("sha256", $rawToken);
    }

    /**
     * Saves the token and captures the generated ID.
     * Overrides parent to set the ID after insert.
     */
    public function save(): ?AbstractToken
    {
        $tableName = static::getTableName();
        $columns = static::getColumnsForInsert($this);
        
        $placeholders = array_map(static fn($c) => ":{$c}", $columns);
        $stmt = static::prepare(
            "INSERT INTO {$tableName} (" . implode(", ", $columns) . ")
            VALUES (" . implode(", ", $placeholders) . ")"
        );
        
        $ok = $stmt->execute([
            "user_id" => $this->user_id,
            "token_hash" => $this->token_hash,
            "expires_at" => $this->expires_at->format("Y-m-d H:i:s"),
            "created_at" => $this->created_at->format("Y-m-d H:i:s"),
        ]);

        if (!$ok) {
            // log the error or let PDO exceptions surface
            throw new \PDOException("Failed to save token to database.", 0);
        }

        $this->id = (int)static::db()->lastInsertId();
        return $this;
    }

    public function isExpired(): bool
    {
        return new \DateTimeImmutable("now") >= $this->expires_at;
    }
}
