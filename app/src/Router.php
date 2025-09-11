<?php
declare(strict_types=1);

namespace App;

final class Router
{
    private array $routingMap = [];

    public function get(string $route, callable|array $action): static
    {
        return $this->register('get', $route, $action);
    }

    public function post(string $route, callable|array $action): static
    {
        return $this->register('post', $route, $action);
    }

    public function put(string $route, callable|array $action): static
    {
        return $this->register('put', $route, $action);
    }

    public function patch(string $route, callable|array $action): static
    {
        return $this->register('patch', $route, $action);
    }

    public function delete(string $route, callable|array $action): static
    {
        return $this->register('delete', $route, $action);
    }

    public function options(string $route, callable|array $action): static
    {
        return $this->register('options', $route, $action);
    }

    public function head(string $route, callable|array $action): static
    {
        return $this->register('head', $route, $action);
    }

    private function register(string $httpMethod, string $route, callable|array $action): static
    {
        $this->routingMap[$httpMethod][$route] = $action;

        return $this;
    }

    public function resolve(string $requestMethod, string $requestUri)
    {
        $route = explode("?", $requestUri)[0];

        $action = $this->routingMap[$requestMethod][$route] ?? null;

        if (is_callable($action)) {
            return call_user_func($action);
        }

        if (is_array($action)) {
            [$class, $method] = $action;

            if (class_exists($class)) {
                $obj = new $class();

                if (method_exists($obj, $method)) {
                    return call_user_func_array([$obj, $method], []);
                }
            }
        }

        throw new \Exception("Implement route not found exception");
    }
}
