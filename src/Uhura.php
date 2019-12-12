<?php

namespace Uhura;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;

class Uhura
{
    private $api;
    private $http;
    private $responseHandler;
    private $resourceSuffix;

    private $resource = [];
    private $token = null;
    private $headers = [];
    private $payLoad = 'json';

    public function __construct($api)
    {
        $this->setApiRoot($api);
        $this->http = new Client(['base_uri' => $this->api]);
        $this->responseHandler = new ResponseHandler\Passthru;
    }

    function setPayloadType($type = null)
    {
        $this->payLoad = $type;
    }

    public static function test($api)
    {
        $uhura = new static($api);

        $uhura->http = new Client([
            'base_uri' => $uhura->api,
            'handler' => new MockHandler
        ]);

        return $uhura;
    }

    function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    protected function setApiRoot($api)
    {
        $this->api = strrev($api)[0] == '/' ? $api : $api . '/';
    }

    public function useResponseHandler($handler)
    {
        $this->responseHandler = $handler;
    }

    public function useResourceSuffix($suffix)
    {
        $this->resourceSuffix = $suffix;
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
        return sprintf("%s%s", $this->api, $this->getResource());
    }

    public function getResource()
    {
        return implode('/', $this->resource) . $this->resourceSuffix;
    }

    public function reset()
    {
        $this->resource = [];
    }

    private function request($method, $payload = null)
    {
        $response = $this->responseHandler->handle(
            $this->http->request($method, $this->getResource(), $this->buildOptionsForRequest($method, $payload))
        );

        $this->reset();

        return $response;
    }

    private function buildOptionsForRequest($method, $payload)
    {
        $options = [];
        if ($this->token) {
            $options['headers'] = ['Authorization' => $this->token];
        }

        if (!empty($this->headers)) {
            foreach ($this->headers as $name => $value) {
                $options['headers'][$name] = $value;
            }
        }
        if ($payload) {
            $key = $method == 'GET' ? 'query' : 'form_params';
            if ($method != 'GET') {
                if ($this->payLoad == 'json') {
                    $options['json'] = $payload;
                } else {
                    $options[$key] = $payload;
                }

            } else {
                $options[$key] = $payload;
            }
        }

        //    dd($options);
     //   $options['debug'] = true;
        return $options;
    }

    protected function appendToResource(/* $args */)
    {
        $this->resource = array_merge($this->resource, func_get_args());
    }

    public function __get($name)
    {
        $this->appendToResource($name);
        return $this;
    }

    public function __call($method, $args)
    {
        $this->appendToResource($method, $args[0]);
        return $this;
    }
}
