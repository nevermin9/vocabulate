<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Core\Application;
use App\Models\Language;
use App\Models\Stack;
use App\Services\StackService;

class HomeController
{
    public function index(): View
    {
        $stacks = Stack::getAll(Application::authService()->getUserId());
        $langs = Language::getAll();

        return View::make("index", [
            "documentTitle" => "lol",
            "stacks" => $stacks,
            "languages" => $langs
        ]);
    }

    public function createStack()
    {
        $req = Application::request();
        $userId = Application::authService()->getUserId();
        new StackService()->createStack($userId, $req->data['stack-name'], $req->data['stack-language']);
        redirect("/");
        die();
    }
}
