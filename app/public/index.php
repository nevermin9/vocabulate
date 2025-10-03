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

define('VIEWS_DIR', dirname(__DIR__) . "/src/Views");
define('LAYOUTS_DIR', dirname(__DIR__) . "/src/Views/_layouts");

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$router = new Router();

$router
    ->get("/", [HomeController::class, "index"])
    ->get("/registration", [AuthController::class, "registrationView"])
    ->post("/registration", [AuthController::class, "register"])
    ->get("/login", [AuthController::class, "loginView"])
    ->post("/login", [AuthController::class, "login"])
    ->get("/stack/:id", [StackOverviewController::class, "index"])
    ->get("/logout", [AuthController::class, "logout"])
    ->post("/stack/create", [HomeController::class, "createStack"])
    ->post("/stack/:stackId/add-flashcard", [StackOverviewController::class, "addFleshcard"])
    ->get("/forgot-password", [AuthController::class, "forgotPasswordView"])
    ->post("/forgot-password", [AuthController::class, "forgotPassword"])
    ->get("/reset-password", [AuthController::class, "resetPasswordView"])
    ->post("/reset-password", [AuthController::class, "resetPassword"])
    ->get("/forgot-password/status", [AuthController::class, "forgotPasswordSentView"])
    ->get("/reset-password/invalid", [AuthController::class, "resetPasswordInvalidView"])
    ->get("/reset-password/success", [AuthController::class, "resetPasswordSuccessView"])
;


new Application($router, new Config($_ENV))->run();

