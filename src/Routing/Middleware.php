<?php

namespace Diana\Routing;

use Closure;
use Diana\Contracts\Kernel;
use Diana\IO\Request;
use Diana\IO\Response;
use Diana\Contracts\Middleware as MiddlewareContract;
use Diana\Runtime\Application;

class Middleware implements MiddlewareContract
{
    public function __construct(private Application $app, private Kernel $kernel)
    {

    }

    public function run(Request $request, Closure $next): Response
    {
        $response = $next($request, $next);

        // check if another middleware has taken care of giving a response, like for instance if an error has occured
        // and only if there is no response yet, the middleware will provide the response from the controller
        if ($response)
            return $response;

        $route = $this->app->resolve(Router::class)->findRoute($request);

        if (!$route)
            return new Response("404");

        // register the route-specific middleware to be executed after the global middleware
        foreach ($route['middleware'] as $middleware)
            $this->kernel->registerMiddleware($middleware);

        $response = $next($request, $next);
        if ($response)
            return $response;

        $result = $this->app->call($route['controller'] . '@' . $route['method']);

        $response = new Response($result);

        return $response;
    }
}