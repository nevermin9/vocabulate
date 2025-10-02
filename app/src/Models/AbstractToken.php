<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Application;

abstract class AbstractToken
{
    protected const EXPIRES_IN_SECONDS = 60 * 60; // 1h

    protected int $id;
    protected string $token;
    protected string $tokenHash;
    protected readonly \DateTimeImmutable $expiresAt;
    protected readonly \DateTimeImmutable $createdAt;

    abstract protected static function getTableName(): string;

    public function __construct(
        protected readonly string $userId,
        int $expiresInSeconds = static::EXPIRES_IN_SECONDS
    )
    {
        $this->token = bin2hex(random_bytes(16));
        $this->tokenHash = hash("sha256", $this->token);
        $this->createdAt = new \DateTimeImmutable('now');
        $this->expiresAt = $this->createdAt->modify("+{$expiresInSeconds} seconds");
    }

    /**
     * Factory method to create a Token instance from database data.
     * This is used when RETRIEVING an existing token.
     *
     * @param array $data Associative array of token data from the database.
     * @return static
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

    public static function getByUserId(string $userId): ?static
    {
        $db = Application::db();
        $tableName = static::getTableName();

        $stmt = $db->prepare(
            "SELECT id, user_id, token_hash, expires_at, created_at
            FROM {$tableName}
            WHERE user_id = ?"
        );

        $ok = $stmt->execute([$userId]);

        if (! $ok) {
            return null;
        }

        $data = $stmt->fetch();

        if (! $data) {
            return null;
        }

        return static::fromDatabase($data);
    }

    /**
     * Retrieves an existing token by its token hash.
     * Uses the fromDatabase factory to correctly hydrate the object.
     */
    public static function getByTokenHash(string $tokenHash): ?static
    {
        $db = Application::db();
        $tableName = static::getTableName();

        $stmt = $db->prepare(
            "SELECT id, user_id, token_hash, expires_at, created_at
            FROM {$tableName}
            WHERE token_hash = :hash"
        );

        if (!$stmt->execute(['hash' => $tokenHash])) {
            return null;
        }

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }
        
        return static::fromDatabase($data);
    }

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

    public function save(): ?static
    {

        $db = Application::db();
        $tableName = static::getTableName();

        $stmt = $db->prepare(
            "INSERT INTO {$tableName} (user_id, token_hash, expires_at, created_at)
            VALUES (:user_id, :token_hash, :expires_at, :created_at)"
        );

        $ok = $stmt->execute([
            "user_id" => $this->userId,
            "token_hash" => $this->tokenHash,
            "expires_at" => $this->expiresAt->format("Y-m-d H:i:s"),
            "created_at" => $this->createdAt->format("Y-m-d H:i:s"),
        ]);

        if (!$ok) {
            // In a professional app, you should log the error or let PDO exceptions surface
            throw new \PDOException("Failed to save token to database.", 0);
        }

        $this->id = (int)$db->lastInsertId();

        return $this;
    }

    public function delete()
    {
        $db = Application::db();
        $tableName = static::getTableName();

        $stmt = $db->prepare(
            "DELETE FROM {$tableName} WHERE id = ?;"
        );

        $stmt->execute([$this->id]);
    }
}
