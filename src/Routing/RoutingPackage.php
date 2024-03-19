<?php

namespace Diana\Routing;

use Diana\Interfaces\Runnable;
use Diana\Runtime\Application;
use Diana\Runtime\Package;
use Diana\Routing\Router;

class RoutingPackage extends Package implements Runnable
{
    private Router $router;

    public function register(Application $app)
    {
        $this->router = new RoutingDriver();
        $app->registerDriver(Router::class, $this->router);
    }

    public function boot(Application $app)
    {
        $this->router->loadRoutes($app->getControllers());
    }
}