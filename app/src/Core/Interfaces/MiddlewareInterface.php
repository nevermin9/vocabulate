<?php
declare(strict_types=1);

namespace App\Core\Interfaces;

use App\Core\Request;

interface MiddlewareInterface
{
    public function handle(Request $req): mixed;
}
