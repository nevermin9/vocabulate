<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Application;
use App\Traits\UniqueTrait;

class User
{
    use UniqueTrait;

    protected ?string $id = null;
    protected ?string $aiApiKey = null;

    public function __construct(
        protected string $username,
        protected string $email,
        protected string $passwordHash,
    )
    {
    }

    public function create()
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
            $this->id = $this->convertBytesToString($id);
        }
    }
}
