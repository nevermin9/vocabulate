<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\AbstractController;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Forms\ForgotPasswordForm;
use App\Forms\LoginForm;
use App\Forms\RegistrationForm;
use App\Forms\ResetPasswordForm;
use App\Repositories\User\UserRepository;
use App\Services\AuthService;
use App\Services\UserService;

final class AuthController extends AbstractController
{
    public function __construct(
        protected Session $session,
        protected AuthService $auth, 
        protected UserService $userService,
        protected UserRepository $userRepository
    )
    {
        $this->setLayout('guest-view');
    }

    public function loginView(): View
    {
        $loginForm = $this->session->getFlash('login-form');

        return $this->renderView("login", [
            "csrf_token" => $this->auth->getCSRF(),
            "model" => $loginForm?->getFormModel() ?? null,
            "errors" => $loginForm?->errors ?? null,
        ]);
    }

    public function registrationView(): View
    {
        $regForm = $this->session->getFlash('registration-form');

        return $this->renderView("registration", [
            "csrf_token" => $this->auth->getCSRF(),
            "password_rules" => new RegistrationForm()->getPasswordMessages(),
            "model" => $regForm?->getFormModel() ?? null,
            "errors" => $regForm?->errors ?? null,
        ]);
    }

    public function forgotPasswordView(): View
    {
        $forgotPassForm = $this->session->getFlash('forgot-password-form');

        return $this->renderView("forgot-password", [
            "csrf_token" => $this->auth->getCSRF(),
            "model" => $forgotPassForm?->getFormModel() ?? null,
            "errors" => $forgotPassForm?->errors ?? null,
        ]);
    }

    public function resetPasswordView(Request $req): View
    {
        $resetPasswordForm = $this->session->getFlash("reset-password-form");
        $token = $req->data['token'] ?? '';

        if (! $resetPasswordForm) {
            if (! $this->userService->validateForgotPasswordToken($token)) {
                redirect("/reset-password/invalid");
                die();
            }
        }

        return $this->renderView("reset-password", [
            "csrf_token" => $this->auth->getCSRF(),
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

        if ($isValid) {
            $user = $this->userRepository->findByEmail($loginForm->email);

            if ($loginForm->validateUserPassword($user)) {
                $this->auth->login($user);
                redirect("/");
                die();
            }
        }

        $this->session->setFlash('login-form', $loginForm);
        redirect("/login");
        die();
    }

    public function register(Request $req)
    {
        $regForm = new RegistrationForm();
        $regForm->load($req->data);
        $isValid = $regForm->validate();

        if ($isValid) {
            $user = $this->userService->register($regForm->email, $regForm->password);
            $this->auth->login($user);
            redirect("/");
            die();
        }

        $this->session->setFlash('registration-form', $regForm);
        redirect("/registration");
        die();
    }

    public function forgotPassword(Request $req)
    {
        $forgotPassForm = new ForgotPasswordForm();
        $forgotPassForm->load($req->data);
        $isValid = $forgotPassForm->validate();

        if ($isValid) {
            $user = $this->userRepository->findByEmail($forgotPassForm->email);
            if (! $user) {
                redirect("/forgot-password/status");
                die();
            }

            $this->userService->sendResetTokenLink($user);
            redirect("/forgot-password/status");
            die();
        }

        $this->session->setFlash('forgot-password-form', $forgotPassForm);
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
            if (! $this->userService->resetPassword($resetPassToken, $resetPassForm->password, $resetPassForm->confirmPassword)) {
                redirect("/reset-password/invalid");
                die();
            }

            redirect("/reset-password/success");
            die();
        } 

        $this->session->setFlash('reset-password-form', $resetPassForm);
        redirect("/reset-password?token={$resetPassToken}");
        die();
    }

    public function logout()
    {
        $this->auth->logout();
        redirect("/login");
        die();
    }
}
