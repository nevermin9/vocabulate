<?php
declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

use App\Application;
use App\Controllers\HomeController;
use App\Services\Request;
use App\Services\Router;

define('VIEWS_DIR', __DIR__ . "/../views");

$router = new Router();

$router
    ->get("/", [HomeController::class, "index"]);

new Application($router)->run(new Request());

