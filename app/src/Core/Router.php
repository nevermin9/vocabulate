<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routingMap = [];

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

    public function resolve(string $requestMethod, string $requestUri)
    {
        $route = explode("?", $requestUri)[0];

        foreach ($this->routingMap[$requestMethod] ?? [] as $pattern => $routeData) {
            $regex = preg_replace('#:([\w]+)#', '([^/]+)', $pattern);
            $regex = "#^" . $regex . "$#";

            if (preg_match($regex, $route, $matches)) {
                array_shift($matches);
                preg_match_all('#:([\w]+)#', $pattern, $paramNames);
                $paramAssoc = [];

                foreach ($paramNames[1] as $i => $name) {
                    $paramAssoc[$name] = $matches[$i] ?? null;
                }

                $action = $routeData['action'];
                $middleware = $routeData['middleware'];

                foreach ($middleware as $middlewareClass) {
                    if (class_exists($middlewareClass)) {
                        $middlewareObj = new $middlewareClass();
                        $response = $middlewareObj->handle(); 

                        if ($response !== null) {
                            return $response; 
                        }
                    }
                }

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

        throw new \Exception("Implement route not found exception");
    }
}
