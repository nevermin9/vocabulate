<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Application;
use App\Traits\UUIDTrait;

class Stack
{
    protected ?int $id;
    protected ?string $createdAt;

    public function __construct(
        protected string $userId,
        protected string $name,
        protected string $langCode
    )
    {
    }

    public function create(): Stack
    {
        $db = Application::db();

        $stmt = $db->prepare(
            "INSERT INTO stacks (user_id, name, language_code, created_at)
            VALUES (:user_id, :name, :language_code, NOW())"
        );

        $ok = $stmt->execute([
            "user_id" => $this->userId,
            "name" => $this->name,
            "language_code" => $this->langCode
        ]);

        if ($ok) {
            $this->id = (int) $db->lastInsertId();
            $stmt = $db->prepare("SELECT created_at FROM stacks WHERE id = :id");
            $stmt->execute(["id" => $this->id]);
            $this->createdAt = $stmt->fetchColumn();
        }

        return $this;
    }

    public static function get(int $id): ?Stack
    {
        $db = Application::db();
        $stmt = $db->prepare(
            "SELECT id,user_id,name,language_code,created_at
            FROM stacks
            WHERE id = :id"
        );
        $ok = $stmt->execute(["id" => $id]);

        if (! $ok) {
            return null;
        }

        $data = $stmt->fetch();
        $stack = new Stack($data['user_id'], $data['name'], $data['language_code']);
        $stack->id = (int) $data['id'];
        $stack->createdAt = $data['created_at'];

        return $stack;
    }

    public static function getAll(string $userId): array
    {
        $db = Application::db();

        // join to show number of flashcards
        $stmt = $db->prepare(
            "SELECT id,name,language_code FROM stacks WHERE `user_id`=?"
        );

        $ok = $stmt->execute([$userId]);

        if ($ok) {
            return $stmt->fetchAll();
        }

        return [];
    }
}
