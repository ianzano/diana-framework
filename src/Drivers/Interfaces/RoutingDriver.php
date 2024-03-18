<?php

namespace Diana\Drivers\Interfaces;

use Diana\IO\Request;

interface RoutingDriver
{
    public function findEndpoint(Request $request): callable;
}