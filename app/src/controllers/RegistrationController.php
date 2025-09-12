<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\View;
use App\Services\UserService;

final class RegistrationController
{
    public function index(): View
    {
        return View::make("registration");
    }

    public function register(): string
    {
        $req = Application::request();

        new UserService()->register($req->data['password'], $req->data['email']);

        return "success!";
    }
}
