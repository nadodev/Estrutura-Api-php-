<?php

declare(strict_types=1);

namespace App\Shared\Container;

use Closure;
use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

final class Container
{
    private array $bindings = [];

    private array $instances = [];

    private array $singletons = [];

    public function bind(string $abstract, string|Closure|null $concrete = null): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete ?? $abstract,
            'singleton' => false,
        ];
    }

    public function singleton(string $abstract, string|Closure|null $concrete = null): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete ?? $abstract,
            'singleton' => true,
        ];
    }

    public function instance(string $abstract, object $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    public function make(string $abstract): object
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $binding = $this->bindings[$abstract] ?? null;

        $concrete = $binding['concrete'] ?? $abstract;

        if ($concrete instanceof Closure) {
            $object = $concrete($this);
        } else {
            $object = $this->build($concrete);
        }

        if (($binding['singleton'] ?? false) === true) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    private function build(string $concrete): object
    {
        $reflection = new ReflectionClass($concrete);

        if (! $reflection->isInstantiable()) {
            throw new RuntimeException("Classe {$concrete} não pode ser instanciada.");
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $concrete();
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if (! $type instanceof ReflectionNamedType) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }

                throw new RuntimeException(
                    "Não foi possível resolver o parâmetro {$parameter->getName()} da classe {$concrete}."
                );
            }

            if ($type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }

                throw new RuntimeException(
                    "Não foi possível resolver o parâmetro escalar {$parameter->getName()} da classe {$concrete}."
                );
            }

            $dependencies[] = $this->make($type->getName());
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}