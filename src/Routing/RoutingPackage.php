<?php

namespace Diana\Routing;

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

    public function register(): void
    {
        // TODO: Register routing middleware here
    }

    public function boot(): void
    {
        $this->app->resolve(Router::class)->loadRoutes();
    }
}