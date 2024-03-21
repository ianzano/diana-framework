<?php

namespace Diana\Contracts;

interface Cache
{
    public function cache();
    public function flush();

    public function exists(): bool;
}