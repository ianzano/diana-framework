<?php

namespace Diana\Support\Facades;

class App extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'app';
    }
}