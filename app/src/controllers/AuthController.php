<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\View;
use App\Forms\ForgotPasswordForm;
use App\Forms\LoginForm;
use App\Forms\RegistrationForm;
use App\Forms\ResetPasswordForm;
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
            "csrf_token" => AuthService::getCSRF(),
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
            "csrf_token" => AuthService::getCSRF(),
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
            "csrf_token" => AuthService::getCSRF(),
            "model" => $forgotPassForm?->getFormModel() ?? null,
            "errors" => $forgotPassForm?->errors ?? null,
        ]);
    }

    public function resetPasswordView(): View
    {
        if (AuthService::isAuthenticated()) {
            redirect("/");
        }

        $resetPasswordForm = $this->getAndClearFromSession("reset-password-form");
        $req = Application::request();

        if (! $resetPasswordForm) {
            $userService = new UserService();

            if (! $userService->checkResetPasswordToken($req->data['token'] ?? '')) {
                redirect("/reset-password/invalid");
                die();
            }
        }

        return View::make("reset-password", [
            "csrf_token" => AuthService::getCSRF(),
            "model" => $resetPasswordForm?->getFormModel() ?? null,
            "errors" => $resetPasswordForm?->errors ?? null,
            "password_rules" => new ResetPasswordForm()->getPasswordMessages(),
            "token" => $req->data['token']
        ]);
    }


    public function login()
    {
        $req = Application::request();
        $token = $req->data['csrf_token'];

        if (! AuthService::checkCSRF($token)) {
            $this->forbidAndExit();
        }

        $loginForm = new LoginForm();
        $loginForm->load($req->data);

        $isValid = $loginForm->validate();

        if ($isValid) {
            $user = User::getByEmail($loginForm->email);

            if ($loginForm->validateUser($user)) {
                AuthService::login($user->getId());
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
        $token = $req->data['csrf_token'];

        if (! AuthService::checkCSRF($token)) {
            $this->forbidAndExit();
        }

        $regForm = new RegistrationForm();
        $regForm->load($req->data);

        $isValid = $regForm->validate();

        if ($isValid) {
            $userService = new UserService();
            $user = $userService->register($regForm->email, $regForm->password);
            AuthService::login($user->getId());
            redirect("/");
            die();
        }

        $this->saveInSession('registration-form', $regForm);
        redirect("/registration");
        die();
    }

    public function forgotPassword()
    {
        $req = Application::request();
        $token = $req->data['csrf_token'];

        if (! AuthService::checkCSRF($token)) {
            $this->forbidAndExit();
        }

        $forgotPassForm = new ForgotPasswordForm();
        $forgotPassForm->load($req->data);
        $isValid = $forgotPassForm->validate();

        if ($isValid) {
            $user = User::getByEmail($forgotPassForm->email);
            if (! $user) {
                redirect("/forgot-password/status");
                die();
            }

            $userService = new UserService($user);
            $userService->forgotPassword();
            redirect("/forgot-password/status");
            die();
        }

        $this->saveInSession('forgot-password-form', $forgotPassForm);
        redirect("/forgot-password");
        die();
    }

    public function resetPassword()
    {
        $req = Application::request();
        $token = $req->data['csrf_token'];

        if (! AuthService::checkCSRF($token)) {
            $this->forbidAndExit();
        }

        $resetPassForm = new ResetPasswordForm();
        $resetPassForm->load($req->data);
        $isValid = $resetPassForm->validate();
        $resetPassToken = $req->data['reset_pass_token'];

        if ($isValid) {
            $userService = new UserService();

            if (! $userService->resetPassword($resetPassToken, $resetPassForm->password, $resetPassForm->confirmPassword)) {
                redirect("/reset-password/invalid");
                die();
            }

            redirect("/reset-password/success");
            die();
        } 

        $this->saveInSession('reset-password-form', $resetPassForm);
        redirect("/reset-password?token={$resetPassToken}");
        die();
    }

    public function logout()
    {
        AuthService::logout();
        redirect("/login");
        die();
    }
}
