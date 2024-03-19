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
    use Singleton, Runtime;

    public function __construct(protected Application $app, protected ClassLoader $classLoader)
    {
        $this->path = dirname($classLoader->findFile($this::class), 2);

        $this->startRuntime($app);
    }

    public function registerPackage(string ...$classes): void
    {
        $this->app->registerPackages(...$classes);
    }

    public function registerControllers(string ...$controllers): void
    {
        $this->app->registerControllers(...$controllers);
    }

    public function registerDriver(string $driverName, Driver $driver): void
    {
        $this->app->registerDriver($driverName, $driver);
    }

    public function registerDrivers(array $drivers): void
    {
        $this->app->registerDrivers($drivers);
    }

    public function getPath(): string
    {
        return $this->path;
    }
}