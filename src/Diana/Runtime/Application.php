<?php

namespace Diana\Runtime;

use Composer\Autoload\ClassLoader;

use Diana\Cache\Facades;
use Diana\Interfaces\Runnable;
use Diana\IO\Request;

use Diana\Contracts\Kernel;
use Diana\Support\Debug;
use Diana\Support\Facades\Facade;

use Diana\Runtime\Traits\Runtime;
use Diana\Support\Bag;

class Application extends Container implements Runnable
{
    use Runtime;

    const VERSION = "1.0.0";

    protected array $packages = [];

    protected array $controllers = [];

    protected function __construct(private string $path, protected ClassLoader $classLoader)
    {
        $this->setExceptionHandler();

        $this->registerBindings();

        $this->provideFacades();
    }

    public function getVersion()
    {
        return static::VERSION;
    }

    protected function provideFacades()
    {
        $this->resolve(Facades::class)->provide();
    }

    public static function make(string $path, ClassLoader $classLoader): static
    {
        // initializes the application
        $app = new static($path, $classLoader);

        // initializes all packages
        $app->registerPackage(\AppPackage::class);

        // registers all packages
        $app->performRegister($app);

        // boots all packages
        $app->performBoot($app);

        return $app;
    }

    protected function registerBindings(): void
    {
        static::setInstance($this);
        Facade::setApplication($this);
        $this->instance('app', $this);

        foreach (['app' => [Application::class, Container::class], 'kernel' => [Kernel::class]] as $key => $aliases) {
            foreach ($aliases as $alias)
                $this->alias($key, $alias);
        }
    }

    public function registerPackage(...$classes): void
    {
        foreach ((new Bag($classes))->flat() as $class) {
            if (in_array($class, $this->packages))
                continue;

            $this->packages[] = $class;

            $this->singleton($class);
            $package = $this->resolve($class)->withPath($this->classLoader);

            if ($this->isRegistered())
                $package->performRegister($this);

            if ($this->hasBooted())
                $package->performBoot($this);
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
        foreach ($this->packages as $package)
            $this->resolve($package)->performRegister($this); // $app->call()
    }

    public function boot(): void
    {
        foreach ($this->packages as $package)
            $this->resolve($package)->performBoot($this);
    }

    protected function setExceptionHandler(): void
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
        $kernel = $this->resolve('kernel');

        $response = $kernel->process($request);
        $response->emit();

        // $kernel->terminate($request, $response);
    }

    public function getControllers()
    {
        return new Bag($this->controllers);
    }

    public function getPackages()
    {
        return new Bag($this->packages);
    }
}