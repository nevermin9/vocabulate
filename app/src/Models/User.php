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

    public function __construct(
        protected string $username,
        protected string $email,
        protected string $passwordHash,
    )
    {
    }

    public function create(): User
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
}
