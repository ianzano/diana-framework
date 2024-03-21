<?php

namespace Diana\Support\Facades;

use Diana\Runtime\Application;

class App extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return Application::class;
    }
}