<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Stack;
use App\Repositories\Stack\StackRepositoryInterface;

class StackService
{
    public function __construct(
        protected StackRepositoryInterface $stackRepo,
    )
    {
    }

    public function createStack(string $userId, string $name, string $langCode): Stack
    {
        $stack = new Stack($userId, $name, $langCode);
        $this->stackRepo->save($stack);
        return $stack;
    }
}
