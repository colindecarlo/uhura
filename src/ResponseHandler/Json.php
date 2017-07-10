<?php

namespace Uhura\ResponseHandler;

class Json
{
    public function handle($response)
    {
        return $response->json();
    }
}
