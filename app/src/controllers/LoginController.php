<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\View;
use App\Services\AuthService;
use App\Services\UserService;

final class LoginController
{
    private function renderLogin(string $email = '', string $password = '', array $errors = []): View
    {
        return View::make("login", [
            "token" => AuthService::getCSRF(),
            "errors" => $errors,
            "email" => $email,
            "password" => $password,
        ]);
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

        $token = $req->data['_token'];

        $isValidToken = AuthService::checkCSRF($token);

        if (! $isValidToken) {
            unset($_SESSION['csrf_token']);
            session_regenerate_id(true);
            redirect("/login", 403);
            die();
        }

        [$user, $errors] = new UserService()->verifyUser($req->data['password'], $req->data['email']);

        if ($user) {
            AuthService::login($user->id);
            // if user verified via email
            redirect("/");
            die();
        }

        return $this->renderLogin($req->data['email'], $req->data['password'], $errors);
    }

    public function logout(): void
    {
        AuthService::logout();
        redirect("/login");
        die();
    }
}


