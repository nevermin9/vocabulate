<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Core\Application;

class HomeController
{
    public function index(): View
    {
        $req = Application::request();

        if (! isset($req->headers['AUTHORIZATION'])) {
            redirect("/login", 302);
        }

        return View::make("index", ["documentTitle" => "lol"]);
    }
}
