<?php

namespace Diana\Support\Packages\Routing\Drivers;

use Diana\IO\Request;

interface Router
{
    public function loadRoutes(array $controllers): void;
    public function findRoute(Request $request): ?array;
}