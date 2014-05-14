Servidor Push
=============

Para hacer funcionar el sistema es necesario un servidor rabbitMQ.

- arrancar el broker: sudo service rabbitmq-server start

- arrancar el servidor push: php push_server.php

- arrancar el servidor web: cd web; php -S localhost 8000

- en un navegador cargar la página http://localhost:8000

- en un navegador cargar la página de monitorización de rabbitMQ:
  http://localhost:15672, abrir la cola denominada 'la_cola' y 
  enviarle mensajes. Para que lleguen a los clientes dichos mensaje
  es imprescindible añadir un header denominado 'cat' con un valor
  'cat_messages'

Servidor Chat
=============

- arrancar el servidor push: php push_chat.php

- arrancar el servidor web: cd web; php -S localhost 8000

- abrir varios navegadores apuntando a la página http://localhost:8000/index_chat.html

- En uno de los navegadores escribir mensajes y comprobar que llegan a los demás