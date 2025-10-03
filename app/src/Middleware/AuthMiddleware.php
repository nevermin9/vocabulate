<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Interfaces\MiddlewareInterface;
use App\Core\Request;
use App\Services\AuthService;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $req): mixed
    {
        if (! AuthService::isAuthenticated()) {
            redirect("/login");
            die();
        }
        
        return null;
    }
}
