<?php
declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

require_once __DIR__ . "/../src/helpers/init.php";

use Dotenv\Dotenv;
use App\Core\Application;
use App\Core\Config;
use App\Core\Request;
use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\RegistrationController;
use App\Controllers\StackOverviewController;




use App\Core\View;
use App\Services\MailService;

define('VIEWS_DIR', __DIR__ . "/../src/Views");

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$router = new Router();

class TestMail
{
    public function index()
    {
        return View::make("test-mail");
    }

    public function handle()
    {
        $mail = new MailService();
        $mail->setRecipients(
            ['address' => 'fooo@gmail.com', 'name' => 'Your Daddy'],
            ['address' => $_POST['address'], $_POST['name']]
        );
        $mail->fillHTML('Subject', 'Click <a href="google.com">here</a>;');
        $mail->send();
    }
}

$router
    ->get("/", [HomeController::class, "index"])
    ->get("/registration", [RegistrationController::class, "index"])
    ->post("/register", [RegistrationController::class, "register"])
    ->get("/login", [LoginController::class, "index"])
    ->post("/login", [LoginController::class, "login"])
    ->get("/stack/:id", [StackOverviewController::class, "index"])
    ->get("/logout", [LoginController::class, "logout"])
    ->post("/stack/create", [HomeController::class, "createStack"])
    ->post("/stack/:stackId/add-flashcard", [StackOverviewController::class, "addFleshcard"])
    ->get("/test-email", [TestMail::class, "index"])
    ->post("/test-email", [TestMail::class, "handle"]);


new Application($router, new Config($_ENV))->run();

