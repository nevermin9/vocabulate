<?php
declare(strict_types=1);

namespace App\Core\Interfaces;

interface DataInjectorInterface
{
    public function inject(...$data): array;
}
