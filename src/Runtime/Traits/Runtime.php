<?php

namespace Diana\Runtime\Traits;

use Diana\Runtime\Application;
use Diana\Support\Bag;
use Composer\Autoload\ClassLoader;
use Diana\Support\Debug;

trait Runtime
{
    private string $path;

    protected Bag $config;

    private bool $booted = false;

    /**
     * Gets the absolute path to the project.
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }
}