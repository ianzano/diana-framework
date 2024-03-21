<?php

namespace Diana\IO\Traits;

use Diana\Support\Bag;

trait Headers
{
    protected Bag $headers;

    public function setHeader(string $header, mixed $value = null): void
    {
        if (!$value)
            $this->headers = new Bag($header);
        else
            $this->headers[$header] = $value;
    }

    public function getHeader(string $header = null): mixed
    {
        return $header ? $this->header[$header] : $this->header;
    }
}