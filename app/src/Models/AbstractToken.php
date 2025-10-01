<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Application;

abstract class AbstractToken
{
    protected const EXPIRES_IN = 60 * 60; // 1h
    public readonly int $id;
    protected readonly string $userId;
    protected readonly string $token;
    protected readonly string $tokenHash;
    protected readonly string $expiresAt;
    protected readonly string $createdAt;
    protected readonly string $databaseName;

    public function __construct(string $userId, string $dbName)
    {
        $this->userId = $userId;
        $this->databaseName = $dbName;
        $this->token = bin2hex(random_bytes(16));
        $this->tokenHash = hash("sha256", $this->token);
        $this->createdAt = date("Y-m-d H:i:s", time() + static::EXPIRES_IN);
    }

    public function save(): ?AbstractToken
    {

        $db = Application::db();
        $stmt = $db->prepare(
            "INSERT INTO {$this->databaseName} (user_id, token_hash, expires_at, created_at)
            VALUES (:user_id, :token_hash, :expires_at, :created_at)"
        );

        $ok = $stmt->execute([
            "user_id" => $this->userId,
            "token_hash" => $this->tokenHash,
            "expires_at" => $this->expiresAt,
            "created_at" => $this->createdAt,
        ]);

        if (! $ok) {
            return null;
        }

        $this->id = (int) $db->lastInsertId();
        $stmt = $db->prepare("SELECT created_at FROM verification_tokens WHERE id = :id");
        $stmt->execute(["id" => $this->id]);
        $this->createdAt = $stmt->fetchColumn();

        return $this;
    }
}
