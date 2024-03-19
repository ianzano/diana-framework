<?php

namespace Diana\Routing;

use Diana\IO\Request;

interface RoutingInterface
{
    public function findRoute(Request $request): ?array;
}