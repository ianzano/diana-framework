<?php

namespace Diana\Support;

use Diana\Runtime\Application;
use Diana\Runtime\Exceptions\FileNotFoundException;

class File extends Bag
{
    /**
     * Includes a configuration file
     * @param string $file
     * @param callable $callable
     * @param array $params
     * @throws FileNotFoundException
     * @return mixed
     */
    public static function config(string $file, ?callable $callable = null)
    {
        try {
            $include = include (Application::getInstance()->getPath() . DIRECTORY_SEPARATOR . $file . '.php');
        } catch (\Exception $e) {
            FileNotFoundException::throw('The configuration file "' . $file . '.php" could not have been found');
        }

        $file = new File($include);

        return is_callable($callable) ? $callable($file) : $file;
    }

    public static function json(string $file, callable $callable = null)
    {
        $content = @file_get_contents(Environment::fetch(Project::class)->getPath() . $file . '.json');

        if (!$content)
            throw new FileNotFoundException('The configuration file "' . $file . '.json" could not have been found');

        $content = bag(json_decode($content));
        return is_callable($callable) ? $callable($content) : $content;
    }

    /**
     * Formats a file location properly
     * @param string $file
     * @return string
     */
    public static function path(string $file)
    {
        return substr($file, -1) != DIRECTORY_SEPARATOR ? $file . DIRECTORY_SEPARATOR : $file;
    }
}