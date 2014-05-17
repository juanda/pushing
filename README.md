Servidor Push
=============

Para hacer funcionar el sistema es necesario un servidor rabbitMQ con el 
plugin STOMP habilitado (rabbitmq-plugins enable rabbitmq_stomp)

- arrancar el broker: sudo service rabbitmq-server start

- arrancar el servidor push: php push_server.php

- arrancar el servidor web: cd web; php -S localhost 8000

- en varios navegadores cargar la página http://localhost:8000

- desde la aplicación puedes enviar mensajes al resto de clientes,
  también puedes lanzar un procedimiento remoto que calcula el área de
  un cuadrado.

- Por otro lado el script 'sendMessageToQueue.php' es un ejemplo de como enviar
  mensajes a los clientes desde aplicaciones externas a través de un servidor 
  rabbitMQ:
  # php sendMessageToQueue.php push.tutorial.messages "hola mundo" 
  este script envía un mensaje a la cola con un header
  de clave 'cat' y valor el primer argumento ('push.tutorial.messages')

Nota: También podemos enviar mensajes a la cola desde la aplicación
de monitorización de rabbitMQ. Para ello:

- en un navegador cargar la página de monitorización de rabbitMQ:
  http://localhost:15672, abrir la cola denominada 'la_cola' y 
  enviarle mensajes. Para que lleguen a los clientes dichos mensaje
  es imprescindible añadir un header denominado 'cat' con un valor
  'push.tutorial.messages'

Servidor Chat
=============

- arrancar el servidor push: php push_chat.php

- arrancar el servidor web: cd web; php -S localhost 8000

- abrir varios navegadores apuntando a la página http://localhost:8000/index_chat.html

- En uno de los navegadores escribir mensajes y comprobar que llegan a los demás