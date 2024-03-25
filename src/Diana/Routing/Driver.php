<?php

namespace Diana\Routing;

use ReflectionClass, ReflectionMethod;

use Diana\IO\Request;
use Diana\Routing\Attributes\Delete;
use Diana\Routing\Attributes\Get;
use Diana\Routing\Attributes\Patch;
use Diana\Routing\Attributes\Post;
use Diana\Routing\Attributes\Put;

use Diana\Routing\Router as RouterContract;
use Diana\Runtime\Application;
use Diana\Routing\Attributes\Middleware;
use Exception;
use Diana\Support\Debug;

class Driver implements RouterContract
{
    private static $methodMap = [
        Delete::class => "DELETE",
        Get::class => "GET",
        Patch::class => "PATCH",
        Post::class => "POST",
        Put::class => "PUT"
    ];

    private array $routes = [];

    public function __construct(private Application $app)
    {
    }

    public function loadRoutes(): void
    {
        foreach (self::$methodMap as $class => $method)
            $this->routes[$method] = [];

        foreach ($this->app->getControllers() as $controller) {
            foreach ((new ReflectionClass($controller))->getMethods() as $method) {
                $reflection = new ReflectionMethod($controller, $method->name);
                $attributes = $reflection->getAttributes();

                $middleware = [];
                foreach ($attributes as $attribute) {
                    if ($attribute->getName() == Middleware::class)
                        foreach ($attribute->getArguments() as $argument)
                            $middleware[] = $argument;
                }

                foreach ($attributes as $attribute) {
                    if (!array_key_exists($attribute->getName(), self::$methodMap))
                        continue;

                    $arguments = $attribute->getArguments();

                    if (empty ($arguments))
                        throw new Exception('Route [' . $controller . '@' . $method->name . '] does not provide a path.');

                    $path = '/';
                    if (isset ($controller::$route))
                        $path .= trim($controller::$route, '/') . '/';
                    $path .= trim($arguments[0], '/');

                    if (array_key_exists($path, $this->routes[self::$methodMap[$attribute->getName()]]))
                        throw new Exception('Route [' . $controller . '@' . $method->name . '] tried to assign the path [' . $path . '] that has already been assigned to [' . $this->routes[self::$methodMap[$attribute->getName()]][$path]['controller'] . '@' . $this->routes[self::$methodMap[$attribute->getName()]][$path]['method'] . ']');

                    $this->routes[self::$methodMap[$attribute->getName()]][$path] = [
                        'controller' => $controller,
                        'method' => $method->name,
                        'middleware' => $middleware,
                        'segments' => explode('/', trim($path, '/'))
                    ];
                }
            }
        }
    }

    public function findRoute(Request $request): ?array
    {
        $trim = trim($request->getRoute(), '/');
        $segments = explode('/', $trim);
        $segmentCount = count($segments);

        $params = [];

        foreach ($this->routes[$request->getMethod()] as $route) {
            if ($segmentCount != count($route['segments']))
                continue;

            if ($trim) {
                for ($i = 0; $i < $segmentCount; $i++) {
                    if ($route['segments'][$i][0] == ':') {
                        $params[substr($route['segments'][$i], 1)] = $segments[$i];
                    } elseif ($route['segments'][$i] != $segments[$i])
                        continue 2;
                }
            }

            $route['params'] = $params;
            return $route;
        }

        return null;
    }
}