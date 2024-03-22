<?php

namespace Diana\Support;

use Diana\Interfaces\Serializable;

use Exception, Error;

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
        return (string) $this->toString();
    }
}