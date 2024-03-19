<?php

namespace Diana\Runtime;

use Composer\Autoload\ClassLoader;

use Diana\Routing\RoutingInterface;
use Diana\Interfaces\Runnable;
use Diana\IO\Request;
use Diana\IO\Response;

use Diana\Runtime\Traits\Runtime;
use Diana\Runtime\Traits\Singleton;
use Diana\Support\Bag;
use Diana\Support\Debug;
use Diana\Support\File;
use Diana\Support\Obj;
use SamplePackage;

use ReflectionMethod;

class Application extends Obj implements Runnable
{
    use Singleton {
        make as makeSingleton;
    }

    use Runtime;

    /**
     * The current request.
     */
    protected Request $request;

    protected array $drivers = [];

    protected array $packages = [];

    protected array $controllers = [];

    protected function __construct(private string $path, protected ClassLoader $classLoader)
    {
        $this->setExceptionHandler();

        $this->startRuntime($this);
    }

    public static function make(): static
    {
        $app = self::makeSingleton(...func_get_args());
        $app->register(); // start the lifecycle OUTSIDE of the constructor
        return $app;
    }

    public function loadPackage(string $class): void
    {
        if (!$class::getInstance())
            $this->packages[$class] = $class::make($this, $this->classLoader);
    }

    public function addController(string $controller): void
    {
        if (!in_array($controller, $this->controllers))
            $this->controllers[] = $controller;
    }

    public function register(): void
    {
        // TODO: register the drivers
        $this->drivers[RoutingInterface::class] = $this->meta->drivers[RoutingInterface::class]::make();

        // register the packages
        foreach ($this->packages as $package)
            DependencyInjector::inject($package, 'register');

        // boot the application
        $this->boot();
    }

    public function boot(): void
    {
        $this->drivers[RoutingInterface::class]->loadRoutes($this->controllers);

        foreach ($this->packages as $package)
            DependencyInjector::inject($package, 'boot');
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

    public function getDriver(string $driver)
    {
        return $this->drivers[$driver];
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

        $route = $this->drivers[RoutingInterface::class]->findRoute($request);

        $result = (new $route['controller']())->{$route['method']}();

        // TODO: Fire up the router, pass it the request and let it generate a response which then is emitted
        Response::make($result)->emit();
    }
}