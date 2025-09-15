<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\View;
use App\Services\AuthService;
use App\Services\UserService;

final class RegistrationController
{
    public function index(): View
    {
        if (AuthService::isAuthenticated()) {
            redirect("/", 302);
        }

        return View::make("registration");
    }

    public function register(): void
    {

        $req = Application::request();

        $newUser = new UserService()->register($req->data['password'], $req->data['email']);

        AuthService::login($newUser->id);

        redirect("/");
    }
}
