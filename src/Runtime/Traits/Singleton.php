<?php

namespace Diana\Runtime\Traits;

use Diana\Interfaces\Instantiable;
use Diana\Runtime\Application;
use Diana\Runtime\Exceptions\RuntimeException;
use Diana\Support\Debug;

trait Singleton
{
    protected static $instance;

    public static function make(): self
    {
        if (static::$instance)
            throw new RuntimeException('An instance of ' . static::class . ' does already exist.');

        static::$instance = parent::make(...func_get_args());

        return static::$instance;
    }

    public static function getInstance(): ?self
    {
        return static::$instance;
    }

    public static function getInstanceOrMake(): ?self
    {
        if (!static::$instance)
            static::$instance = parent::make(...func_get_args());

        return static::$instance;
    }
}