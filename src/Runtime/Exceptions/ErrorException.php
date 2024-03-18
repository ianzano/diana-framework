<?php

namespace Diana\Runtime\Exceptions;

use Diana\Interfaces\Throwable;

use Diana\Support\Debug;

use ErrorException as PHPErrorException;

class ErrorException extends PHPErrorException implements Throwable
{
    public static function throw(): void
    {
        throw new static(...func_get_args());
    }
}