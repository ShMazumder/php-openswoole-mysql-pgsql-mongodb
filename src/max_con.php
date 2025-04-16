<?php 


$server = new Swoole\Server("0.0.0.0", 9501);

$server->set([
    'worker_num' => 4,
    'max_connection' => 10000, // Max concurrent connections
    'daemonize' => false,
]);

$server->on("connect", function ($server, $fd) {
    echo "Client: Connect.\n";
});

$server->on("receive", function ($server, $fd, $reactor_id, $data) {
    $server->send($fd, "Server: $data");
});

$server->on("close", function ($server, $fd) {
    echo "Client: Close.\n";
});

$server->start();

?>