<?php

require __DIR__ . '/vendor/autoload.php';


$loop = React\EventLoop\Factory::create();


$pusher = new Jazzyweb\Pusher;

$factory = new React\Stomp\Factory($loop);
$client = $factory->createClient(array(
    'host' => 'localhost',
    'port' => '61613',
    'login' => 'guest',
    'passcode' => 'guest',
    'vhost' => '/',
        ));
$client->connect();

$client->subscribe('kuku', array($pusher, 'onBlogEntry'));

//$client->on('kaka', array($pusher, 'onBlogEntry'));

// Listen for the web server to make a ZeroMQ push after an ajax request
//$context = new React\ZMQ\Context($loop);
//$pull = $context->getSocket(ZMQ::SOCKET_PULL);
//$pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
//$pull->on('message', array($pusher, 'onBlogEntry'));
// Set up our WebSocket server for clients wanting real-time updates
$webSock = new React\Socket\Server($loop);
$webSock->listen(8080, '0.0.0.0'); // Binding to 0.0.0.0 means remotes can connect
$webServer = new Ratchet\Server\IoServer(
        new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
        new Ratchet\Wamp\WampServer(
        $pusher
        )
        )
        ), $webSock
);

$loop->run();
