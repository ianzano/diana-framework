<?php

namespace Diana\Runtime\Exceptions;

use Diana\Interfaces\Throwable;

use Diana\Support\Debug;

use Exception as PHPException;
use Error as PHPError;

class Exception extends PHPException implements Throwable
{
    public static function throw(): void
    {
        throw new static(...func_get_args());
    }

    public static function handleException(PHPException|PHPError $error)
    {
        echo Debug::container('', 'Something went wrong.');

        $message = '';
        for ($i = 0; $i < count($trace = $error->getTrace()); $i++) {
            $message .= ($i + 1) . '. at ' .
                (isset ($trace[$i]['class']) ? substr($trace[$i]['class'], strrpos($trace[$i]['class'], '\\') + 1) : '') .
                (isset ($trace[$i]['type']) ? ' ' . $trace[$i]['type'] . ' ' : '') .
                $trace[$i]['function'] .
                (isset ($trace[$i]['file']) ? ' in ' . substr($trace[$i]['file'], strrpos($trace[$i]['file'], '\\') + 1) : '') .
                (isset ($trace[$i]['file']) ? ' on line ' . $trace[$i]['line'] : '') . "\n";
        }

        if (strstr($class = get_class($error), '\\'))
            $class = substr($class, strrpos($class, '\\') + 1);

        if (strstr($file = $error->getFile(), DIRECTORY_SEPARATOR))
            $file = substr($file, strrpos($file, DIRECTORY_SEPARATOR) + 1);

        echo Debug::container($class . ' in ' . $file . ' on line ' . $error->getLine(), $error->getMessage() . '<p>' . $message);
    }
}