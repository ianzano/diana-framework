<?php

namespace Diana\Support\Packages\Routing\Attributes;

abstract class Route
{
    public function __construct(protected string $path)
    {

    }
}