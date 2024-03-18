<?php

namespace Diana\Runtime;

use Composer\Autoload\ClassLoader;

use Diana\Interfaces\Runnable;
use Diana\Runtime\Application;
use Diana\Runtime\Traits\Runtime;
use Diana\Runtime\Traits\Singleton;
use Diana\Support\Obj;

abstract class Package extends Obj implements Runnable
{
    use Singleton, Runtime;

    public function __construct(private Application $app, protected ClassLoader $classLoader)
    {
        $this->path = dirname($classLoader->findFile($this::class), 2);
    }

    public function load(): void
    {
        $this->loadMeta();

        foreach ($this->meta->packages as $class)
            $this->app->loadPackage($class);
    }

    public function register(): void
    {
    }

    public function boot(): void
    {

    }

    public function getPath(): string
    {
        return $this->path;
    }
}