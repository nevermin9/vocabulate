<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\View;
use App\Models\Flashcard;
use App\Services\AuthService;
use App\Services\FlashcardService;

final class StackOverviewController
{
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
