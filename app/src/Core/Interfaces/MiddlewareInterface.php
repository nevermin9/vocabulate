<?php
declare(strict_types=1);

namespace App\Core\Interfaces;

interface MiddlewareInterface
{
    public function handle(): mixed;
}
