<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\AbstractController;
use App\Core\Application;
use App\Core\Request;
use App\Core\View;
use App\Models\VerificationToken;
use App\Services\UserService;

class VerificationController extends AbstractController
{
    public function __construct()
    {
        $this->setLayout('guest-view');
    }

    public function indexView(): View
    {
        return $this->renderView("verification-pending", [
            "csrf_token" => Application::authService()->getCSRF(),
        ]);
    }

    public function verificationInvalidView(): View
    {
        return $this->renderView("verification-invalid", [
            "is-auth" => Application::authService()->isAuthenticated(),
            "csrf_token" => Application::authService()->getCSRF(),
        ]);
    }

    public function verificationSuccessView(): View
    {
        return $this->renderView("verification-success");
    }

    public function sendVerificationLink(Request $req)
    {
        $redirect = $req->data['redirect_back'];
        $userService = new UserService(Application::authService()->getUser());
        $userService->sendVerificationLink();
        redirect($redirect);
        die();
    }

    public function verify(Request $req)
    {
        $token = $req->data['token'] ?? '';
        $userService = new UserService();
        $isValid = $userService->checkToken($token, VerificationToken::class);

        if (!$isValid) {
            redirect("/verification/invalid");
            die();
        }

        $user = $userService->verifyUser($token);

        if (! $user) {
            redirect("/verification/invalid");
            die();
        }

        $auth = Application::authService();

        if (! $auth->isAuthenticated()) {
            $auth->login($user);
        }

        redirect("/verification/success");
        die();
    }
}
