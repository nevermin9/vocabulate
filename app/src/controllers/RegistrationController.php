<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\View;
use App\Services\AuthService;
use App\Services\UserService;

final class RegistrationController
{
    public function renderRegistrationPage(
        string $email = '',
        string $password = '',
        string $confirmPassword = '',
        array $errors = []
    ): View
    {
        return View::make("registration", [
            "password_rules" => UserService::getPasswordRules(),
            "email" => $email,
            "password" => $password,
            "confirm_password" => $confirmPassword,
            "errors" => $errors,
            "token" => AuthService::getCSRF(),
        ]);
    }

    public function index(): View
    {
        if (AuthService::isAuthenticated()) {
            redirect("/", 302);
        }

        return $this->renderRegistrationPage();
    }

    public function register(): View
    {

        $req = Application::request();

        $token = $req->data['_token'];

        $isValidToken = AuthService::checkCSRF($token);

        if (! $isValidToken) {
            unset($_SESSION['csrf_token']);
            session_regenerate_id(true);
            redirect("/registration", 403);
            die();
        }

        [$newUser, $errors] = new UserService()->register($req->data['password'], $req->data['confirm_password'], $req->data['email']);

        if ($newUser) {
            AuthService::login($newUser->id);
            redirect("/");
            die();
        }

        return $this->renderRegistrationPage(
            $req->data['email'],
            $req->data['password'],
            $req->data['confirm_password'],
            $errors
        );
    }
}
