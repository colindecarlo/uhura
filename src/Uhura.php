<?php

namespace Uhura;

use Zttp\ZttpRequest;

class Uhura
{
    private $api;
    private $responseHandler;

    private $resource = [];
    private $token = null;

    public function __construct($api)
    {
        $this->setApiRoot($api);
        $this->responseHandler = new ResponseHandler\Passthru;
    }

    protected function setApiRoot($api)
    {
        $this->api = strrev($api)[0] == '/' ? $api : $api . '/';
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
        return $this->request('PATCH', $payload);
    }

    public function replace($payload)
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
        return implode('/', $this->resource);
    }

    public function reset()
    {
        $this->resource = [];
    }

    private function request($method, $payload = null)
    {
        return $this->responseHandler->handle(
            $this->prepareRequest()->{$method}($this->url(), $payload)
        );
    }

    private function prepareRequest()
    {
        $zttp = ZttpRequest::new()->asFormParams();

        if ($this->token) {
            $zttp->withHeaders(['Authorization' => $this->token]);
        }

        return $zttp;
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
