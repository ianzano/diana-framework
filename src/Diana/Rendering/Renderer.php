<?php

namespace Diana\Rendering;

interface Renderer
{
    public function render(string $view, array $data = []): string;
}