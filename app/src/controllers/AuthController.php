<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\AbstractController;
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

final class AuthController extends AbstractController
{
    use SessionStoreTrait;
    use CSRFGuardTrait;

    public function __construct()
    {
        $this->setLayout('guest-view');
    }

    public function loginView(): View
    {
        $loginForm = $this->getAndClearFromSession('login-form');

        return $this->renderView("login", [
            "csrf_token" => AuthService::getCSRF(),
            "model" => $loginForm?->getFormModel() ?? null,
            "errors" => $loginForm?->errors ?? null,
        ]);
    }

    public function registrationView(): View
    {
        $regForm = $this->getAndClearFromSession('registration-form');

        return $this->renderView("registration", [
            "csrf_token" => AuthService::getCSRF(),
            "password_rules" => new RegistrationForm()->getPasswordMessages(),
            "model" => $regForm?->getFormModel() ?? null,
            "errors" => $regForm?->errors ?? null,
        ]);
    }

    public function forgotPasswordView(): View
    {
        $forgotPassForm = $this->getAndClearFromSession('forgot-password-form');

        return $this->renderView("forgot-password", [
            "csrf_token" => AuthService::getCSRF(),
            "model" => $forgotPassForm?->getFormModel() ?? null,
            "errors" => $forgotPassForm?->errors ?? null,
        ]);
    }

    public function resetPasswordView(): View
    {
        $resetPasswordForm = $this->getAndClearFromSession("reset-password-form");
        $req = Application::request();

        if (! $resetPasswordForm) {
            $userService = new UserService();

            if (! $userService->checkResetPasswordToken($req->data['token'] ?? '')) {
                redirect("/reset-password/invalid");
                die();
            }
        }

        return $this->renderView("reset-password", [
            "csrf_token" => AuthService::getCSRF(),
            "model" => $resetPasswordForm?->getFormModel() ?? null,
            "errors" => $resetPasswordForm?->errors ?? null,
            "password_rules" => new ResetPasswordForm()->getPasswordMessages(),
            "token" => $req->data['token']
        ]);
    }

    public function forgotPasswordSentView()
    {
        return $this->renderView("forgot-password-sent");
    }

    public function resetPasswordInvalidView()
    {
        return $this->renderView("reset-password-invalid");
    }

    public function resetPasswordSuccessView()
    {
        return $this->renderView("reset-password-success");
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

            if ($loginForm->validateUserPassword($user)) {
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
