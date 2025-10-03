<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Application;
use App\Traits\UUIDTrait;

/**
 * @property-read string $id
 * @property-read string $email
 * @property-read string $passwordHash
 * @property-read ?string $aiApiKey
 * @property-read ?string $createdAt
 * @property-read bool $isVerified
 * @property-read bool $isPremium
 */
class User
{
    use UUIDTrait;

    private readonly string $id;
    private ?string $aiApiKey = null;
    private ?string $createdAt = null;
    private bool $isVerified = false; // Use boolean for logic
    private bool $isPremium = false; // Use boolean for logic

    public function __construct(
        private string $username,
        private readonly string $email,
        private string $passwordHash,
    ) {
    }

    /**
     * Factory method to create a User instance from database data.
     *
     * @param array $data Associative array of user data from the database.
     * @return User
     */
    public static function fromDatabase(array $data): User
    {
        $user = new User(
            $data['username'],
            $data['email'],
            $data['password_hash']
        );

        $user->id = $user->convertBytesToString($data['id']);
        $user->aiApiKey = $data['ai_api_key'] ?? null;
        $user->createdAt = $data['created_at'] ?? null;
        $user->isVerified = (bool)($data['verified'] ?? 0);
        $user->isPremium = (bool)($data['premium'] ?? 0);

        return $user;
    }

    public static function getColumns()
    {
        return ["id", "username", "email", "password_hash", "created_at", "verified", "premium", "ai_api_key"];
    }

    // --- Accessors (Getters) ---

    public function getId(bool $asBin = false): string
    {
        return $this->id;
    }

    public function getDbId(): string 
    {
        return $this->convertStringToBytes($this->id);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getAiApiKey(): ?string
    {
        return $this->aiApiKey;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function isPremium(): bool
    {
        return $this->isPremium;
    }

    public static function getByEmail(string $email): ?User
    {
        $db = Application::db();

        $stmt = $db->prepare(
            "SELECT id, username, email, password_hash, ai_api_key, created_at, verified, premium
            FROM users
            WHERE email = :email;"
        );

        if (!$stmt->execute(["email" => $email])) {
            return null;
        }

        $data = $stmt->fetch();

        if ($data) {
            return self::fromDatabase($data);
        }

        return null;
    }

    /**
     * Persists a new User object to the database.
     * @return $this
     */
    public function save(): static
    {
        $db = Application::db();
        $idBytes = $this->generateIdBytes();
        $this->id = $this->convertBytesToString($idBytes);

        $stmt = $db->prepare(
            "INSERT INTO users (id, username, email, password_hash, created_at, verified, premium)
            VALUES (:id, :username, :email, :password_hash, NOW(), :verified, :premium)"
        );

        $params = [
            "id" => $idBytes,
            "username" => $this->username,
            "email" => $this->email,
            "password_hash" => $this->passwordHash,
            "verified" => (int)$this->isVerified,
            "premium" => (int)$this->isPremium,
        ];

        if (!$stmt->execute($params)) {
            // Handle error: throw exception, log, etc.
            // For simplicity, we'll just return $this for now, but a failure should be signaled.
            throw new \Exception("implement error for user creation error");
        }

        $stmt = $db->prepare("SELECT created_at FROM users WHERE id = :id");
        if ($stmt->execute(["id" => $idBytes])) {
            $this->createdAt = $stmt->fetchColumn();
        }

        return $this;
    }

    public function update(array $params)
    {
        $db = Application::db();
        $cols = array_intersect(static::getColumns(), array_keys($params));

        if (! $cols) {
            return $this;
        }

        $setClauses = implode(", ", array_map(static fn($c) => "{$c} = :{$c}", $cols));
        $sql = "UPDATE users
                SET {$setClauses}
                WHERE id = :id_bin";

        $stmt = $db->prepare($sql);

        $params['id_bin'] = $this->getDbId();

        $ok = $stmt->execute($params);

        if (! $ok) {
            error_log("Database update failed for User ID: {$this->id}");
            return null;
        }

        return $this->syncAttributes($params);
    }

    private function syncAttributes(array $params): static
    {
        if (isset($params['username'])) {
            $this->username = $params['username'];
        }

        if (isset($params['password_hash'])) {
            $this->passwordHash = $params['password_hash'];
        }

        if (isset($params['ai_api_key'])) {
            $this->aiApiKey = $params['ai_api_key']; 
        }

        if (isset($params['verified'])) {
            $this->isVerified = (bool)$params['verified'];
        }

        if (isset($params['premium'])) {
            $this->isPremium = (bool)$params['premium'];
        }

        return $this;
    }
}
