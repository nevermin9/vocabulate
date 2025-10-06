<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
    }

    public function test_get_registers_route_without_middleware()
    {
        $this->router->get('/public', fn() => 'public');
        
        $routes = $this->router->getRouter();
        
        $this->assertEquals([], $routes['get']['/public']['middleware']);
    }

    public function test_get_registers_route_with_multiple_middleware()
    {
        $this->router->get('/admin', ['AdminController', 'index'], ['Auth', 'Admin', 'Log']);
        
        $routes = $this->router->getRouter();
        
        $this->assertEquals(['Auth', 'Admin', 'Log'], $routes['get']['/admin']['middleware']);
    }
}
