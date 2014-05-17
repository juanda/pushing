<?php

namespace Jazzyweb;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface {

    /**
     * Este array guardará todos los tópicos en los que se subscriben los
     * clientes del websocket
     */
    protected $subscribedTopics = array();

    public function onSubscribe(ConnectionInterface $conn, $topic) {

        echo "Requested subscription: " . $topic->getId() . PHP_EOL;

        // Almacenamos en un array los topics a los que los clientes se
        // van subscribiendo. Hacemos esto de cara al método onMessageArrive
        // que es el encargado de procesar los mensajes Stomp que vienen desde
        // rabbitMQ, y necesita este array para poder asociar mensajes stomp 
        // a topicos registrados
        if (!array_key_exists($topic->getId(), $this->subscribedTopics)) {
            $this->subscribedTopics[$topic->getId()] = $topic;
        }
    }

    // Esta función se ejectuta cuando el websocket server recibe un mensaje
    // desde el broker.
    // Comprueba si la categoría del mensaje está en la lista de temas y si
    // es así envía dicho mensaje a todos los clientes conectados (broadcast)
    public function onMessageArrive(\React\Stomp\Protocol\Frame $entry) {

        $category = $entry->getHeader('cat');
        echo "Message arrived, category: " . $category . PHP_EOL;
        // If the lookup topic object isn't set there is no one to publish to
        if (!array_key_exists($category, $this->subscribedTopics)) {
            return;
        }

        $topic = $this->subscribedTopics[$category];

        // re-send the data to all the clients subscribed to that category
        $topic->broadcast($entry);

        echo "Messages sent to all subscribers: " . $entry->body . PHP_EOL;
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }

    public function onClose(ConnectionInterface $conn) {
        
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        
    }

    public function onOpen(ConnectionInterface $conn) {
        
    }

    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        $category = $topic->getId();

        echo "Message arrived, category: " . $category . PHP_EOL;
       
        $topic->broadcast($event);

        echo "Messages sent to all subscribers: " . $event['body'] . PHP_EOL;
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
        
    }

    /* The rest of our methods were as they were, omitted from docs to save space */
}
