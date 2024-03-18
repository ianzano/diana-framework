<?php

namespace Diana\IO;

use Diana\IO\Traits\Headers;
use Diana\Runtime\Exceptions\EnvironmentException;
use Diana\Support\Bag;
use Diana\Support\Debug;
use Diana\Support\Obj;

class Request extends Obj
{
    use Headers;

    protected string $resource = '';
    protected string $route = '';
    protected string $host = '';
    protected string $query = '';
    protected string $protocol;

    public function __construct(
        string $url = '',
        protected string $method = 'GET',
        array|Bag $headers = []
    ) {
        if (($pos = strpos($url, "://")) !== false) {
            $this->protocol = substr($url, 0, $pos);
            $url = substr($url, $pos + 3);

            if (($pos = strpos($url, "/")) !== false) {
                $this->host = substr($url, 0, $pos);
                $url = substr($url, $pos);
            }
        } else
            $this->protocol = strtolower(strtok($_SERVER["SERVER_PROTOCOL"], "/"));

        if (!$this->host)
            $this->host = $_SERVER["HTTP_HOST"];

        if (($pos = strpos($url, "?")) !== false) {
            $this->query = substr($url, $pos + 1);
            foreach (explode("&", $this->query) as $params) {
                $position = strpos($params, "=");
                $_GET[substr($params, 0, $position)] = substr($params, $position + 1);
            }

            $url = substr($url, 0, $pos);
        }

        $this->route = $url ?: $_SERVER["REQUEST_URI"];

        $this->resource = $this->protocol . "://" . $this->host . $this->route . ($this->query ? "?" . $this->query : "");

        $this->headers = new Bag($headers);
    }

    public static function mock()
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_')
                continue;

            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }

        $protocol = strtolower(strtok($_SERVER['SERVER_PROTOCOL'], '/'));

        return Request::make($protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $headers);
    }

    public function getProtocol()
    {
        return $this->protocol;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getRoute()
    {
        return $this->route;
    }
}