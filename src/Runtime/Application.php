<?php

namespace Diana\Runtime;

use Composer\Autoload\ClassLoader;

use Diana\Drivers\Router;
use Diana\Interfaces\Runnable;
use Diana\IO\Request;
use Diana\IO\Response;

use Diana\Runtime\Exceptions\RuntimeException;
use Diana\Runtime\Traits\Runtime;
use Diana\Runtime\Traits\Singleton;
use Diana\Support\Bag;
use Diana\Support\Blueprints\Driver;
use Diana\Support\Debug;
use Diana\Support\File;
use Diana\Support\Obj;
use SamplePackage;

use ReflectionMethod;

class Application extends Obj implements Runnable
{
    use Runtime;

    public static $instance;

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
    }

    public static function make(string $path, ClassLoader $classLoader): static
    {
        self::$instance = new static($path, $classLoader);
        self::$instance->register(); // start the lifecycle OUTSIDE of the constructor
        return self::$instance;
    }

    public function registerPackage(...$classes): void
    {
        foreach ((new Bag($classes))->flat() as $class) {
            if (in_array($class, $this->packages))
                continue;

            $this->packages[$class] = new $class(dirname($this->classLoader->findFile($class), 2), $this);
            $this->packages[$class]->performRegister();


            if (!$this->packages[$class]->isBooted() && $this->booted)
                $this->packages[$class]->performBoot();
        }
    }

    public function registerController(...$controllers): void
    {
        foreach ((new Bag($controllers))->flat() as $controller) {
            if (!in_array($controller, $this->controllers))
                $this->controllers[] = $controller;
        }
    }

    public function register(): void
    {
        $this->registerPackage(\AppPackage::class);

        // boot the application
        $this->boot();
    }

    public function boot(): void
    {
        foreach ($this->packages as $package)
            $package->performBoot();

        $this->booted = true;
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

        // execute the middleware, on of them is RoutingMiddleware who takes care of routing

        // $route = $this->drivers[Router::class]->findRoute($request);

        // if (!$route) {
        //     Response::make("404")->emit();
        //     return;
        // }

        // $result = (new $route['controller']())->{$route['method']}();

        // TODO: Fire up the router, pass it the request and let it generate a response which then is emitted
        (new Response('zdz'))->emit();
    }

    public function registerDriver(string $driverName, Driver $driver): void
    {
        $this->drivers[$driverName] = $driver;
    }

    public function getDriver(string $driverName): Driver
    {
        return $this->drivers[$driverName];
    }

    public function getControllers()
    {
        return $this->controllers;
    }

    public static function getInstance()
    {
        return self::$instance;
    }
}