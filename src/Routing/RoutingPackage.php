<?php

namespace Diana\Routing;

use Diana\Runtime\Application;
use Diana\Runtime\Package;
use Diana\Routing\Router;

class RoutingPackage extends Package
{
    private Router $router;

    public function register(Application $app): void
    {
        $this->router = new RoutingDriver();
        $app->registerDriver(Router::class, $this->router);
    }

    public function boot(Application $app): void
    {
        $this->router->loadRoutes($app->getControllers());
    }
}