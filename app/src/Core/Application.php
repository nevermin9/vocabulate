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
        static::$auth = new AuthService(static::session());
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
            $this->handleError($e);
        }
    }

    private function handleError(\Throwable $e): void
    {
        // Better error handling
        // error_log($e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
        
        // if ($this->config->debug) {
        if (static::config()->app['is-dev']) {
            echo "Error: " . $e->getMessage() . "<br>";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
            echo "Trace: <pre>" . $e->getTraceAsString() . "</pre>";
        } else {
            echo "An error occurred. Please try again later.";
        }
    }
}
