<?php

require __DIR__ . '/../vendor/autoload.php';

$pid = exec('php -S ' . 'localhost:' . getenv('TEST_SERVER_PORT') . ' -t ./server/public > /dev/null 2>&1 & echo $!');
while (@file_get_contents('http://localhost:' . getenv('TEST_SERVER_PORT') . '/up-yet') === false) {
    usleep(1000);
}
register_shutdown_function(function () use ($pid) {
    exec('kill ' . $pid);
});
