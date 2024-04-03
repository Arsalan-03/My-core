<?php

namespace src\Core;

class Container
{
    private array $services = [];

    public function __construct(array $services)
    {
        $this->services = $services;
    }
    public function set($className, callable $callback): void //СОХРАНЯЕТ ИНФОРМАЦИЮ о том, как создавать объект определённого класса
    {
        $this->services[$className] = $callback;
    }

    public function get($className): object // ВОЗВРАЩАЕТ ОБЪЕКТ указанного класса
    {
        if (isset($this->services[$className])) {
            $callback = $this->services[$className];

            return $callback($this);
        }

        return new $className();
    }
}