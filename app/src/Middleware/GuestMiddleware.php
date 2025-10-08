<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Interfaces\MiddlewareInterface;
use App\Core\Request;
use App\Services\AuthService;

class GuestMiddleware implements MiddlewareInterface
{
    public function __construct(protected AuthService $auth)
    {
    }

    public function handle(Request $req): mixed
    {
        if ($this->auth->isAuthenticated()) {
            redirect("/");
            die();
        }
        
        return null;
    }
}
