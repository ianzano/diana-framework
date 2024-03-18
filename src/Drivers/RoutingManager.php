<?php

namespace Diana\Drivers;

use Diana\Drivers\Interfaces\RoutingDriver;
use Diana\IO\Request;
use Diana\Support\Obj;

class RoutingManager extends Obj implements RoutingDriver
{
    public function __construct()
    {
        // TODO: load routes
    }

    public function findEndpoint(Request $request): callable
    {

    }
}