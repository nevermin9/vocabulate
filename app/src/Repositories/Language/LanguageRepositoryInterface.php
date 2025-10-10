<?php
declare(strict_types=1);

namespace App\Repositories\Language;

use App\Models\Language;

interface LanguageRepositoryInterface
{
    /**
    * @return Language[]
    */
    public function getAllAsc(): array;
}
