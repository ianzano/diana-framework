<?php

namespace Diana\Rendering;

class View
{
    public function __construct(protected Renderer $renderer, protected string $path, protected array $data = [])
    {
    }

    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    public function render()
    {
        try {
            // We will keep track of the number of views being rendered so we can flush
            // the section after the complete rendering operation is done. This will
            // clear out the sections for any separate views that may be rendered.
            $this->renderer->incrementRenderCount();

            $engine = $this->renderer->getEngineFromPath($this->path);

            $contents = $engine->get($this->path, array_merge($this->renderer->getShared(), $this->data));

            // Once we've finished rendering the view, we'll decrement the render count
            // so that each section gets flushed out next time a view is created and
            // no old sections are staying around in the memory of an environment.
            $this->renderer->decrementRenderCount();

            // Once we have the contents of the view, we will flush the sections if we are
            // done rendering all views so that there is nothing left hanging over when
            // another view gets rendered in the future by the application developer.
            if ($this->renderer->doneRendering())
                $this->renderer->flushState();

            return $contents;
        } catch (\Exception $e) {
            $this->renderer->flushState();

            throw $e;
        }
    }
}