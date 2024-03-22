<?php

namespace Diana\Contracts;

use Closure;
use Diana\IO\Request;
use Diana\IO\Response;

interface Kernel
{
    public function process(Request $request): Response;

    public function registerMiddleware(string|Closure $middleware): void;
}