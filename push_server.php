<?php

require __DIR__ . '/vendor/autoload.php';

// Este objeto implementa una interfaz WAMP (http://wamp.ws). Servirá 
// tanto para responder a eventos del websocket como para procesar los 
// mensajes que lleguen desde el broker (rabbitMQ)
$pusher = new Jazzyweb\Pusher;

// Creamos un consumidor de mensajes de una cola RabbitMQ. En esta cola
// el servidor web o cualquier otra aplicación del sistema colocará los
// mensajes que se quieran enviar a los browser que estén conectados al
// websocket
$loop = React\EventLoop\Factory::create();
$factory = new React\Stomp\Factory($loop);
$client = $factory->createClient(array(
    'host' => 'localhost',
    'port' => '61613',
    'login' => 'guest',
    'passcode' => 'guest',
    'vhost' => '/',
        ));
$client->connect();

// Subscribimos el cliente como consumidor de la cola llamada 'la_cola'. 
// Cuando llegue un mensaje desde esta cola se ejecutará el método
// onMessageArrive del objeto pusher
$client->subscribe('la_cola', array($pusher, 'onMessageArrive'));

/**
 * Si queremos usar un broker ZeroMQ
 * 
 * $context = new React\ZMQ\Context($loop);
 * $pull = $context->getSocket(ZMQ::SOCKET_PULL);
 * $pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
 * $pull->on('message', array($pusher, 'onMessageArrive'));
 */

// Creación del websocket server.
// Hacemos que entienda el protocolo Wamp
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
