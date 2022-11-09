<?php

namespace DanialPanah\DataTransformer\Container;

use Closure;
use ReflectionClass;
use DanialPanah\DataTransformer\Exceptions\ContainerException;

class AppContainer
{
    /**
     * @var array
     */
    protected $instances = [];


    public function bind(string $abstract, $concrete = NULL): void
    {
        if ($concrete === NULL) $concrete = $abstract;

        $this->instances[$abstract] = $concrete;
    }


    public function make(string $abstract, $parameters = []): object|null
    {
        if (!isset($this->instances[$abstract])) $this->bind($abstract);

        return $this->resolve($this->instances[$abstract], $parameters);
    }


    public function resolve(string $concrete, $parameters): mixed
    {
        if ($concrete instanceof Closure) return $concrete($this, $parameters);

        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable())
            throw new ContainerException("Invalid instantiable class: {$concrete} ");


        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) return $reflector->newInstance();

        $parameters = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters);

        return $reflector->newInstanceArgs($dependencies);
    }


    public function getDependencies($parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();

            if ($dependency === NULL) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new ContainerException("Dependency class can not be resolved: {$parameter->name}");
                }
            } else {
                $dependencies[] = $this->make($dependency->name);
            }
        }

        return $dependencies;
    }
}