<?php

namespace Uhura;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;

class Uhura
{
    private $api = '';
    private $resource = '';
    private $http;

    public function __construct($api)
    {
        $this->api = $api;
        $this->http = new Client(['base_uri' => $this->api]);
    }

    public static function test($api)
    {
        $uhura = new static($api);

        $uhura->http = new Client([
            'base_uri' => $api,
            'handler' => new MockHandler
        ]);

        return $uhura;
    }

    public function getHttp()
    {
        return $this->http;
    }

    public function get()
    {
        return $this->http->get($this->resource);
    }

    public function create($payload)
    {
        return $this->http->post($this->resource, ['form_params' => $payload]);
    }

    public function update($payload)
    {
        return $this->http->put($this->resource, ['form_params' => $payload]);
    }

    public function delete()
    {
        return $this->http->delete($this->resource);
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
