<?php

namespace Diana\Interfaces;

use Diana\Runtime\Container;

interface Runnable
{
    // Dependency injection
    // public function register(): void;
    // public function boot(): void;

    public function performRegister(Container $container): void;
    public function performBoot(Container $container): void;

    public function isRegistered(): bool;
    public function hasBooted(): bool;
}