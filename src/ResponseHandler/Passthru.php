<?php

namespace Uhura\ResponseHandler;

class Passthru
{
    public function handle($response)
    {
        return $response;
    }
}
