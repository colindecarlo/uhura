<?php

namespace Uhura;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;

class Uhura
{
    private $api;
    private $http;
    private $responseHandler;

    private $resource = '';
    private $token = null;

    public function __construct($api)
    {
        $this->api = $api;
        $this->http = new Client(['base_uri' => $this->api]);
        $this->responseHandler = new ResponseHandler\Passthru;
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

    public function useResponseHandler($handler)
    {
        $this->responseHandler = $handler;
    }

    public function useBasicAuthentication($username, $password)
    {
        $token = base64_encode(sprintf("%s:%s", $username, $password));
        return $this->authenticate(sprintf("Basic %s", $token));
    }

    public function authenticate($token)
    {
        $this->token = $token;
        return $this;
    }

    public function getHttp()
    {
        return $this->http;
    }

    public function get($queryParams = [])
    {
        return $this->request('GET', $queryParams);
    }

    public function create($payload)
    {
        return $this->request('POST', $payload);
    }

    public function update($payload)
    {
        return $this->request('PUT', $payload);
    }

    public function delete()
    {
        return $this->request('DELETE');
    }

    public function url()
    {
        return sprintf("%s%s", $this->api, $this->resource);
    }

    private function request($method, $payload = null)
    {
        $options = $this->buildOptionsForRequest($method, $payload);

        return $this->responseHandler->handle(
            $this->http->request($method, $this->resource, $options)
        );
    }

    private function buildOptionsForRequest($method, $payload)
    {
        $options = [];
        if ($this->token) {
            $options['headers'] = ['Authorization' => $this->token];
        }

        if ($payload) {
            $key = $method == 'GET' ? 'query' : 'form_params';
            $options[$key] = $payload;
        }

        return $options;
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
