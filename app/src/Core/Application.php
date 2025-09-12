<?php
declare(strict_types=1);

namespace App\Core;

use App\Core\Router;
use App\Core\Request;
use App\Core\Config;
use App\Core\DB;

final class Application
{
    private static Request $request;
    private static Config $config;
    private static DB $db;

    public function __construct(private Router $router, Config $config)
    {
        static::$request = new Request();
        static::$config = $config;
        static::$db = new DB(static::$config->db);
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

    public function run(): void
    {
        try {
            echo $this->router->resolve(static::$request->method, static::$request->uri);
        } catch (\Throwable $e) {
            echo "error: ";
            echo $e->getMessage();
        }
    }
}
