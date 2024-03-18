<?php

namespace Diana\Drivers;

use Diana\Drivers\Interfaces\RoutingDriver;
use Diana\IO\Request;
use Diana\Runtime\Traits\Singleton;
use Diana\Support\Obj;

class RoutingManager extends Obj implements RoutingDriver
{
    use Singleton;

    public function __construct()
    {
        // TODO: load routes
    }

    public function findEndpoint(Request $request): callable
    {

    }
}