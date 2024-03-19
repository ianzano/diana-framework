<?php

namespace Diana\Interfaces;

interface Instantiable
{

    /**
     * Factory function to construct the object.
     */
    public static function make(): static;

}