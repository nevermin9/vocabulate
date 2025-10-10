<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Application;
use App\Core\DB;

class Language extends AbstractModel
{
    public function __construct(
        public readonly string $code,
        public readonly string $name
    )
    {
    }

    public static function fromDatabase(array $data): static
    {
        return new static($data['code'], $data['name']);
    }

    public static function getTableName(): string
    {
        return 'languages';
    }

    public static function getColumns(): array
    {
        return ['code', 'name'];
    }
}
