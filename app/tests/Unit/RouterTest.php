<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Container;
use App\Core\Enums\HttpMethod;
use App\Core\Request;
use App\Core\RequestFactory;
use App\Core\Route;
use App\Core\Router;
use App\Exceptions\NotFoundException;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $container = $this->createStub(Container::class);
        $container->method('get')->willReturnCallback(function($class) {
            if (!class_exists($class)) {
                throw new \RuntimeException("Class $class does not exist");
            }
            
            // Return instances of test classes
            return match($class) {
                TestIndexController::class => new TestIndexController(),
                AllowMiddleware::class => new AllowMiddleware(),
                BlockMiddleware::class => new BlockMiddleware(),
                default => new $class()
            };
        });
        
        $this->router = new Router($container);
    }

    public function test_register_global_middleware()
    {
        $result = $this->router->registerGlobalMiddleware([BlockMiddleware::class, AllowMiddleware::class]);
        
        $this->assertInstanceOf(Router::class, $result);
        $this->assertSame([BlockMiddleware::class, AllowMiddleware::class], $this->router->getGlobalMiddleware());
    }

    public function test_register_controllers_creates_routes_from_attributes()
    {
        $this->router->registerControllers([TestControllerWithAttributes::class]);
        
        $routes = $this->router->getRoutes();
        
        $this->assertArrayHasKey('GET', $routes);
        $this->assertArrayHasKey('/test', $routes['GET']);
        $this->assertEquals([TestControllerWithAttributes::class, 'index'], $routes['GET']['/test']['action']);
    }

    public function test_register_controllers_handles_middleware()
    {
        $this->router->registerControllers([TestControllerWithMiddleware::class]);
        
        $routes = $this->router->getRoutes();
        
        $this->assertArrayHasKey('GET', $routes);
        $this->assertEquals([AllowMiddleware::class], $routes['GET']['/protected']['middleware']);
    }

    public function test_register_controllers_handles_multiple_routes_per_method()
    {
        $this->router->registerControllers([TestControllerWithMultipleRoutes::class]);
        
        $routes = $this->router->getRoutes();
        
        $this->assertArrayHasKey('GET', $routes);
        $this->assertArrayHasKey('POST', $routes);
        $this->assertArrayHasKey('/users', $routes['GET']);
        $this->assertArrayHasKey('/users', $routes['POST']);
    }

    public function test_resolve_exact_route_with_closure()
    {
        $req = RequestFactory::create(
            method: 'GET',
            path: '/hello',
        );
        
        // Manually register a closure route
        $reflection = new \ReflectionClass($this->router);
        $registerMethod = $reflection->getMethod('register');
        $registerMethod->setAccessible(true);
        $registerMethod->invoke($this->router, 'GET', '/hello', fn() => 'Hello World', []);
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('Hello World', $result);
    }

    public function test_resolve_route_with_array_callable()
    {
        $req = RequestFactory::create(
            method: 'GET',
            path: '/test',
        );
        
        $this->router->registerControllers([TestControllerWithAttributes::class]);
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('test index', $result);
    }

    public function test_resolve_route_with_single_parameter()
    {
        $req = RequestFactory::create(
            method: 'GET',
            path: '/users/123',
        );
        
        $this->router->registerControllers([TestControllerWithParams::class]);
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('User ID: 123', $result);
    }

    public function test_resolve_route_with_multiple_parameters()
    {
        $req = RequestFactory::create(
            method: 'GET',
            path: '/users/123/posts/321',
        );

        $this->router->registerControllers([TestControllerWithMultipleParams::class]);

        $result = $this->router->resolve($req);

        $this->assertEquals("User 123 Post 321", $result);
    }

    public function test_resolve_route_with_request_injection()
    {
        $req = RequestFactory::create(
            method: 'POST',
            path: '/data',
        );
        
        $this->router->registerControllers([TestControllerWithRequest::class]);
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('Request method: POST', $result);
    }

    public function test_resolve_route_with_request_and_parameters()
    {
        $req = RequestFactory::create(
            method: 'GET',
            path: '/users/42/edit',
        );
        
        $this->router->registerControllers([TestControllerWithRequestAndParams::class]);
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('Method: GET, ID: 42', $result);
    }

    public function test_resolve_route_with_optional_parameter()
    {
        $req = RequestFactory::create(
            method: 'GET',
            path: '/hello',
        );
        
        $this->router->registerControllers([TestControllerWithOptionalParam::class]);
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('Hello Guest', $result);
    }

    public function test_resolve_throws_exception_for_non_existent_route()
    {
        $req = RequestFactory::create(
            method: 'GET',
            path: '/non-existent',
        );
        
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Route not found: GET /non-existent");
        
        $this->router->resolve($req);
    }

    public function test_route_specific_middleware_executes()
    {
        $req = RequestFactory::create(
            method: 'GET',
            path: '/protected',
        );
        
        $this->router->registerControllers([TestControllerWithMiddleware::class]);
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('protected content', $result);
    }

    public function test_middleware_can_short_circuit_request()
    {
        $req = RequestFactory::create(
            method: 'GET',
            path: '/admin',
        );
        
        $this->router->registerControllers([TestControllerWithBlockingMiddleware::class]);
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('Access Denied', $result);
    }

    public function test_global_middleware_executes_before_route_middleware()
    {
        $req = RequestFactory::create(
            method: 'GET',
            path: '/test',
        );
        
        $this->router->registerGlobalMiddleware([BlockMiddleware::class]);
        $this->router->registerControllers([TestControllerWithAttributes::class]);
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('Access Denied', $result);
    }

    public function test_parameter_order_matches_callable_signature()
    {
        $req = RequestFactory::create(
            method: 'GET',
            path: '/posts/100/comments/200',
        );
        
        $this->router->registerControllers([TestControllerWithReversedParams::class]);
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('Post: 100, Comment: 200', $result);
    }

    public function test_non_existent_controller_class_throws_runtime_exception()
    {
        $req = RequestFactory::create(
            method: 'GET',
            path: '/test',
        );
        
        // Manually register a non-existent controller
        $reflection = new \ReflectionClass($this->router);
        $registerMethod = $reflection->getMethod('register');
        $registerMethod->setAccessible(true);
        $registerMethod->invoke($this->router, 'GET', '/test', ['NonExistentController', 'index'], []);
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Failed to execute route action.");

        $this->router->resolve($req);
    }

    public function test_non_existent_controller_method_throws_runtime_exception()
    {
        $req = RequestFactory::create(
            method: 'GET',
            path: '/test',
        );

        // Manually register a controller with non-existent method
        $reflection = new \ReflectionClass($this->router);
        $registerMethod = $reflection->getMethod('register');
        $registerMethod->setAccessible(true);
        $registerMethod->invoke($this->router, 'GET', '/test', [\stdClass::class, 'nonExistentMethod'], []);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Failed to execute route action.");

        $this->router->resolve($req);
    }

    public function test_route_matching_is_case_sensitive()
    {
        $req = RequestFactory::create(
            method: 'GET',
            path: '/Users',
        );
        
        $this->router->registerControllers([TestControllerWithAttributes::class]);
        
        $this->expectException(NotFoundException::class);
        
        $this->router->resolve($req);
    }

    public function test_route_with_trailing_slash_does_not_match_without()
    {
        $req = RequestFactory::create(
            method: 'GET',
            path: '/test/',
        );
        
        $this->router->registerControllers([TestControllerWithAttributes::class]);
        
        $this->expectException(NotFoundException::class);
        
        $this->router->resolve($req);
    }
}

