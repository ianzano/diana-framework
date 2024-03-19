<?php

namespace Diana\Interfaces;

interface Runnable
{
    // TODO: Find a way to integrate them with variable params for dependency injection
    // public function register(): void;
    // public function boot(): void;

    public function getPath(): string;
}