<?php
declare(strict_types=1);

namespace App\Core;

use App\Attributes\Container\Singleton;
use App\Exceptions\NotFoundException;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;

#[Singleton]
class Router
{
    /** @var array<string, array<string, array{action: callable|array, middleware: class-string[]}>> */
    private array $routingMap = [];
    
    /** @var class-string[] */
    private array $globalMiddleware = [];

    public const HTTP_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'];

    public function __construct(protected Container $container)
    {
    }

    /**
     * Dynamically registers a route for a specific HTTP method.
     */
    public function __call(string $name, array $arguments): static
    {
        $httpMethod = strtoupper($name);
        if (in_array($httpMethod, self::HTTP_METHODS, true)) {
            [$route, $action, $middleware] = $arguments + [2 => []];
            
            if (!is_string($route) || (!is_callable($action) && !is_array($action) && !is_string($action))) {
                throw new \InvalidArgumentException("Invalid route or action passed to {$name}() method.");
            }
            if (!is_array($middleware)) {
                $middleware = [$middleware];
            }
            
            return $this->register($httpMethod, $route, $action, $middleware);
        }

        throw new \BadMethodCallException("Method {$name} not supported.");
    }

    /**
     * Registers an array of class strings as global middleware.
     * @param class-string[] $middleware
     */
    public function registerGlobalMiddleware(array $middleware): static
    {
        $this->globalMiddleware = array_filter($middleware, 'is_string');

        return $this;
    }

    /**
     * Gets the full routing map.
     * @return array<string, array<string, array{action: callable|array, middleware: class-string[]}>>
     */
    public function getRoutes(): array
    {
        return $this->routingMap;
    }

    /**
     * Gets the registered global middleware.
     * @return class-string[]
     */
    public function getGlobalMiddleware(): array
    {
        return $this->globalMiddleware;
    }

    /**
     * Registers a route in the routing map.
     * @param string $httpMethod HTTP method (e.g., 'GET', 'POST').
     * @param string $route The route pattern (e.g., '/users/:id').
     * @param callable|array $action The handler for the route.
     * @param string[] $middleware Optional middleware to run.
     * @return static
     */
    private function register(string $httpMethod, string $route, callable|array $action, array $middleware): static
    {
        $httpMethod = strtoupper($httpMethod);
        
        $this->routingMap[$httpMethod][$route] = [
            'action' => $action,
            'middleware' => $middleware,
        ];

        return $this;
    }


    private function validateAction(callable|array $action) {
        if (is_array($action)) {
            [$class, $method] = $action;
            return class_exists($class) && method_exists($class, $method);
        }

        return is_callable($action);
    }

    /**
     * Runs the middleware stack.
     * @param Request $req The current Request object.
     * @param array<class-string> $middlewareClasses The middleware classes to execute.
     * @return mixed A response object if middleware halts execution, or null.
     */
    private function runMiddleware(Request $req, array &$middlewareClasses): mixed
    {
        foreach ($middlewareClasses as $middlewareClass) {
            if (!class_exists($middlewareClass)) {
                continue;
            }
            
            $middlewareObj = $this->container->get($middlewareClass);
            
            if (method_exists($middlewareObj, 'handle')) {
                $response = $middlewareObj->handle($req);
                
                if ($response !== null) {
                    return $response;
                }
            }
        }

        return null;
    }

    /**
     * Extracts route parameters by matching the route pattern against the request path.
     * @param string $pattern The route pattern (e.g., '/users/:id').
     * @param string $route The request path (e.g., '/users/123').
     * @return array|null An array of route parameters or null if no match.
     */
    private function extractRouteParams(string $pattern, string $route): ?array
    {
        // Convert pattern to regex
        $regex = preg_replace('#:([\w]+)#', '([^/]+)', $pattern);
        $regex = "#^" . $regex . "$#";

        if (!preg_match($regex, $route, $matches)) {
            return null;
        }

        // Extract parameter names
        preg_match_all('#:([\w]+)#', $pattern, $paramNames);

        array_shift($matches); // Remove the full match
        $routeParams = [];
        foreach ($paramNames[1] as $i => $name) {
            $routeParams[$name] = $matches[$i] ?? null;
        }

        return $routeParams;
    }

    /**
     * Builds the associative parameter array for a callable's execution.
     * @param callable|array $callable The function or [class, method] array.
     * @param array $routeParams The URL parameters (e.g., ['id' => '123']).
     * @param Request $req The current Request object.
     * @return array The associative array of parameters to pass to call_user_func_array.
     */
    private function getCallableParameters(callable|array $callable, array $routeParams, Request $req): array
    {
        if (is_array($callable)) {
            [$class, $method] = $callable;
            $reflector = new ReflectionMethod($class, $method);
        } elseif (is_callable($callable)) {
            $reflector = new ReflectionFunction($callable);
        } else {
            return [];
        }

        $paramAssoc = [];
        $requestClass = Request::class;

        foreach ($reflector->getParameters() as $parameter) {
            $paramName = $parameter->getName();
            $paramType = $parameter->getType();

            if ($paramType instanceof ReflectionNamedType && !$paramType->isBuiltin() && $paramType->getName() === $requestClass) {
                $paramAssoc[$paramName] = $req;
                continue;
            }

            if (array_key_exists($paramName, $routeParams)) {
                $paramAssoc[$paramName] = $routeParams[$paramName];
                continue;
            }

            if ($parameter->isOptional()) {
                $paramAssoc[$paramName] = $parameter->getDefaultValue();
                continue;
            }
        }

        return $paramAssoc;
    }
    
    /**
     * Resolves and executes the action for the current request.
     * @throws NotFoundException
     * @throws RuntimeException
     */
    public function resolve(Request $req): mixed
    {
        $middlewareResponse = $this->runMiddleware($req, $this->globalMiddleware);
        if ($middlewareResponse) {
            return $middlewareResponse;
        }

        $requestMethod = $req->method;
        $routePath = $req->path;

        foreach ($this->routingMap[$requestMethod] ?? [] as $pattern => $routeData) {
            $routeParams = $this->extractRouteParams($pattern, $routePath);
            
            if ($routeParams === null) {
                continue;
            }

            $action = $routeData['action'];
            $middleware = $routeData['middleware'];
            $middlewareResponse = $this->runMiddleware($req, $middleware);
            if ($middlewareResponse) {
                return $middlewareResponse;
            }

            return $this->executeAction($action, $routeParams, $req);
        }

        throw NotFoundException::forRoute($routePath, $requestMethod);
    }
    
    /**
     * Executes the route action.
     * @param callable|array $action The action to execute.
     * @param array $routeParams Extracted route parameters.
     * @param Request $req The current Request.
     * @return mixed The response from the action.
     * @throws RuntimeException
     */
    private function executeAction(callable|array $action, array $routeParams, Request $req): mixed
    {
        if (! $this->validateAction($action)) {
            throw new \RuntimeException('Failed to execute route action.');
        }

        $paramAssoc = $this->getCallableParameters($action, $routeParams, $req);

        if (is_callable($action)) {
            return call_user_func_array($action, $paramAssoc);
        }

        [$class, $method] = $action;

        $obj = $this->container->get($class);

        return call_user_func_array([$obj, $method], $paramAssoc);
    }
}
