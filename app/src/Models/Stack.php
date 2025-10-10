<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Application;
use App\Core\DB;

class Stack extends AbstractModel
{
    public readonly int $id;
    public readonly string $user_id;
    public readonly string $name;
    public readonly string $language_code;
    public readonly string $created_at;

    public function __construct(
        string $userId,
        string $name,
        string $langCode,
    )
    {
        $this->user_id = $userId;
        $this->name = $name;
        $this->language_code = $langCode;
    }

    public static function getTableName(): string
    {
        return "stacks";
    }

    public static function getColumns(): array
    {
        return ["id", "user_id", "name", "language_code", "created_at"];
    }

    public static function fromDatabase(array $data): static
    {
        $stack = new static($data['user_id'], $data['name'], $data['language_code']);
        $stack->id = $data['id'];
        $stack->created_at = $data['created_at'];
        return $stack;
    }
}
