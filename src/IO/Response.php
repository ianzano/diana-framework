<?php

namespace Diana\IO;

use Diana\IO\Traits\Headers;
use Diana\Support\Bag;
use Diana\Support\Obj;

class Response extends Obj
{
    use Headers;

    public function __construct(protected string $response = "", array|Bag $headers = [])
    {
        $this->headers = new Bag($headers);
    }

    public function emit(): void
    {
        echo $this->response;
    }

    public function toString(): string
    {
        return $this->response;
    }

    public function set($response)
    {
        $this->response = $response;
    }

}