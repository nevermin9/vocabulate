<?php
declare(strict_types=1);

namespace App\Core;

use App\Core\Router;
use App\Core\Request;
use App\Core\Config;
use App\Core\DB;
use App\Core\Session;
use App\Services\AuthService;

final class Application
{
    private static Request $request;
    private static Config $config;
    private static DB $db;
    private static Session $session;
    private static AuthService $auth;

    public function __construct(private Router $router, Config $config)
    {
        static::$request = new Request();
        static::$config = $config;
        static::$db = new DB(static::$config->db);
        static::$session = new Session();
        static::$auth = new AuthService();
    }

    public static function request(): Request
    {
        return static::$request;
    }

    public static function config(): Config
    {
        return static::$config;
    }

    public static function db(): DB
    {
        return static::$db;
    }

    public static function session(): Session
    {
        return static::$session;
    }

    public static function authService(): AuthService
    {
        return static::$auth;
    }

    public function run(): void
    {
        try {
            echo $this->router->resolve(static::$request);
        } catch (\Throwable $e) {
            echo "error: ";
            echo $e->getFile() . $e->getLine() . "<br>" . $e->getTrace() . "<br>" . $e->getMessage();
        }
    }
}
