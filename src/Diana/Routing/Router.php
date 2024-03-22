<?php

namespace Diana\Routing;

use Diana\IO\Request;

interface Router
{
    public function loadRoutes(): void;
    public function findRoute(Request $request): ?array;
}