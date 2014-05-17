Objetivo del proyecto
=====================

¿Cómo hacer en una aplicación web que el servidor envíe datos al cliente
sin que este último los haya solicitado mediante una petición HTTP? 

Responder a esta pregunta es lo que me propongo con este proyecto. Qué 
architecturas, patrones, protocolos y otros bichos raros podemos combinar para
dar otro giro de tuerca en el desarrollo de aplicaciones web y hacerlas aún más 
dinámicas permitiendo una comunicación full duplex en el tradicional esquema
cliente-servidor.


Servidor Chat
=============

Instrucciones
-------------

- arrancar el servidor push: php push_chat.php

- arrancar el servidor web: cd web; php -S localhost 8000

- abrir varios navegadores apuntando a la página http://localhost:8000/index_chat.html

- En uno de los navegadores escribir mensajes y comprobar que llegan a los demás

Servidor Push
=============

Instrucciones
-------------

- arrancar el servidor push: php push_server.php

- arrancar el servidor web: cd web; php -S localhost 8000

- en varios navegadores cargar la página http://localhost:8000

- desde la aplicación puedes enviar mensajes al resto de clientes,
  también puedes lanzar un procedimiento remoto que calcula el área de
  un cuadrado.

Si quieres comprobar como funciona el envío de mensajes desde aplicaciones
externas debes instalar un servidor rabbitMQ con los plugins  management y stomp
habilitados (rabbitmq-plugins enable rabbitmq_stomp rabbitmq_management)

- arrancar el broker: sudo service rabbitmq-server start

- El script 'sendMessageToQueue.php' es un ejemplo de como enviar
  mensajes a los clientes desde aplicaciones externas a través de un servidor 
  rabbitMQ:
  ``php sendMessageToQueue.php push.tutorial.messages "hola mundo"`` 
  este script envía un mensaje a la cola con un header
  de clave 'cat' y valor el primer argumento ('push.tutorial.messages')

Nota: También podemos enviar mensajes a la cola desde la aplicación
de monitorización de rabbitMQ. Para ello:

- en un navegador cargar la página de monitorización de rabbitMQ:
  http://localhost:15672, abrir la cola denominada 'la_cola' y 
  enviarle mensajes. Para que lleguen a los clientes dichos mensaje
  es imprescindible añadir un header denominado 'cat' con un valor
  'push.tutorial.messages'

Software utilizado
==================

- Ratchet para la implementación del servidor (broker/dealer) WAMP sobre webSocket 
  (http://socketo.me)

- Autobahn para la implementación de los clientes WAMP (subscriber/publisher, caller)
  (http://autobahn.ws/js)

- RabbitMQ como middleware de mensajería para la comunicación de aplicaciones
  externas con el servidor WAMP. (https://www.rabbitmq.com)