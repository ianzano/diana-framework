<?php

namespace Diana\Runtime;

use Composer\Autoload\ClassLoader;

use Diana\Interfaces\Runnable;
use Diana\IO\Request;
use Diana\IO\Response;
use Diana\Routing\Router;
use Diana\Runtime\Exceptions\CodeException;
use Diana\Runtime\Exceptions\Exception;
use Diana\Runtime\Exceptions\FatalCodeException;

use Diana\Runtime\Traits\Runtime;
use Diana\Runtime\Traits\Singleton;
use Diana\Support\Bag;
use Diana\Support\Debug;
use Diana\Support\File;
use Diana\Support\Obj;
use SamplePackage;

class Application extends Obj implements Runnable
{
    use Singleton, Runtime;

    /**
     * The current request.
     */
    protected Request $request;

    protected Router $router;

    protected array $drivers = [];

    protected Bag $packages;

    protected function __construct(private string $path, protected ClassLoader $classLoader)
    {
        $this->setExceptionHandler();

        $this->load();
    }

    public function load(): void
    {
        $this->loadMeta();
        $this->router = new Router();

        $this->packages = new Bag();
        foreach ($this->meta->packages as $class)
            $this->loadPackage($class);

        foreach ($this->meta->drivers as $interface => $driver)
            $this->drivers[$interface] = new $driver;

        $this->register();
    }

    public function loadPackage(string $class)
    {
        if (!$class::getInstance()) {
            $this->packages->$class = $class::make($this, $this->classLoader);
            $this->packages->$class->load();
        }
    }

    public function register(): void
    {
        foreach ($this->packages as $package)
            $package->register();

        $this->boot();
    }

    public function boot(): void
    {
        foreach ($this->packages as $package)
            $package->boot();
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