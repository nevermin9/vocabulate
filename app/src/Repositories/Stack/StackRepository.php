<?php
declare(strict_types=1);

namespace App\Repositories\Stack;

use App\Database\Enums\Order;
use App\Models\Stack;

class StackRepository implements StackRepositoryInterface
{
    public function save(Stack $stack): ?Stack
    {
        return $stack->save();
    }

    public function getAllAsc(string $userId): array
    {
        return Stack::findMany(['user_id' => $userId], ['orderBy' => 'name', 'order' => Order::Asc]);
    }
}
