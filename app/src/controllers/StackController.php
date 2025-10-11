<?php
declare(strict_types=1);

use App\Core\AbstractController;
use App\Core\Enums\HttpMethod;
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
    public function createStack()
    {
        $userId = $this->auth->userId;
        var_dump($userId);

    }
}
