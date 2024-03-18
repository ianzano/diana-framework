<?php

namespace Diana\Runtime\Traits;

use Diana\Runtime\Application;
use Diana\Runtime\Exceptions\EnvironmentException;
use Diana\Support\Debug;

trait Singleton
{
    protected static $instance;

    public static function make(): static
    {
        if (static::$instance)
            EnvironmentException::throw('An instance of ' . static::class . ' does already exist.');

        static::$instance = parent::make(...func_get_args());

        return static::$instance;
    }

    public static function getInstance(): ?static
    {
        return static::$instance;
    }

    public static function getInstanceOrMake(): ?static
    {
        if (!static::$instance)
            static::$instance = parent::make(...func_get_args());

        return static::$instance;
    }
}