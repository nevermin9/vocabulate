<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\View;
use App\Services\AuthService;
use App\Services\UserService;

final class LoginController
{
    private function renderLogin(array $errors = []): View
    {
        return View::make("login", ["token" => AuthService::getCSRF(), "errors" => $errors]);
    }

    public function index(): View
    {
        if (AuthService::isAuthenticated()) {
            redirect("/", 302);
        }

        return $this->renderLogin();
    }

    public function login(): View
    {
        $req = Application::request();

        [$user, $errors] = new UserService()->verifyUser($req->data['password'], $req->data['email']);

        if ($user) {
            AuthService::login($user->id);
            redirect("/");
            die();
        }

        return $this->renderLogin($errors);
    }

    public function logout(): void
    {
        AuthService::logout();
        redirect("/login");
        die();
    }
}


