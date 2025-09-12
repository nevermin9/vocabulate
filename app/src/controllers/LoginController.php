<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class LoginController
{
    public function index(): View
    {
        return View::make("login");
    }
}


