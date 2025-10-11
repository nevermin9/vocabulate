<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\AbstractController;
use App\Core\Enums\HttpMethod;
use App\Core\Request;
use App\Core\Route;
use App\Middleware\AuthMiddleware;
use App\Middleware\VerificationMiddleware;
use App\Services\AuthService;
use App\Services\StackService;

class StackController extends AbstractController
{
    public function __construct(
        protected AuthService $auth,
        protected StackService $stackService
    )
    {
    }

    #[Route(method: HttpMethod::POST, path: "/stack/create", middleware: [AuthMiddleware::class, VerificationMiddleware::class])]
    public function createStack(Request $req): never
    {
        $userId = $this->auth->getUser()?->idBin;
        $langCode = $req->data['stack-language'] ?? null;
        $stackName = $req->data['stack-name'] ?? null;

        if ($langCode && $stackName && $userId) {
            $this->stackService->createStack($userId, $stackName, $langCode);
            redirect("/");
            die();
        }

        http_response_code(400);
        die();
    }
}
