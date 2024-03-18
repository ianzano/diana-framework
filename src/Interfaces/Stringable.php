<?php

namespace Diana\Interfaces;

interface Stringable
{
    /**
     * the output that should be printed
     */
    public function toString(): string;
}