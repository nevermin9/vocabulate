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

    public readonly string $username;
    public readonly string $email;
    public readonly string $created_at;
    protected string $password_hash;
    protected string $id;
    protected int $verified = 0;
    protected int $premium = 0;
    protected ?string $ai_api_key = null;

    public string $idBin {
        get {
            return $this->id;
        }
    }

    public string $idString {
        get {
            return $this->convertBytesToString($this->idBin);
        }
    }

    public string $aiApiKey {
        get {
            return $this->ai_api_key ?? '';
        }
    }

    public string $passwordHash {
        get {
            return $this->password_hash;
        }
    }

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
     * @return User on success, null otherwise (parent handles execution).
     */
    public function save(): ?User
    {
        $this->id = static::generateIdBytes();
        return parent::save();
    }

    public static function findOne(array $where): ?static
    {
        if (array_key_exists(static::primaryKey(), $where)) {
            $where[static::primaryKey()] = static::convertStringToBytes($where[static::primaryKey()]);
        }
        return parent::findOne($where);
    }
}
