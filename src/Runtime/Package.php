<?php

namespace Diana\Runtime;

use Diana\Runtime\Traits\Runtime;
use Diana\Support\Obj;

abstract class Package extends Obj
{
    use Runtime;


    public function performBoot(): void
    {
        $this->boot();
        $this->hasBooted = true;
    }

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