<?php

namespace Diana\Runtime;

use Composer\Autoload\ClassLoader;

use Diana\IO\Request;
use Diana\IO\Response;

use Diana\Runtime\Traits\Runtime;
use Diana\Support\Bag;
use Diana\Support\Blueprints\Driver;
use Diana\Support\Debug;
use Diana\Support\Obj;

use Diana\Routing\Router;

class Application extends Container
{
    use Runtime;

    protected array $packages = [];

    protected array $controllers = [];

    protected function __construct(private string $path, protected ClassLoader $classLoader)
    {
        $this->setExceptionHandler();

        static::setInstance($this);
        $this->instance(Application::class, $this);
        $this->instance(Container::class, $this);

        $this->registerPackage(\AppPackage::class);

        // boot the application
        $this->boot();
    }

    public static function make(string $path, ClassLoader $classLoader): static
    {
        return new static($path, $classLoader);
    }

    public function registerPackage(...$classes): void
    {
        foreach ((new Bag($classes))->flat() as $class) {
            if (in_array($class, $this->packages))
                continue;

            $this->packages[] = $class;

            $this->singleton($class);
            $package = $this->resolve($class)->withPath($this->classLoader);

            if ($this->hasBooted())
                $package->performBoot();
        }
    }

    public function registerController(...$controllers): void
    {
        foreach ((new Bag($controllers))->flat() as $controller) {
            if (!in_array($controller, $this->controllers))
                $this->controllers[] = $controller;
        }
    }

    public function boot(): void
    {
        foreach ($this->packages as $package)
            $this->resolve($package)->performBoot();

        $this->hasBooted = true;
    }

    public function hasBooted(): bool
    {
        return $this->hasBooted;
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

    public function handleRequest(Request $request): void
    {
        // TODO: execute the middleware, on of them is RoutingMiddleware who takes care of routing

        $route = $this->resolve(Router::class)->findRoute($request);


        if (!$route) {
            (new Response("404"))->emit();
            return;
        }

        $result = (new $route['controller']())->{$route['method']}();

        // TODO: Fire up the router, pass it the request and let it generate a response which then is emitted
        (new Response($result))->emit();
    }

    public function getControllers(): array
    {
        return $this->controllers;
    }
}