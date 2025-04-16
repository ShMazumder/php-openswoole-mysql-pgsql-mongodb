<?php
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

$server = new Server("0.0.0.0", 9501);

$server->on("start", function (Server $server) {
    echo "Swoole HTTP server is started at http://localhost:9501\n";
});

$server->on("request", function (Request $request, Response $response) {
    $response->header("Content-Type", "text/plain");
    $response->end("Hello from OpenSwoole!");
});

$server->start();
