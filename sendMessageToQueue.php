<?php

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

require __DIR__ . '/vendor/autoload.php';

if ($argc != 3) {
    echo "Uso: " . $argv[0] . " categoria mensaje" . PHP_EOL;
    exit;
}

$cat = $argv[1];
$msgBody = $argv[2];



$amqpAddress = 'localhost';
$amqpPort = 5672;
$amqpUser = 'guest';
$amqpPassword = 'guest';
$jobQueueName = 'la_cola';

$connection = new AMQPConnection($amqpAddress, $amqpPort, $amqpUser, $amqpPassword);
$channel = $connection->channel();

$channel->queue_declare($jobQueueName, false, true, false, false);

$msg = new AMQPMessage($msgBody, array(
    'application_headers' => array('cat' => array("S", $cat)),
    'delivery_mode' => 2
        ));

$channel->basic_publish($msg, '', $jobQueueName);

echo "Se ha enviado el mensaje a la cola" . PHP_EOL;
