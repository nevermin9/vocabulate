<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Application;
use App\Traits\UUIDTrait;

class User
{
    use UUIDTrait;

    public readonly ?string $id;
    protected ?string $aiApiKey = null;
    protected ?string $createdAt = null;
    protected int $verified = 0;
    protected int $premium = 0;

    public function __construct(
        protected string $username,
        public readonly string $email,
        public readonly string $passwordHash,
    )
    {
    }

    public function save(): User
    {
        $db = Application::db();
        $id = $this->generateIdBytes();

        $stmt = $db->prepare(
            "INSERT INTO users (id, username, email, password_hash, created_at)
            VALUES (:id, :username, :email, :password_hash, NOW())"
        );

        $ok = $stmt->execute([
            "id" => $id,
            "username" => $this->username,
            "email" => $this->email,
            "password_hash" => $this->passwordHash
        ]);

        if ($ok) {
            $this->id = $id;
            $stmt = $db->prepare("SELECT created_at FROM users WHERE id = :id");
            $stmt->execute(["id" => $id]);
            $this->createdAt = $stmt->fetchColumn();
        }

        return $this;
    }

    public static function get(string $email): ?User
    {
        $db = Application::db();

        $stmt = $db->prepare(
            "SELECT id, username, email, password_hash, ai_api_key, created_at
            FROM users
            WHERE email = :email;"
        );

        $ok = $stmt->execute(["email" => $email]);
        $data = $ok ? $stmt->fetch() : false;

        if ($data) {
            $user = new User($data['username'], $data['email'], $data['password_hash']);

            $user->id = $data['id'];
            $user->aiApiKey = $data['ai_api_key'];
            $user->createdAt = $data['created_at'];

            return $user;
        }

        return null;
    }
}
