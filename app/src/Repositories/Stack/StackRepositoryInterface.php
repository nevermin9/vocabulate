<?php
declare(strict_types=1);

namespace App\Repositories\Stack;

use App\Models\Stack;

interface StackRepositoryInterface
{
    /**
    * @param string $userId binary user id
    * @return Stack[]
    */
    public function getAllAsc(string $userId): array;
}
