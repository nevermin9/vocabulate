<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\View;
use App\Services\AuthService;
use App\Services\UserService;

final class LoginController
{
    public function index(): View
    {
        if (AuthService::isAuthenticated()) {
            redirect("/", 302);
        }

        return View::make("login");
    }

    public function login(): View
    {
        $req = Application::request();

        $user = new UserService()->verifyUser($req->data['password'], $req->data['email']);

        if ($user) {
            AuthService::login($user->id);
            redirect("/");
            die();
        }

        return View::make("login");
    }

    public function logout(): void
    {
        AuthService::logout();
        redirect("/login");
        die();
    }
}


