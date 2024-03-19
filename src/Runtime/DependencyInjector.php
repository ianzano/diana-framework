<?php

namespace Diana\Runtime;

use Diana\Support\Debug;
use Diana\Runtime\Application;

use ReflectionClass;
use ReflectionMethod;

class DependencyInjector
{
    private static function buildParams(object|string $object, string $method): array
    {
        $reflection = new ReflectionMethod($object, $method);
        $params = [];

        foreach ($reflection->getParameters() as $param) {
            $dependency = $param->getType()->getName();

            $params[] = match (true) {
                (new ReflectionClass($dependency))->isInterface() => Application::getInstance()->getDriver($dependency),
                default => $dependency::getInstance()
            };
        }

        return $params;
    }

    public static function inject(object $object, string $method): mixed
    {
        return $object->$method(...self::buildParams($object, $method));
    }

    public static function injectConstructor(string $class): mixed
    {
        return new $class(...self::buildParams($class, '__construct'));
    }
}