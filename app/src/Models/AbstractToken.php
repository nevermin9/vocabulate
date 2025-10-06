<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Application;
use DateTimeImmutable;

abstract class AbstractToken extends AbstractModel
{
    protected const EXPIRES_IN_SECONDS = 60 * 60;

    protected int $id;
    protected string $token;
    protected string $tokenHash;
    protected readonly \DateTimeImmutable $expiresAt;
    protected readonly \DateTimeImmutable $createdAt;

    public function __construct(
        protected readonly string $userId,
        ?int $expiresInSeconds = null
    ) {
        $this->token = bin2hex(random_bytes(16));
        $this->tokenHash = static::generateTokenHash($this->token);
        $expiry = $expiresInSeconds ?? static::EXPIRES_IN_SECONDS;
        $this->createdAt = new \DateTimeImmutable('now');
        $this->expiresAt = $this->createdAt->modify("+{$expiry} seconds");
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
        $instance->userId = $data['user_id'];
        $instance->tokenHash = $data['token_hash'];
        $instance->createdAt = new \DateTimeImmutable($data['created_at']);
        $instance->expiresAt = new \DateTimeImmutable($data['expires_at']);

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
     * Retrieves a token by user ID.
     */
    public static function getByUserId(string $userId): ?static
    {
        return static::findOne(['user_id' => $userId]);
    }

    /**
     * Retrieves a token by its hash.
     */
    public static function getByTokenHash(string $tokenHash): ?static
    {
        return static::findOne(['token_hash' => $tokenHash]);
    }

    /**
     * Saves the token and captures the generated ID.
     * Overrides parent to set the ID after insert.
     */
    public function save(): bool
    {
        $tableName = static::getTableName();
        $columns = static::getColumnsForInsert();
        
        $placeholders = array_map(static fn($c) => ":{$c}", $columns);
        $stmt = static::prepare(
            "INSERT INTO {$tableName} (" . implode(", ", $columns) . ")
            VALUES (" . implode(", ", $placeholders) . ")"
        );
        
        $ok = $stmt->execute([
            "user_id" => $this->userId,
            "token_hash" => $this->tokenHash,
            "expires_at" => $this->expiresAt->format("Y-m-d H:i:s"),
            "created_at" => $this->createdAt->format("Y-m-d H:i:s"),
        ]);

        if (!$ok) {
            // log the error or let PDO exceptions surface
            throw new \PDOException("Failed to save token to database.", 0);
        }

        $this->id = (int)Application::db()->lastInsertId();

        return true;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getRawToken(): string
    {
        return $this->token;
    }

    public function getTokenHash(): string
    {
        return $this->tokenHash;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isExpired(): bool
    {
        return new \DateTimeImmutable("now") >= $this->expiresAt;
    }
}
