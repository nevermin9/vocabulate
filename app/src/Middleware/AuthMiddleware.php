<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Application;
use App\Core\Interfaces\MiddlewareInterface;
use App\Core\Request;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $req): mixed
    {
        $auth = Application::authService();
        if (! $auth->isAuthenticated()) {
            redirect("/login");
            die();
        }
        
        return null;
    }
}
