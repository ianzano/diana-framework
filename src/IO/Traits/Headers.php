<?php

namespace Diana\IO\Traits;

use Diana\Runtime\Exceptions\EnvironmentException;
use Diana\Support\Bag;
use Diana\Support\Debug;

trait Headers
{
    protected Bag $headers;

    public function setHeader($header, mixed $value = null)
    {
        if (!$value)
            $this->headers = new Bag($header);
        else
            $this->headers[$header] = $value;
    }

    public function getHeader($header = null)
    {
        return $header ? $this->header[$header] : $this->header;
    }
}