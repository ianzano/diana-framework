<?php

namespace Diana\Rendering\Components;

use Closure;
use Diana\Rendering\Compiler;
use Diana\Rendering\Renderer;
use Diana\Rendering\View;
use Diana\Runtime\Application;
use Diana\Runtime\Container;
use Diana\Rendering\ComponentTagCompiler;
use Diana\Support\Debug;
use Diana\Support\Helpers\Str;
use App;

class AnonymousComponent extends Component
{
    public function __construct(public Application $app, public Renderer $renderer, public string $view, public array $data = [])
    {

    }

    public function render(): View
    {
        return $this->renderer->make(
            join(DIRECTORY_SEPARATOR, [$this->app->getPath(), Str::replace(':', DIRECTORY_SEPARATOR, $this->view)])
        );
    }

    /**
     * Get the data that should be supplied to the view.
     *
     * @return array
     */
    public function data()
    {
        $this->attributes = $this->attributes ?: $this->newAttributeBag();

        return array_merge(
            ($this->data['attributes'] ?? null)?->getAttributes() ?: [],
            $this->attributes->getAttributes(),
            $this->data,
            ['attributes' => $this->attributes]
        );
    }
}
