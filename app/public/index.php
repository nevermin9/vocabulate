<?php
declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

require_once __DIR__ . "/../src/helpers/init.php";

use App\Controllers\AuthController;
use App\Controllers\ForgotPasswordController;
use Dotenv\Dotenv;
use App\Core\Application;
use App\Core\Config;
use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\StackOverviewController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

define('VIEWS_DIR', dirname(__DIR__) . "/src/Views");
define('LAYOUTS_DIR', dirname(__DIR__) . "/src/Views/_layouts");

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$router = new Router();

$router
    // user's routes
    ->get("/", [HomeController::class, "index"], [AuthMiddleware::class])
    ->get("/stack/:id", [StackOverviewController::class, "index"], [AuthMiddleware::class])
    ->get("/logout", [AuthController::class, "logout"], [AuthMiddleware::class])
    ->post("/stack/create", [HomeController::class, "createStack"], [AuthMiddleware::class])
    ->post("/stack/:stackId/add-flashcard", [StackOverviewController::class, "addFleshcard"], [AuthMiddleware::class])

    // anonymous's routes
    ->get("/registration", [AuthController::class, "registrationView"], [GuestMiddleware::class])
    ->post("/registration", [AuthController::class, "register"], [GuestMiddleware::class])
    ->get("/login", [AuthController::class, "loginView"], [GuestMiddleware::class])
    ->post("/login", [AuthController::class, "login"], [GuestMiddleware::class])
    ->get("/forgot-password", [AuthController::class, "forgotPasswordView"], [GuestMiddleware::class])
    ->post("/forgot-password", [AuthController::class, "forgotPassword"], [GuestMiddleware::class])
    ->get("/reset-password", [AuthController::class, "resetPasswordView"], [GuestMiddleware::class])
    ->post("/reset-password", [AuthController::class, "resetPassword"], [GuestMiddleware::class])
    ->get("/forgot-password/status", [AuthController::class, "forgotPasswordSentView"], [GuestMiddleware::class])
    ->get("/reset-password/invalid", [AuthController::class, "resetPasswordInvalidView"], [GuestMiddleware::class])
    ->get("/reset-password/success", [AuthController::class, "resetPasswordSuccessView"], [GuestMiddleware::class])
;


new Application($router, new Config($_ENV))->run();

