<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\AbstractController;
use App\Core\View;
use App\Core\Get;
use App\Services\AuthService;
use App\Middleware\AuthMiddleware;
use App\Middleware\VerificationMiddleware;
use App\Repositories\Language\LanguageRepositoryInterface;
use App\Repositories\Stack\StackRepositoryInterface;

class HomeController extends AbstractController
{
    public function __construct(
        protected AuthService $auth,
        protected StackRepositoryInterface $stackRepo,
        protected LanguageRepositoryInterface $langRepo
    )
    {
    }

    #[Get(path: "/", middleware: [AuthMiddleware::class, VerificationMiddleware::class])]
    public function index(): View
    {
        $langs = $this->langRepo->getAllAsc();
        $stacks = $this->stackRepo->getAllAsc($this->auth->getUser()->idBin);

        return $this->renderView("index", [
            "stacks" => $stacks,
            "languages" => $langs,
            "csrf_token" => $this->auth->csrfToken,
        ]);
    }
}
