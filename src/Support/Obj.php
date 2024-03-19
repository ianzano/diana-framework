<?php

namespace Diana\Support;

use Diana\Interfaces\Instantiable;
use Diana\Interfaces\Serializable;

use Error as PHPError;
use Exception as PHPException;

use Diana\Runtime\Exceptions\Exception;

use stdClass as PHPObj;

class Obj extends PHPObj implements Serializable
{
    /**
     * Implement a default serialization method.
     * @return string
     */
    public function toJSON(): string
    {
        return json_encode($this);
    }

    /**
     * Implement a default print method.
     * @return string
     */
    public function toString(): string
    {
        return $this->toJSON(); // TODO: use a formatted version of this later
    }

    /**
     * The string cast implementation.
     * This forces the user to use the custom toString method provided by the Stringable Prototype,
     * in order to allow exception handling in it.
     * @return string
     */
    public final function __toString(): string
    {
        try {
            return (string) $this->toString();
        } catch (PHPException | PHPError $e) {
            Exception::throw($e);
            return '';
        }
    }
}