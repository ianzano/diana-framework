<?php

namespace Diana\Runtime;

use Composer\Autoload\ClassLoader;

use Diana\Drivers\Interfaces\RoutingDriver;
use Diana\Interfaces\Runnable;
use Diana\IO\Request;
use Diana\IO\Response;
use Diana\Routing\Router;
use Diana\Runtime\Exceptions\CodeException;
use Diana\Runtime\Exceptions\EnvironmentException;
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

    protected RoutingDriver $router;

    protected array $packages = [];

    protected function __construct(private string $path, protected ClassLoader $classLoader)
    {
        $this->setExceptionHandler();

        $this->startRuntime($this);

        $this->register();
    }

    public function loadPackage(string $class): void
    {
        if (!$class::getInstance())
            $this->packages[$class] = $class::make($this, $this->classLoader);
    }

    public function register(): void
    {
        // TODO: register the drivers
        $this->router = $this->meta->drivers[RoutingDriver::class]::make();

        // register the packages
        foreach ($this->packages as $package)
            $package->register();

        // boot the application
        $this->boot();
    }

    public function boot(): void
    {
        foreach ($this->packages as $package)
            $package->boot();
    }

    private function setExceptionHandler(): void
    {
        // TODO: clean up
        error_reporting(E_ALL);
        ini_set('display_errors', true ? 'On' : 'Off');
        ini_set('log_errors', 'On');
        ini_set('error_log', $this->getPath() . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'error.log');
        ini_set('access_log', $this->getPath() . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'access.log');
        ini_set('date.timezone', 'Europe/Berlin');

        ini_set('xdebug.var_display_max_depth', 10);
        ini_set('xdebug.var_display_max_children', 256);
        ini_set('xdebug.var_display_max_data', 1024);
        //ini_set('xdebug.max_nesting_level', 9999);

        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();

        // set_exception_handler(function ($error) {
        //     return Exception::handleException($error);
        // });

        // register_shutdown_function(function () {
        //     if ($error = error_get_last()) {
        //         ob_end_clean();
        //         FatalCodeException::throw ($error['message'], $error["type"], 0, $error["file"], $error["line"]);
        //     }
        // });

        // set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        //     CodeException::throw ($errstr, $errno, 0, $errfile, $errline);
        // });
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