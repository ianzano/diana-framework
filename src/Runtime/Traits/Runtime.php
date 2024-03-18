<?php

namespace Diana\Runtime\Traits;

use Diana\Support\Bag;
use Composer\Autoload\ClassLoader;

trait Runtime
{
    private string $path;

    protected Bag $meta;

    protected Bag $config;

    protected ClassLoader $classLoader;

    protected function loadMeta()
    {
        $this->meta = new Bag(include ($this->getPath() . DIRECTORY_SEPARATOR . 'meta.php'));
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