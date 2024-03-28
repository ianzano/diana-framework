<?php

namespace Diana\Rendering;

use App;
use Diana\Rendering\Components\Component;
use Diana\Rendering\Components\DynamicComponent;
use Diana\Rendering\Engines\CompilerEngine;
use Diana\Rendering\Engines\FileEngine;
use Diana\Rendering\Engines\PhpEngine;
use Diana\Runtime\Application;
use Diana\Runtime\Package;

use Illuminate\Filesystem\Filesystem;

class RenderingPackage extends Package
{

    public function __construct(private Application $app)
    {
        $app->terminating(static function () {
            Component::flushCache();
        });

        $filesystem = new Filesystem;

        $compiler = new Compiler(
            $filesystem,
            join(DIRECTORY_SEPARATOR, [$app->getPath(), "cache", "blade"]),
            $app->getPath(),
            false,
            'php'
        );
        $app->instance(Compiler::class, $compiler);
        $compiler->component('dynamic-component', DynamicComponent::class);

        $resolver = new EngineResolver;
        $resolver->register('file', fn() => new FileEngine($filesystem));
        $resolver->register('php', fn() => new PhpEngine($filesystem));
        $resolver->register('blade', function () use ($compiler, $filesystem) {
            $engine = new CompilerEngine($compiler, $filesystem);

            $this->app->terminating(static fn() => $engine->forgetCompiledOrNotExpired());

            return $engine;
        });

        $driver = new Driver($resolver, $compiler);

        $driver->directive("vite", function ($entry) {
            $entry = trim($entry, "\"'");

            $env = "prod";
            $vite_host = 'http://localhost:3000';

            if ($env == "dev") {
                return
                    '<script type="module">
                        import RefreshRuntime from "' . $vite_host . '/@react-refresh"
                        RefreshRuntime.injectIntoGlobalHook(window)
                        window.$RefreshReg$ = () => {}
                        window.$RefreshSig$ = () => (type) => type
                        window.__vite_plugin_react_preamble_installed__ = true
                    </script>
                    <script type="module" src="' . $vite_host . '/@vite/client"></script>
                    <script type="module" src="' . $vite_host . '/' . $entry . '"></script>';
            } else {
                $content = file_get_contents(App::getPath() . '/dist/.vite/manifest.json');
                $manifest = json_decode($content, true);

                $script = isset ($manifest[$entry]) ? "<script type=\"module\" src=\"" . $manifest[$entry]['file'] . "\"></script>" : "";

                foreach ($manifest[$entry]['imports'] ?? [] as $imports) $script .= "\n<link rel=\"modulepreload\" href=\"/" . $manifest[$imports]['file'] . "\">";
                foreach ($manifest[$entry]['css'] ?? [] as $file) $script .= "\n<link rel=\"stylesheet\" href=\"/$file\">";

                return $script;
            }
        });

        $app->instance(Renderer::class, $driver);
    }

    public function register()
    {

    }

    public function boot()
    {

    }
}