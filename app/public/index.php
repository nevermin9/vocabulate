<?php
declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

require_once __DIR__ . "/../src/helpers/init.php";

use App\Controllers\AuthController;
use Dotenv\Dotenv;
use App\Core\Application;
use App\Core\Config;
use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\StackOverviewController;
use App\Controllers\VerificationController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CSRFTokenMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\VerificationMiddleware;

define('VIEWS_DIR', dirname(__DIR__) . "/src/Views");
define('LAYOUTS_DIR', dirname(__DIR__) . "/src/Views/_layouts");

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$router = new Router();

$router
    ->registerGlobalMiddleware([CSRFTokenMiddleware::class])
    // user's routes
    ->get("/", [HomeController::class, "index"], [AuthMiddleware::class, VerificationMiddleware::class])
    ->get("/stack/:id", [StackOverviewController::class, "index"], [AuthMiddleware::class, VerificationMiddleware::class])
    ->post("/stack/create", [HomeController::class, "createStack"], [AuthMiddleware::class, VerificationMiddleware::class])
    ->post("/stack/:stackId/add-flashcard", [StackOverviewController::class, "addFleshcard"], [AuthMiddleware::class, VerificationMiddleware::class])
    ->get("/logout", [AuthController::class, "logout"], [AuthMiddleware::class])
    ->get(VerificationMiddleware::getVerificationURL(), [VerificationController::class, "indexView"], [AuthMiddleware::class])
    ->post("/verifiction/send", [VerificationController::class, "sendVerificationLink"], [AuthMiddleware::class])
    ->get("/verify", [VerificationController::class, "verify"])
    ->get("/verification/invalid", [VerificationController::class, "verificationInvalidView"])
    ->get("/verification/success", [VerificationController::class, "verificationSuccessView"], [AuthMiddleware::class, VerificationMiddleware::class])

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

