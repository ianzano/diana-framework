<?php

namespace Diana\Kernel;

use Diana\IO\Request;
use Diana\IO\Response;
use Diana\Routing\Router;
use Diana\Runtime\Application;
use Diana\Support\Blueprints\Driver as BaseDriver;

class Driver extends BaseDriver implements Kernel
{
    protected array $middleware = [];

    public function registerMiddleware(string $middleware)
    {

    }

    public function __construct(private Application $app)
    {
    }

    public function process(Request $request): Response
    {
        $this->app->instance('request', $request);

        // $this->bootstrap();

        // return (new Pipeline($this->app))
        //     ->send($request)
        //     ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
        //     ->then($this->dispatchToRouter());

        // TODO: Create pipeline
        // TODO: execute the middleware, on of them is RoutingMiddleware who takes care of routing

        $route = $this->app->resolve(Router::class)->findRoute($request);


        if (!$route) {
            return new Response("404");
        }

        $result = (new $route['controller']())->{$route['method']}();

        // TODO: Fire up the router, pass it the request and let it generate a response which then is emitted
        return new Response($result);
    }
}