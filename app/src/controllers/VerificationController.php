<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\AbstractController;
use App\Core\Application;
use App\Core\Enums\HttpMethod;
use App\Core\Request;
use App\Core\Route;
use App\Core\View;
use App\Middleware\AuthMiddleware;
use App\Middleware\VerificationMiddleware;
use App\Services\AuthService;
use App\Services\UserService;

class VerificationController extends AbstractController
{
    public function __construct(
        protected AuthService $auth,
        protected UserService $userService
    )
    {
        $this->setLayout('guest-view');
    }

    #[Route(method: HttpMethod::GET, path: "/verification/pending", middleware: [AuthMiddleware::class])]
    public function indexView(): View
    {
        return $this->renderView("verification-pending", [
            "csrf_token" => $this->auth->getCSRF(),
        ]);
    }

    #[Route(method: HttpMethod::GET, path: "/verification/invalid")]
    public function verificationInvalidView(): View
    {
        return $this->renderView("verification-invalid", [
            "is-auth" => $this->auth->isAuthenticated(),
            "csrf_token" => $this->auth->getCSRF(),
        ]);
    }

    #[Route(method: HttpMethod::GET, path: "/verification/success", middleware: [AuthMiddleware::class, VerificationMiddleware::class])]
    public function verificationSuccessView(): View
    {
        return $this->renderView("verification-success");
    }

    #[Route(method: HttpMethod::POST, path: "/verifiction/send", middleware: [AuthMiddleware::class])]
    public function sendVerificationLink(Request $req)
    {
        $redirect = $req->data['redirect_back'];
        $this->userService->sendVerificationLink($this->auth->getUser());
        redirect($redirect);
        die();
    }

    #[Route(method: HttpMethod::GET, path: "/verify")]
    public function verify(Request $req)
    {
        $token = $req->data['token'] ?? '';
        $isValid = $this->userService->validateVerificationToken($token);

        if (!$isValid) {
            redirect("/verification/invalid");
            die();
        }

        $user = $this->userService->verifyUser($token);

        if (! $user) {
            redirect("/verification/invalid");
            die();
        }


        if (! $this->auth->isAuthenticated()) {
            $this->auth->login($user);
        }

        redirect("/verification/success");
        die();
    }
}
