<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\Enums\HttpMethod;
use App\Core\Route;
use App\Core\View;
use App\Models\Flashcard;
use App\Services\AuthService;
use App\Services\FlashcardService;
use App\Middleware\AuthMiddleware;
use App\Middleware\VerificationMiddleware;

final class StackOverviewController
{
    #[Route(method: HttpMethod::GET, path: "/stack/:id", middleware: [AuthMiddleware::class, VerificationMiddleware::class])]
    public function index(string $id): View
    {
        if (! AuthService::isAuthenticated()) {
            redirect("/login");
        }

        $flashcards = Flashcard::getAllByStack((int) $id);

        return View::make("stack-overview", [
            "flashcards" => $flashcards,
            "stackId" => $id,
        ]);
    }

    #[Route(method: HttpMethod::POST, path: "/stack/:stackId/add-flashcard", middleware: [AuthMiddleware::class, VerificationMiddleware::class])]
    public function addFleshcard(string $stackId)
    {
        $req = Application::request();

        new FlashcardService()->addNewFlashcard(
            (int) $stackId,
            $req->data['word'],
            $req->data['translation'],
            $req->data['example-usage'],
            $req->data['example-usage-translation']
        );

        redirect("/stack/" . $stackId);
        die();
    }
}