// Test Controllers
class TestIndexController
{
    public function index()
    {
        return 'test index';
    }
}

class TestControllerWithAttributes
{
    #[Route(method: HttpMethod::GET, path: '/test')]
    public function index()
    {
        return 'test index';
    }
}

class TestControllerWithMiddleware
{
    #[Route(method: HttpMethod::GET, path: '/protected', middleware: [AllowMiddleware::class])]
    public function index()
    {
        return 'protected content';
    }
}

class TestControllerWithBlockingMiddleware
{
    #[Route(method: HttpMethod::GET, path: '/admin', middleware: [BlockMiddleware::class])]
    public function index()
    {
        return 'admin content';
    }
}

class TestControllerWithMultipleRoutes
{
    #[Route(method: HttpMethod::GET, path: '/users')]
    public function index()
    {
        return 'users list';
    }

    #[Route(method: HttpMethod::POST, path: '/users')]
    public function store()
    {
        return 'user created';
    }
}

class TestControllerWithParams
{
    #[Route(method: HttpMethod::GET, path: '/users/:id')]
    public function show($id)
    {
        return "User ID: $id";
    }
}

class TestControllerWithMultipleParams
{
    #[Route(method: HttpMethod::GET, path: '/users/:id/posts/:postId')]
    public function show($id, $postId)
    {
        return "User $id Post $postId";
    }
}

class TestControllerWithRequest
{
    #[Route(method: HttpMethod::POST, path: '/data')]
    public function handle(Request $req)
    {
        return 'Request method: ' . $req->method->value;
    }
}

class TestControllerWithRequestAndParams
{
    #[Route(method: HttpMethod::GET, path: '/users/:id/edit')]
    public function edit(Request $req, $id)
    {
        return "Method: {$req->method->value}, ID: $id";
    }
}

class TestControllerWithOptionalParam
{
    #[Route(method: HttpMethod::GET, path: '/hello')]
    public function greet($name = 'Guest')
    {
        return "Hello $name";
    }
}

class TestControllerWithReversedParams
{
    #[Route(method: HttpMethod::GET, path: '/posts/:postId/comments/:commentId')]
    public function show($commentId, $postId)
    {
        return "Post: $postId, Comment: $commentId";
    }
}

// Test Middleware
class AllowMiddleware
{
    public function handle(Request $req)
    {
        return null;
    }
}

class BlockMiddleware
{
    public function handle(Request $req)
    {
        return 'Access Denied';
    }
}
