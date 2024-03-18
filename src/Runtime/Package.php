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

    public function __construct($app, protected ClassLoader $classLoader)
    {
        $this->path = dirname($classLoader->findFile($this::class), 2);

        $this->startRuntime($app);
    }

    public function getPath(): string
    {
        return $this->path;
    }
}