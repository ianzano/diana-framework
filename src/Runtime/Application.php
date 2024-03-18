<?php

namespace Diana\Runtime;

use Diana\IO\Request;
use Diana\IO\Response;
use Diana\Routing\Router;
use Diana\Runtime\Exceptions\CodeException;
use Diana\Runtime\Exceptions\Exception;
use Diana\Runtime\Exceptions\FatalCodeException;

use Diana\Runtime\Traits\Singleton;
use Diana\Support\Bag;
use Diana\Support\Debug;
use Diana\Support\File;
use Diana\Support\Obj;

class Application extends Obj
{
    use Singleton;

    /**
     * The current request.
     */
    protected Request $request;

    protected Router $router;

    protected array $drivers = [];

    protected Bag $config;

    protected Bag $packages;

    protected function __construct(protected string $path)
    {
        $this->setExceptionHandler();

        $this->loadConfigs('config');
        $this->loadDrivers();

        $this->router = new Router();

        $this->loadPackages();
    }

    private function loadDrivers()
    {
        foreach ($this->config->meta->drivers as $interface => $driver) {
            $this->drivers[$interface] = new $driver;
        }
    }

    private function loadPackages()
    {
        $this->packages = new Bag();
        foreach ($this->config->meta->packages as $package) {
            $this->packages[$package] = new $package;
            $this->packages[$package]->register(); // TODO: Dependency injection here
        }

        foreach ($this->config->meta->packages as $package) {
            $this->packages[$package]->boot(); // TODO: Dependency injection here
        }
    }

    private function setExceptionHandler(): void
    {
        set_exception_handler(function ($error) {
            return Exception::handleException($error);
        });

        register_shutdown_function(function () {
            if ($error = error_get_last()) {
                ob_end_clean();
                FatalCodeException::throw ($error['message'], $error["type"], 0, $error["file"], $error["line"]);
            }
        });

        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            CodeException::throw ($errstr, $errno, 0, $errfile, $errline);
        });
    }

    private function loadConfigs($directory)
    {
        $this->config = new Bag();
        foreach (array_diff(scandir($this->getPath() . DIRECTORY_SEPARATOR . $directory), ['.', '..']) as $file) {
            if (!str_ends_with($file, '.php'))
                continue;

            $this->config[substr($file, 0, -4)] = include ($this->getPath() . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . $file);
        }
    }

    /**
     * Gets the absolute path to the project.
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Gets the current request.
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    public function handleRequest(Request $request): void
    {
        $this->request = $request;

        // TODO: Fire up the router, pass it the request and let it generate a response which then is emitted
        Response::make('test')->emit();
    }
}