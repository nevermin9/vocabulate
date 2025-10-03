<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Interfaces\MiddlewareInterface;
use App\Services\AuthService;

class GuestMiddleware implements MiddlewareInterface
{
    public function handle(): mixed
    {
        if (AuthService::isAuthenticated()) {
            redirect("/");
            die();
        }
        
        return null;
    }
}
