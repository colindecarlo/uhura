<?php

require_once __DIR__.'/../../../vendor/autoload.php';

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

function build_response($request)
{
    return response()->json([
        'headers' => $request->header(),
        'method' => $request->method(),
        'path' => $request->path(),
        'query' => $request->query(),
        'json' => $request->json()->all(),
        'form_params' => $request->request->all(),
    ]);
}

$app->get('{anything:.*}', function () {
    return build_response(app('request'));
});

$app->post('{anything:.*}', function () {
    return build_response(app('request'));
});

$app->put('{anything:.*}', function () {
    return build_response(app('request'));
});

$app->patch('{anything:.*}', function () {
    return build_response(app('request'));
});

$app->delete('{anything:.*}', function () {
    return build_response(app('request'));
});

$app->run();
