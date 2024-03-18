<?php

namespace Diana\Interfaces;

interface Runnable
{
    public function register(): void;
    public function boot(): void;

    public function getPath(): string;
}