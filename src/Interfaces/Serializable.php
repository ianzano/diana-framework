<?php

namespace Diana\Interfaces;

interface Serializable
{

    /**
     * the JSON that will be generated
     */
    public function toJSON(): string;

}