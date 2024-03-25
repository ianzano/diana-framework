<?php

namespace Diana\Support\Facades;

class Kernel extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'kernel';
    }
}