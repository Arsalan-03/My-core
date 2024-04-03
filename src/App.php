<?php

namespace src\Core;

use Psr\Log\LoggerInterface;
use Throwable;

class App
{
    private Container $container;
    private array $routes = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run(): void
    {
        $requestUri = $_SERVER['REQUEST_URI'];

        try {
            if (isset($this->routes[$requestUri])) {

                $requestMethod = $_SERVER['REQUEST_METHOD'];
                $routMethods = $this->routes[$requestUri];

                if (isset($routMethods[$requestMethod])) {
                    $handler = $routMethods[$requestMethod];
                    $class = $handler['class'];
                    $method = $handler['method'];
                    $requestClass = $handler['request'] ?? Request::class;
                    $request = new $requestClass($_POST);

                    $obj = $this->container->get($class);
                    $obj->$method($request);
                } else {
                    echo "Метод $requestMethod не поддерживается для $requestUri";
                }
            } else {
                $path = './../../../View/404.html';
                set_include_path(get_include_path() . PATH_SEPARATOR . $path);
            }
        }
        catch (Throwable $exception) {
            $logger = $this->container->get(LoggerInterface::class);

            $data = [
                    'message' => 'Сообщение об ошибке: ' . $exception->getMessage(),
                    'code' => 'Код: ' . $exception->getCode(),
                    'file' => 'Файл: ' . $exception->getFile(),
                    'line' => 'Строка: ' . $exception->getLine(),
                ];

            $logger->error("code execution error\n", $data);

            $path = './../../../View/505.html';
            set_include_path(get_include_path() . PATH_SEPARATOR . $path);
        }
    }

    public function get(string $route, string $class, string $method, string $requestClass = null): void
    {
        $this->routes[$route]['GET'] = [
            'class' => $class,
            'method' => $method,
            'request' => $requestClass
        ];
    }
    public function post(string $route, string $class, string $method, string $requestClass = null): void
    {
        $this->routes[$route]['POST'] = [
            'class' => $class,
            'method' => $method,
            'request' => $requestClass
        ];
    }

    public function pull(string $route, string $class, string $method, string $requestClass = null): void
    {
        $this->routes[$route]['PULL'] = [
            'class' => $class,
            'method' => $method,
            'request' => $requestClass
        ];
    }

    public function delete(string $route, string $class, string $method, string $requestClass = null): void
    {
        $this->routes[$route]['DELETE'] = [
            'class' => $class,
            'method' => $method,
            'request' => $requestClass
        ];
    }
}