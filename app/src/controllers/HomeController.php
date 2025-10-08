<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Core\Application;
use App\Core\Enums\HttpMethod;
use App\Core\Request;
use App\Core\Route;
use App\Models\Language;
use App\Models\Stack;
use App\Services\AuthService;
use App\Services\StackService;
use App\Middleware\AuthMiddleware;
use App\Middleware\VerificationMiddleware;

class HomeController
{
    public function __construct(
        protected AuthService $auth
    )
    {
    }

    #[Route(method: HttpMethod::GET, path: "/", middleware: [AuthMiddleware::class, VerificationMiddleware::class])]
    public function index(): View
    {
        $stacks = Stack::getAll($this->auth->getUserId());
        $langs = Language::getAll();

        return View::make("index", [
            "stacks" => $stacks,
            "languages" => $langs
        ]);
    }

    #[Route(method: HttpMethod::POST, path: "/stack/create", middleware: [AuthMiddleware::class, VerificationMiddleware::class])]
    public function createStack(Request $req)
    {
        // $userId = Application::authService()->getUserId();
        // new StackService()->createStack($userId, $req->data['stack-name'], $req->data['stack-language']);
        redirect("/");
        die();
    }
}
