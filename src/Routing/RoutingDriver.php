<?php

namespace Diana\Routing;

use ReflectionClass, ReflectionMethod;

use Diana\IO\Request;
use Diana\Runtime\Traits\Singleton;
use Diana\Support\Blueprints\Driver;
use Diana\Routing\Attributes\Delete;
use Diana\Routing\Attributes\Get;
use Diana\Routing\Attributes\Patch;
use Diana\Routing\Attributes\Post;
use Diana\Routing\Attributes\Put;

class RoutingDriver extends Driver implements Router
{
    private static $methodMap = [
        Delete::class => "DELETE",
        Get::class => "GET",
        Patch::class => "PATCH",
        Post::class => "POST",
        Put::class => "PUT"
    ];

    private array $routes = [];

    public function __construct()
    {
    }

    public function loadRoutes($controllers): void
    {
        foreach (self::$methodMap as $class => $method)
            $this->routes[$method] = [];

        foreach ($controllers as $controller) {
            foreach ((new ReflectionClass($controller))->getMethods() as $method) {
                $reflection = new ReflectionMethod($controller, $method->name);
                foreach ($reflection->getAttributes() as $attribute) {
                    $arguments = $attribute->getArguments();

                    $path = '/';
                    if (isset ($controller::$route))
                        $path .= trim($controller::$route, '/') . '/';
                    $path .= trim($arguments[0], '/');

                    $this->routes[self::$methodMap[$attribute->getName()]][$path] = [
                        'controller' => $controller,
                        'method' => $method->name,
                        'segments' => explode('/', trim($path, '/'))
                    ];
                }
            }
        }
    }

    public function findRoute(Request $request): ?array
    {
        $segments = explode('/', trim($request->getRoute(), '/'));
        $segmentCount = count($segments);

        $params = [];

        foreach ($this->routes[$request->getMethod()] as $route) {
            if ($segmentCount != count($route['segments']))
                continue;

            for ($i = 0; $i < $segmentCount; $i++) {
                if ($route['segments'][$i][0] == ':') {
                    $params[substr($route['segments'][$i], 1)] = $segments[$i];
                } elseif ($route['segments'][$i] != $segments[$i])
                    continue 2;
            }

            $route['params'] = $params;
            return $route;
        }

        return null;
    }
}