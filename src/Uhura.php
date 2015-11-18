<?php

namespace Uhura;

class Uhura
{
    private $api = '';
    private $resource = '';

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function url()
    {
        return sprintf("%s%s", $this->api, $this->resource);
    }

    public function __get($name)
    {
        $this->resource .= sprintf("/%s", $name);
        return $this;
    }

    public function __call($method, $args)
    {
        $this->resource .= sprintf("/%s/%s", $method, $args[0]);
        return $this;
    }
}
