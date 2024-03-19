<?php

namespace Diana\Runtime;

use Composer\Autoload\ClassLoader;

use Diana\Interfaces\Runnable;
use Diana\Runtime\Application;
use Diana\Runtime\Traits\Runtime;
use Diana\Runtime\Traits\Singleton;
use Diana\Support\Blueprints\Driver;
use Diana\Support\Obj;

abstract class Package extends Obj implements Runnable
{
    use Runtime;

    public function __construct(private string $path)
    {
    }

    public function performRegister()
    {
        DependencyInjector::inject($this, 'register');
    }

    public function performBoot()
    {
        DependencyInjector::inject($this, 'boot');
        $this->booted = true;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}