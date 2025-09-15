<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\AuthService;

final class LoginController
{
    public function index(): View
    {
        if (AuthService::isAuthenticated()) {
            redirect("/", 302);
        }

        return View::make("login");
    }
}


