<?php
declare(strict_types=1);

namespace App\Models;

use App\Traits\UUIDTrait;

/**
 * Note: Properties are set to 'protected' to allow access by AbstractModel's save/update logic,
 * and the names now match the database columns exactly.
 *
 * @property-read string $id (Binary string)
 * @property-read string $email
 * @property-read string $password_hash
 * @property-read ?string $ai_api_key
 * @property-read ?string $created_at
 * @property-read int $verified (0 or 1)
 * @property-read int $premium (0 or 1)
 */
class User extends AbstractModel
{
    use UUIDTrait;

    protected string $id;
    protected string $username;
    protected readonly string $email;
    protected string $password_hash;
    protected int $verified = 0;
    protected int $premium = 0;
    protected ?string $ai_api_key = null;
    protected ?string $created_at = null;

    public function __construct(
        string $username,
        string $email,
        string $password_hash
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->password_hash = $password_hash;
    }

    protected static function usesAutoIncrementPrimaryKey(): bool
    {
        return false;
    }

    public static function getTableName(): string
    {
        return 'users';
    }

    public static function getColumns(): array
    {
        return ["id", "username", "email", "password_hash", "verified", "premium", "ai_api_key", "created_at"];
    }

    public static function fromDatabase(array $data): static
    {
        $user = new static($data['username'], $data['email'], $data['password_hash']);
        $user->id = $data['id'];
        $user->verified = (int)$data['verified'];
        $user->premium = (int)$data['premium'];
        $user->ai_api_key = $data['ai_api_key'];
        $user->created_at = $data['created_at'];
        return $user;
    }

    public function getId(): string
    {
        // Returns the binary ID, which is suitable for database binding
        return $this->id;
    }

    public function getIdString(): string
    {
        // Returns the human-readable UUID string
        return $this->convertBytesToString($this->id);
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
        return $this->password_hash;
    }

    public function getAiApiKey(): ?string
    {
        return $this->ai_api_key;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function isVerified(): bool
    {
        return (bool)$this->verified;
    }

    public function isPremium(): bool
    {
        return (bool)$this->premium;
    }


    /**
     * Persists a new User object to the database.
     * @return bool True on success, false otherwise (parent handles execution).
     */
    public function save(): bool
    {
        $this->id = $this->generateIdBytes();
        return parent::save();
    }

    public static function findOne(array $where): ?static
    {
        if (array_key_exists(static::primaryKey(), $where)) {
            $where[static::primaryKey()] = $this->convertStringToBytes($where[static::primaryKey()]);
        }
        return parent::findOne($where);
    }
}
