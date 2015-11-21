<?php

namespace Uhura\ResponseHandler;

class Json
{
    public function handle($response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}
