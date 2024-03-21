<?php

namespace Diana\Support\Facades;

use Diana\Runtime\Application;

abstract class Facade
{
    public static Application $app;

    public static function __callStatic(string $method, array $args)
    {
        $accessor = static::getFacadeAccessor();
        return self::$app->resolve($accessor)->$method(...$args);
    }

    public static function setApplication(Application $app)
    {
        static::$app = $app;
    }

    abstract static function getFacadeAccessor(): string;
}