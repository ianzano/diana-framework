<?php

namespace Diana\Runtime;

use Diana\Runtime\Traits\Runtime;
use Diana\Support\Obj;

abstract class Package extends Obj
{
    use Runtime;

    public function __construct(private string $path)
    {
    }

    public function performRegister(): void
    {
        DependencyInjector::inject($this, 'register');
    }

    public function performBoot(): void
    {
        DependencyInjector::inject($this, 'boot');
        $this->hasBooted = true;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}