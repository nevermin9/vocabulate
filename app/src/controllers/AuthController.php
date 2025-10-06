<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\AbstractController;
use App\Core\Application;
use App\Core\Request;
use App\Core\View;
use App\Forms\ForgotPasswordForm;
use App\Forms\LoginForm;
use App\Forms\RegistrationForm;
use App\Forms\ResetPasswordForm;
use App\Models\ForgotPasswordToken;
use App\Models\User;
use App\Services\UserService;

final class AuthController extends AbstractController
{
    public function __construct()
    {
        $this->setLayout('guest-view');
    }

    public function loginView(): View
    {
        $loginForm = Application::session()->getFlash('login-form');
        $auth = Application::authService();

        return $this->renderView("login", [
            "csrf_token" => $auth->getCSRF(),
            "model" => $loginForm?->getFormModel() ?? null,
            "errors" => $loginForm?->errors ?? null,
        ]);
    }

    public function registrationView(): View
    {
        $regForm = Application::session()->getFlash('registration-form');
        $auth = Application::authService();

        return $this->renderView("registration", [
            "csrf_token" => $auth->getCSRF(),
            "password_rules" => new RegistrationForm()->getPasswordMessages(),
            "model" => $regForm?->getFormModel() ?? null,
            "errors" => $regForm?->errors ?? null,
        ]);
    }

    public function forgotPasswordView(): View
    {
        $forgotPassForm = Application::session()->getFlash('forgot-password-form');
        $auth = Application::authService();

        return $this->renderView("forgot-password", [
            "csrf_token" => $auth->getCSRF(),
            "model" => $forgotPassForm?->getFormModel() ?? null,
            "errors" => $forgotPassForm?->errors ?? null,
        ]);
    }

    public function resetPasswordView(Request $req): View
    {
        $resetPasswordForm = Application::session()->getFlash("reset-password-form");
        $token = $req->data['token'] ?? '';

        if (! $resetPasswordForm) {
            $userService = new UserService();

            if (! $userService->checkToken($token, ForgotPasswordToken::class)) {
                redirect("/reset-password/invalid");
                die();
            }
        }

        $auth = Application::authService();

        return $this->renderView("reset-password", [
            "csrf_token" => $auth->getCSRF(),
            "model" => $resetPasswordForm?->getFormModel() ?? null,
            "errors" => $resetPasswordForm?->errors ?? null,
            "password_rules" => new ResetPasswordForm()->getPasswordMessages(),
            "reset_pass_token" => $token
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

    public function login(Request $req)
    {
        $loginForm = new LoginForm();
        $loginForm->load($req->data);
        $isValid = $loginForm->validate();
        $auth = Application::authService();

        if ($isValid) {
            $user = User::findOne(["email" => $loginForm->email]);

            if ($loginForm->validateUserPassword($user)) {
                $auth->login($user);
                redirect("/");
                die();
            }
        }

        Application::session()->setFlash('login-form', $loginForm);
        redirect("/login");
        die();
    }

    public function register(Request $req)
    {
        $regForm = new RegistrationForm();
        $regForm->load($req->data);
        $isValid = $regForm->validate();
        $auth = Application::authService();

        if ($isValid) {
            $userService = new UserService();
            $user = $userService->register($regForm->email, $regForm->password);
            $auth->login($user);
            redirect("/");
            die();
        }

        Application::session()->setFlash('registration-form', $regForm);
        redirect("/registration");
        die();
    }

    public function forgotPassword(Request $req)
    {
        $forgotPassForm = new ForgotPasswordForm();
        $forgotPassForm->load($req->data);
        $isValid = $forgotPassForm->validate();

        if ($isValid) {
            $user = User::findOne(["email" => $forgotPassForm->email]);
            if (! $user) {
                redirect("/forgot-password/status");
                die();
            }

            $userService = new UserService($user);
            $userService->forgotPassword();
            redirect("/forgot-password/status");
            die();
        }

        Application::session()->setFlash('forgot-password-form', $forgotPassForm);
        redirect("/forgot-password");
        die();
    }

    public function resetPassword(Request $req)
    {
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

        Application::session()->setFlash('reset-password-form', $resetPassForm);
        redirect("/reset-password?token={$resetPassToken}");
        die();
    }

    public function logout()
    {
        Application::authService()->logout();
        redirect("/login");
        die();
    }
}
