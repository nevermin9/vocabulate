<?php
declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

use App\Application;
use App\Request;
use App\Router;
use App\Controllers\HomeController;
use Dotenv\Dotenv;

define('VIEWS_DIR', __DIR__ . "/../views");

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$router = new Router();

$router
    ->get("/", [HomeController::class, "index"]);

new Application($router)->run();

