<?php
declare(strict_types=1);

namespace App\Core;

use App\Core\Router;
use App\Core\Request;
use App\Core\RequestFactory;
use App\Core\Config;
use App\Core\DB;
use App\Core\Session;
use App\Repositories\Language\LanguageRepository;
use App\Repositories\Language\LanguageRepositoryInterface;
use App\Repositories\Stack\StackRepository;
use App\Repositories\Stack\StackRepositoryInterface;
use App\Repositories\Token\TokenRepository;
use App\Repositories\Token\TokenRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\AuthService;

final class Application
{
    private static Application $instance;
    protected Request $request;

    public function __construct(
        public readonly Container $container,
        protected Router $router,
        protected Config $config
    )
    {
        static::$instance = $this;
        $this->request = RequestFactory::createFromGlobals();
        $this->container->bind(Config::class, fn() => $this->config, true);
        $this->container->bind(DB::class, fn() => new DB($this->config->db), true);
        $this->container->bind(Session::class, fn() => new Session(), true);
        $this->container->bind(AuthService::class, AuthService::class, true);
        $this->container->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->container->bind(TokenRepositoryInterface::class, TokenRepository::class);
        $this->container->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
        $this->container->bind(StackRepositoryInterface::class, StackRepository::class);
    }

    public static function app(): ?Application
    {
        return static::$instance;
    }

    public function run(): void
    {
        try {
            echo $this->router->resolve($this->request);
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }

    private function handleError(\Throwable $e): void
    {
        // Better error handling
        // error_log($e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
        
        if ($this->config->app['is-dev']) {
            echo "Error: " . $e->getMessage() . "<br>";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
            echo "Trace: <pre>" . $e->getTraceAsString() . "</pre>";
        } else {
            echo "An error occurred. Please try again later.";
        }
    }
}
