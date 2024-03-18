<?php

namespace Diana\Runtime\Traits;

use Diana\Runtime\Application;
use Diana\Support\Bag;
use Composer\Autoload\ClassLoader;

trait Runtime
{
    private string $path;

    protected Bag $meta;

    protected Bag $config;

    protected ClassLoader $classLoader;

    private function startRuntime(Application $app)
    {
        $this->meta = new Bag(include ($this->getPath() . DIRECTORY_SEPARATOR . 'meta.php'));

        foreach ($this->meta->packages as $class)
            $app->loadPackage($class);
    }

    /**
     * Gets the absolute path to the project.
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}