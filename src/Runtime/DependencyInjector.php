<?php

namespace Diana\Runtime;

use Diana\Support\Debug;
use Diana\Runtime\Application;

use ReflectionClass;
use ReflectionMethod;

class DependencyInjector
{
    public static function inject(mixed $object, string $method): mixed
    {
        $reflection = new ReflectionMethod($object, $method);
        $params = [];

        foreach ($reflection->getParameters() as $param) {
            $dependency = $param->getType()->getName();

            if ((new ReflectionClass($dependency))->isInterface())
                $params[] = Application::getInstance()->getDriver($dependency);
            else
                $params[] = $dependency::getInstance();
        }

        return $object->$method(...$params);
    }
}