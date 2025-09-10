<?php
declare(strict_types=1);

namespace App;

use App\Services\Router;
use App\Services\Request;

// class DataBase;

final class Application
{
    public function __construct(
        private Router $router,
    )
    {
    }

    public function run(Request $request): void
    {
        try {
            echo $this->router->resolve($request->method, $request->uri);
        } catch (\Throwable $e) {
            echo "error: ";
            echo $e->getMessage();
        }
    }
}
