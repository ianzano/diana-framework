<?php

namespace Diana\Cache;

use Diana\Contracts\Cache;
use Diana\Runtime\Application;

use Diana\Rendering\Components\DynamicComponent;
use Diana\Support\Facades\App;

class Aliases implements Cache
{
    private $CONFIG_CACHE_FILE;
    private $CONFIG_ALIASES = [
        App::class,
        DynamicComponent::class
    ];

    public function __construct(private Application $app)
    {
        $this->CONFIG_CACHE_FILE = $this->app->getPath() . '/cache/aliases.php';
    }

    public function provide($cached = true)
    {
        foreach ($this->CONFIG_ALIASES as $facade)
            class_alias($facade, substr($facade, strrpos($facade, '\\') + 1));
    }

    public function cache()
    {
        $cache = "<?php\n\n";

        foreach ($this->CONFIG_ALIASES as $facade)
            $cache .= "class " . substr($facade, strrpos($facade, '\\') + 1) . " extends $facade {}\n";

        file_put_contents($this->CONFIG_CACHE_FILE, $cache);
    }

    public function flush()
    {
        unlink($this->CONFIG_CACHE_FILE);
    }

    public function exists(): bool
    {
        return file_exists($this->CONFIG_CACHE_FILE);
    }
}