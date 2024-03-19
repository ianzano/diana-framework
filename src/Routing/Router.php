<?php

namespace Diana\Routing;

use Diana\IO\Request;

interface Router
{
    public function loadRoutes(array $controllers): void;
    public function findRoute(Request $request): ?array;
}