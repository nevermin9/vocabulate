<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Request;
use App\Core\RequestFactory;
use App\Core\Router;
use App\Exceptions\NotFoundException;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
    }

    public function test_get_registers_route_with_middleware()
    {
        $this->router->get('/users', ['UserController', 'index'], 'AuthMiddleware');
        
        $routes = $this->router->getRouter();
        
        $this->assertArrayHasKey('get', $routes);
        $this->assertArrayHasKey('/users', $routes['get']);
        $this->assertEquals(['UserController', 'index'], $routes['get']['/users']['action']);
        $this->assertEquals(['AuthMiddleware'], $routes['get']['/users']['middleware']);
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

    public function test_post_registers_route()
    {
        $this->router->post('/users', ['UserController', 'store']);

        $routes = $this->router->getRouter();

        $this->assertArrayHasKey('post', $routes);
        $this->assertArrayHasKey('/users', $routes['post']);
    }

    public function test_register_returns_router_instance_for_chaining()
    {
        $result = $this->router
            ->get('/users', fn() => 'users')
            ->post('/users', fn() => 'create')
            ->put('/users/:id', fn() => 'update');

        $this->assertInstanceOf(Router::class, $result);
    }

    public function test_register_global_middleware()
    {
        $result = $this->router->registerGlobalMiddleware(['CorsMiddleware', 'LogMiddleware']);
        
        $this->assertInstanceOf(Router::class, $result);
        $this->assertSame(['CorsMiddleware', 'LogMiddleware'], $this->router->getGlobalMiddleware());
    }


    public function test_resolve_exact_route_with_closure()
    {
        $req = RequestFactory::create(method: 'get', path: '/hello');
        
        $this->router->get('/hello', fn() => 'Hello World');
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('Hello World', $result);
    }

    public function test_resolve_route_with_array_callable()
    {
        $req = RequestFactory::create(method: 'get', path: '/test');
        $controller = new class() {
            public function index()
            {
                return 'test index';
            }
        };
        
        $this->router->get('/test', [$controller::class, 'index']);
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('test index', $result);
    }

    public function test_resolve_route_with_single_parameter()
    {
        $req = RequestFactory::create(method: 'get', path: '/users/123');
        
        $this->router->get('/users/:id', fn($id) => "User ID: $id");
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('User ID: 123', $result);
    }

    public function test_resolve_route_with_multiple_parameters()
    {
        $req = RequestFactory::create(method: 'get', path: '/users/123/posts/321');

        $this->router->get('/users/:id/posts/:postId', 
            fn($id, $postId) => "User {$id} Post {$postId}"
        );

        $result = $this->router->resolve($req);

        $this->assertEquals($result, "User 123 Post 321");
    }

    public function test_resolve_route_with_request_injection()
    {
        $req = RequestFactory::create(method: 'post', path: '/data');
        
        $this->router->post('/data', function(Request $req) {
            return 'Request method: ' . $req->method;
        });
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('Request method: post', $result);
    }

    public function test_resolve_route_with_request_and_parameters()
    {
        $req = RequestFactory::create(method: 'get', path: '/users/42/edit');
        
        $this->router->get('/users/:id/edit', function(Request $req, $id) {
            return "Method: {$req->method}, ID: $id";
        });
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('Method: get, ID: 42', $result);
    }

    public function test_resolve_route_with_optional_parameter()
    {
        $req = RequestFactory::create(method: 'get', path: '/hello');
        
        $this->router->get('/hello', fn($name = 'Guest') => "Hello $name");
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('Hello Guest', $result);
    }

    public function test_resolve_throws_exception_for_non_existent_route()
    {
        $req = RequestFactory::create(method: 'get', path: '/non-existent');
        
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Route not found: GET /non-existent");
        
        $this->router->resolve($req);
    }

    public function test_route_specific_middleware_executes()
    {
        $req = RequestFactory::create(method: 'get', path: '/protected');
        
        $this->router->get('/protected', fn() => 'protected content', AllowMiddleware::class);
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('protected content', $result);
    }

    public function test_middleware_can_short_circuit_request()
    {
        $req = RequestFactory::create(method: 'get', path: '/admin');

        
        $this->router->get('/admin', fn() => 'admin content', BlockMiddleware::class);
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('Access Denied', $result);
    }

    public function test_global_middleware_executes_before_route_middleware()
    {
        $req = RequestFactory::create(method: 'get', path: '/test');
        
        $this->router->registerGlobalMiddleware([BlockMiddleware::class]);
        $this->router->get('/test', fn() => 'content', AllowMiddleware::class);
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('Access Denied', $result);
    }

    public function test_parameter_order_matches_callable_signature()
    {
        $req = RequestFactory::create(method: 'get', path: '/posts/100/comments/200');
        
        $this->router->get('/posts/:postId/comments/:commentId', 
            function($commentId, $postId) {
                return "Post: $postId, Comment: $commentId";
            }
        );
        
        $result = $this->router->resolve($req);
        
        $this->assertEquals('Post: 100, Comment: 200', $result);
    }

    public function test_non_existent_controller_class_throws_not_found()
    {
        $req = RequestFactory::create(method: 'get', path: '/test');
        
        $this->router->get('/test', ['NonExistentController', 'index']);
        
        
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Route not found: GET /test");

        $result = $this->router->resolve($req);
    }

    public function test_non_existent_controller_method_throws_not_found()
    {
        $req = RequestFactory::create(method: 'get', path: '/test');
        $controller = new class () {};

        $this->router->get('/test', [$controller::class, 'nonExistentMethod']);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Route not found: GET /test");

        $result = $this->router->resolve($req);
    }

    public function test_route_matching_is_case_sensitive()
    {
        $req = RequestFactory::create(method: 'get', path: '/Users');
        
        $this->router->get('/users', fn() => 'users');
        
        $this->expectException(NotFoundException::class);
        
        $this->router->resolve($req);
    }

    public function test_route_with_trailing_slash_does_not_match_without()
    {
        $req = RequestFactory::create(method: 'get', path: '/users/');
        
        $this->router->get('/users', fn() => 'users');
        
        $this->expectException(NotFoundException::class);
        
        $this->router->resolve($req);
    }
}

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
