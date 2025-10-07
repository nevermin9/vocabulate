<?php
declare(strict_types=1);

namespace App\Core;

use App\Exceptions\NotFoundException;

final class Router
{
    private array $routingMap = [];
    private array $globalMiddleware = [];

    public function get(string $route, callable|array|string $action, string|array $middleware = []): static
    {
        return $this->register('get', $route, $action, $middleware);
    }

    public function post(string $route, callable|array|string $action, string|array $middleware = []): static
    {
        return $this->register('post', $route, $action, $middleware);
    }

    public function put(string $route, callable|array|string $action, string|array $middleware = []): static
    {
        return $this->register('put', $route, $action, $middleware);
    }

    public function patch(string $route, callable|array|string $action, string|array $middleware = []): static
    {
        return $this->register('patch', $route, $action, $middleware);
    }

    public function delete(string $route, callable|array|string $action, string|array $middleware = []): static
    {
        return $this->register('delete', $route, $action, $middleware);
    }

    public function options(string $route, callable|array|string $action, string|array $middleware = []): static
    {
        return $this->register('options', $route, $action, $middleware);
    }

    public function head(string $route, callable|array|string $action, string|array $middleware = []): static
    {
        return $this->register('head', $route, $action, $middleware);
    }

    public function registerGlobalMiddleware(array $middleware): static
    {
        $this->globalMiddleware = $middleware;

        return $this;
    }

    public function getRouter(): array
    {
        return $this->routingMap;
    }

    public function getGlobalMiddleware(): array
    {
        return $this->globalMiddleware;
    }

    /**
     * @param string $route
     * @param callable|array|string $action
     * @param string|array $middleware Optional middleware (or array of middleware) to run
     * @return static
     */
    private function register(string $httpMethod, string $route, callable|array|string $action, string|array $middleware): static
    {
        $middleware = (array) $middleware;

        $this->routingMap[$httpMethod][$route] = [
            'action' => $action,
            'middleware' => $middleware,
        ];

        return $this;
    }

    private function runGlobalMiddleware(Request $req): mixed
    {
        foreach ($this->globalMiddleware as $middlewareClass) {
            if (class_exists($middlewareClass)) {
                $middlewareObj = new $middlewareClass();
                $response = $middlewareObj->handle($req); 

                if ($response !== null) {
                    return $response; 
                }
            }
        }

        return null;
    }

    /**
     * Uses Reflection to build the parameter array for a callable,
     * injecting the Request object and route parameters.
     * @param callable|array $callable The function or [class, method] array.
     * @param array $routeParams The URL parameters (e.g., ['id' => '123']).
     * @param \App\Core\Request $req The current Request object.
     * @return array The associative array of parameters to pass to call_user_func_array.
     * @throws \ReflectionException
     */
    private function getCallableParameters(callable|array $callable, array $routeParams, Request $req): array
    {
        if (is_array($callable)) {
            [$class, $method] = $callable;

            if (!is_callable($callable)) {
                return [];
            }

            $reflector = new \ReflectionMethod($class, $method);

        } elseif (is_callable($callable)) {
            $reflector = new \ReflectionFunction($callable);
        } else {
            return [];
        }

        $paramAssoc = [];
        $requestClass = Request::class;

        foreach ($reflector->getParameters() as $parameter) {
            $paramName = $parameter->getName();
            $paramType = $parameter->getType();

            if ($paramType instanceof \ReflectionNamedType && $paramType->getName() === $requestClass) {
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

    public function resolve(Request $req): mixed
    {
        $result = $this->runGlobalMiddleware($req);

        if ($result) {
            return $result;
        }

        $requestMethod = $req->method;
        $route = $req->path;

        foreach ($this->routingMap[$requestMethod] ?? [] as $pattern => $routeData) {
            $regex = preg_replace('#:([\w]+)#', '([^/]+)', $pattern);
            $regex = "#^" . $regex . "$#";

            if (preg_match($regex, $route, $matches)) {
                array_shift($matches);
                preg_match_all('#:([\w]+)#', $pattern, $paramNames);

                $routeParams = [];
                foreach ($paramNames[1] as $i => $name) {
                    $routeParams[$name] = $matches[$i] ?? null;
                }

                $action = $routeData['action'];
                $middleware = $routeData['middleware'];

                foreach ($middleware as $middlewareClass) {
                    if (class_exists($middlewareClass)) {
                        $middlewareObj = new $middlewareClass();
                        $response = $middlewareObj->handle($req); 

                        if ($response !== null) {
                            return $response; 
                        }
                    }
                }

                $paramAssoc = $this->getCallableParameters($action, $routeParams, $req);

                if (is_callable($action)) {
                    return call_user_func_array($action, $paramAssoc);
                }

                if (is_array($action)) {
                    [$class, $method] = $action;

                    if (class_exists($class)) {
                        $obj = new $class();

                        if (method_exists($obj, $method)) {
                            return call_user_func_array([$obj, $method], $paramAssoc);
                        }
                    }
                }
            }
        }

        throw NotFoundException::forRoute($req->path, $req->method);
    }
}
