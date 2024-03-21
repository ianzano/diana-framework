<?php

namespace Diana\Contracts;

use Closure;
use Diana\IO\Request;
use Diana\IO\Response;

interface Middleware
{
    public function run(Request $request, Closure $next);
}