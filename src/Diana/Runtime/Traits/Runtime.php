<?php

namespace Diana\Runtime\Traits;

use Diana\Runtime\Application;
use Diana\Runtime\Container;
use Diana\Support\Bag;
use Composer\Autoload\ClassLoader;
use Diana\Support\Debug;
use RuntimeException;

trait Runtime
{
    private string $path;

    private bool $hasBooted = false;
    private bool $isRegistered = false;

    public function performRegister(Container $container): void
    {
        if ($this->isRegistered())
            throw new RuntimeException('The runtime [' . get_class($this) . '] has already been registered.');

        $container->call($this::class . '@register');
        $this->isRegistered = true;
    }

    public function performBoot(Container $container): void
    {
        if ($this->hasBooted())
            throw new RuntimeException('The runtime [' . get_class($this) . '] has already been booted.');

        $container->call($this::class . '@boot');
        // $this->boot();
        $this->hasBooted = true;
    }

    /**
     * Gets the absolute path to the project.
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    public function hasBooted(): bool
    {
        return $this->hasBooted;
    }

    public function isRegistered(): bool
    {
        return $this->isRegistered;
    }
}