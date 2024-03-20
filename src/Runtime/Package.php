<?php

namespace Diana\Runtime;

use Diana\Interfaces\Runnable;
use Diana\Runtime\Traits\Runtime;
use Diana\Support\Obj;

abstract class Package extends Obj implements Runnable
{
    use Runtime;

    public function withPath($classLoader)
    {
        $this->path = dirname($classLoader->findFile($this::class), 2);
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}