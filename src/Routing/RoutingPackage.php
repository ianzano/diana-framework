<?php

namespace Diana\Routing;

use Closure;
use Diana\IO\Request;
use Diana\IO\Response;
use Diana\Contracts\Kernel;
use Diana\Runtime\Application;
use Diana\Runtime\Package;

use Diana\Routing\Router;
use Diana\Routing\Driver;
use Diana\Support\Debug;

class RoutingPackage extends Package
{
    private Router $router;

    public function __construct(private Application $app)
    {
        $this->app->singleton(Router::class, Driver::class);
    }

    public function register(Kernel $kernel): void
    {
        $kernel->registerMiddleware(Middleware::class);
    }

    public function boot(): void
    {
        $this->app->resolve(Router::class)->loadRoutes();
    }
}