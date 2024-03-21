<?php

namespace Diana\Runtime;

use Closure;
use Diana\Exceptions\KernelException;
use Diana\IO\Request;
use Diana\IO\Response;
use Diana\Runtime\Application;
use Diana\Contracts\Kernel as KernelContract;
use Diana\Contracts\Middleware;
use RuntimeException;

class Kernel implements KernelContract
{
    protected array $middleware = [];

    public function registerMiddleware(string|Closure $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    public function __construct(private Application $app)
    {
    }

    public function process(Request $request): Response
    {
        $this->app->instance(Request::class, $request);

        $next = function ($request) use (&$next) {
            $middleware = array_shift($this->middleware);

            if (!$middleware)
                return null;

            if ($middleware instanceof Closure)
                return $middleware($request, $next);
            else if (is_string($middleware)) {
                $instance = $this->app->resolve($middleware);
                if (!$instance instanceof Middleware)
                    throw new RuntimeException('Attempted to register a middleware [' . $instance::class . '] that does not implement Middleware.');
                return $instance->run($request, $next);
            }
        };

        $response = $next($request);

        if (!$response)
            throw new KernelException('No middleware has provided a response.');

        return $response;

        // $this->bootstrap();

        // TODO: Create pipeline

        // return (new Pipeline($this->app))
        //     ->send($request)
        //     ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
        //     ->then($this->dispatchToRouter());
    }
}