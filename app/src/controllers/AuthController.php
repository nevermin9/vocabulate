<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\AbstractController;
use App\Core\Enums\HttpMethod;
use App\Core\Request;
use App\Core\Route;
use App\Core\Session;
use App\Core\View;
use App\Forms\ForgotPasswordForm;
use App\Forms\LoginForm;
use App\Forms\RegistrationForm;
use App\Forms\ResetPasswordForm;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
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

    #[Route(method: HttpMethod::GET, path: "/login", middleware: [GuestMiddleware::class])]
    public function loginView(): View
    {
        $loginForm = $this->session->getFlash('login-form');

        return $this->renderView("login", [
            "csrf_token" => $this->auth->getCSRF(),
            "model" => $loginForm?->getFormModel() ?? null,
            "errors" => $loginForm?->errors ?? null,
        ]);
    }

    #[Route(method: HttpMethod::GET, path: "/registration", middleware: [GuestMiddleware::class])]
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

    #[Route(method: HttpMethod::GET, path: "/forgot-password", middleware: [GuestMiddleware::class])]
    public function forgotPasswordView(): View
    {
        $forgotPassForm = $this->session->getFlash('forgot-password-form');

        return $this->renderView("forgot-password", [
            "csrf_token" => $this->auth->getCSRF(),
            "model" => $forgotPassForm?->getFormModel() ?? null,
            "errors" => $forgotPassForm?->errors ?? null,
        ]);
    }

    #[Route(method: HttpMethod::GET, path: "/reset-password", middleware: [GuestMiddleware::class])]
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

    #[Route(method: HttpMethod::GET, path: "/forgot-password/status", middleware: [GuestMiddleware::class])]
    public function forgotPasswordSentView(): View
    {
        return $this->renderView("forgot-password-sent");
    }

    #[Route(method: HttpMethod::GET, path: "/reset-password/invalid", middleware: [GuestMiddleware::class])]
    public function resetPasswordInvalidView(): View
    {
        return $this->renderView("reset-password-invalid");
    }

    #[Route(method: HttpMethod::GET, path: "/reset-password/success", middleware: [GuestMiddleware::class])]
    public function resetPasswordSuccessView(): View
    {
        return $this->renderView("reset-password-success");
    }

    #[Route(method: HttpMethod::POST, path: "/login", middleware: [GuestMiddleware::class])]
    public function login(Request $req): never
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

    #[Route(method: HttpMethod::POST, path: "/registration", middleware: [GuestMiddleware::class])]
    public function register(Request $req): never
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

    #[Route(method: HttpMethod::POST, path: "/forgot-password", middleware: [GuestMiddleware::class])]
    public function forgotPassword(Request $req): never
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

    #[Route(method: HttpMethod::POST, path: "/reset-password", middleware: [GuestMiddleware::class])]
    public function resetPassword(Request $req): never
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

    #[Route(method: HttpMethod::GET, path: "/logout", middleware: [AuthMiddleware::class])]
    public function logout(): never
    {
        $this->auth->logout();
        redirect("/login");
        die();
    }
}
