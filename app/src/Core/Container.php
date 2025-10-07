<?php
declare(strict_types=1);

namespace App\Core;

use App\Attributes\Container\Singleton;
use App\Exceptions\Container\ContainerException;
use App\Exceptions\Container\NotFoundException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface 
{
    protected array $bindings = [];
    protected array $instances = [];

    public function get(string $id)
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (isset($this->bindings[$id])) {
            $binding = $this->bindings[$id];

            $factory = $binding['factory'] ?? $id; 
            $isSingleton = $binding['singleton'] ?? false;

            if (is_callable($factory)) {
                return $this->setIfSingleton($id, $isSingleton, $factory($this));
            }

            $id = $factory;

            return $this->resolve($id, $isSingleton);
        }

        return $this->resolve($id); 
    }

    public function has(string $id): bool
    {
        return isset($this->bindings[$id]);
    }

    public function bind(string $id, callable|string $factory, bool $singleton = false)
    {
        $this->bindings[$id] = compact('factory', 'singleton');
    }

    public function singleton(string $id, callable|string $factory)
    {
        $this->bind($id, $factory, true);
    }

    protected function setIfSingleton(string $id, bool $singleton, mixed $instance): mixed
    {
        if ($singleton) {
            $this->instances[$id] = $instance;
        }
        return $instance;
    }

    protected function resolve(string $id, bool $singleton = false)
    {
        $reflectionClass = new \ReflectionClass($id);

        if (! $reflectionClass->isInstantiable()) {
            throw new ContainerException("The {$id} is not instantiable.");
        }

        $constructor = $reflectionClass->getConstructor();
        if (! $singleton) {
            $singleton = (bool)$reflectionClass->getAttributes(Singleton::class);
        }

        if (! $constructor) {
            return $this->setIfSingleton($id, $singleton, new $id());
        }

        $parameters = $constructor->getParameters();

        if (! $parameters) {
            return $this->setIfSingleton($id, $singleton, new $id());
        }

        $dependencies = array_map(function (\ReflectionParameter $param) use ($id) {
            $name = $param->getName();
            $type = $param->getType();

            if (! $type) {
                throw new ContainerException("Failed to resolve class {$id} bc param {$name} doesn't have a type hint");
            }

            if ($type->isBuiltin()) {
                throw new ContainerException("Failed to resolve class {$id} bc param {$name} is built-in type: {$type->getName()}");
            }

            if ($type instanceof \ReflectionUnionType) {
                throw new ContainerException("Failed to resolve class {$id} bc param {$name} is union type");
            }

            if ($type instanceof \ReflectionNamedType) {
                return $this->get($type->getName());
            }

            throw new ContainerException("Failed to resolve class {$id} bc of invalid param {$name}");
        }, $parameters);

        return $this->setIfSingleton($id, $singleton, $reflectionClass->newInstanceArgs($dependencies));
    }
}
