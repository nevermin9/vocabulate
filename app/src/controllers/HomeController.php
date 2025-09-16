<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Core\Application;
use App\Models\Language;
use App\Models\Stack;
use App\Services\AuthService;
use App\Services\StackService;

class HomeController
{
    public function index(): View
    {
        if (! AuthService::isAuthenticated()) {
            redirect("/login", 302);
        }

        $stacks = Stack::getAll(AuthService::getUserId());
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
        $userId = AuthService::getUserId();
        new StackService()->createStack($userId, $req->data['stack-name'], $req->data['stack-language']);
        redirect("/");
        die();
    }
}
