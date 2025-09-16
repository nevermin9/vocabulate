<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Stack;

final class StackService
{
    public function createStack(string $userId, string $name, string $langCode): Stack
    {
        $stack = new Stack($userId, $name, $langCode)->create();

        return $stack;
    }
}
