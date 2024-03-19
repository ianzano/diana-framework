<?php

namespace Diana\Runtime\Traits;

use Diana\Runtime\Application;
use Diana\Support\Bag;
use Composer\Autoload\ClassLoader;
use Diana\Support\Debug;

trait Runtime
{
    private string $path;

    protected Bag $meta;

    protected Bag $config;

    private bool $registered = false;
    private bool $booted = false;

    protected ClassLoader $classLoader;

    private function startRuntime(Application $app)
    {
        $this->meta = new Bag(@include ($this->getPath() . DIRECTORY_SEPARATOR . 'meta.php'));

        if ($this->meta->packages)
            $app->registerPackages($this->meta->packages);

        if ($this->meta->controllers)
            $app->registerControllers($this->meta->controllers);
    }

    /**
     * Gets the absolute path to the project.
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    public function isRegistered(): bool
    {
        return $this->registered;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }
}