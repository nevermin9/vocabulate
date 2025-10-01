<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\View;
use App\Forms\LoginForm;
use App\Forms\RegistrationForm;
use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use App\Traits\CSRFGuardTrait;
use App\Traits\SessionStoreTrait;

final class AuthController
{
    use SessionStoreTrait;
    use CSRFGuardTrait;

    public function loginView(): View
    {
        if (AuthService::isAuthenticated()) {
            redirect("/");
        }

        $loginForm = $this->getAndClearFromSession('login-form');

        return View::make("login", [
            "token" => AuthService::getCSRF(),
            "model" => $loginForm?->getFormModel() ?? null,
            "errors" => $loginForm?->errors ?? null,
        ]);
    }

    public function registrationView(): View
    {
        if (AuthService::isAuthenticated()) {
            redirect("/");
        }

        $regForm = $this->getAndClearFromSession('registration-form');

        return View::make("registration", [
            "token" => AuthService::getCSRF(),
            "password_rules" => new RegistrationForm()->getPasswordMessages(),
            "model" => $regForm?->getFormModel() ?? null,
            "errors" => $regForm?->errors ?? null,
        ]);
    }

    public function forgotPasswordView(): View
    {
        if (AuthService::isAuthenticated()) {
            redirect("/");
        }

        $forgotPassForm = $this->getAndClearFromSession('forgot-password-form');

        return View::make("forgot-password", [
            "token" => AuthService::getCSRF(),
            "model" => $forgotPassForm?->getFormModel() ?? null,
            "errors" => $forgotPassForm?->errors ?? null,
        ]);
    }

    public function login()
    {
        $req = Application::request();
        $token = $req->data['token'];

        if (! AuthService::checkCSRF($token)) {
            $this->forbidAndExit();
        }

        $loginForm = new LoginForm();
        $loginForm->load($req->data);

        $isValid = $loginForm->validate();

        if ($isValid) {
            $user = User::get($loginForm->email);

            if ($loginForm->validateUser($user)) {
                AuthService::login($user->id);
                redirect("/");
                die();
            }
        }

        $this->saveInSession('login-form', $loginForm);
        redirect("/login");
        die();
    }

    public function register()
    {
        $req = Application::request();
        $token = $req->data['token'];

        if (! AuthService::checkCSRF($token)) {
            $this->forbidAndExit();
        }

        $regForm = new RegistrationForm();
        $regForm->load($req->data);

        $isValid = $regForm->validate();

        if ($isValid) {
            $userService = new UserService();
            $user = $userService->register($regForm->email, $regForm->password);
            AuthService::login($user->id);
            redirect("/");
            die();
        }

        $this->saveInSession('registration-form', $regForm);
        redirect("/registration");
        die();
    }

    public function forgotPassword()
    {

    }

    public function logout()
    {
        AuthService::logout();
        redirect("/login");
        die();
    }
}
