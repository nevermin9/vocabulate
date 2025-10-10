<?php
declare(strict_types=1);

namespace App\Repositories\Language;

use App\Database\Enums\Order;
use App\Models\Language;

class LanguageRepository implements LanguageRepositoryInterface
{
    public function getAllAsc(): array
    {
        return Language::findMany([], ['orderBy' => 'name', 'order' => Order::Asc]);
    }
}
