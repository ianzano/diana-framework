<?php

namespace Diana\Kernel;

use Diana\IO\Request;
use Diana\IO\Response;

interface Kernel
{
    public function process(Request $request): Response;
}