<?php
declare(strict_types=1);

namespace App\Core\Interfaces;

use App\Core\Request;

interface MiddlewareInterface
{
    /**
     * @param Request $req The current Request object.
     * @return mixed A response object if middleware halts execution, or null to continue.
     */
    public function handle(Request $req): mixed;
}
