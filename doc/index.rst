WAMP (Web Application Message Protocol)
=======================================

Es un protocolo que permite el arquitecturas de *aplicaciones distribuidas*
con funcionalidades implementadas sobre *diversos nodos* y *desacopladas*
mediante el paso de *mensajes* que son enrutados por *WAMP's routers*.

Realms, Session, Transport
==========================

Realm. Dominio donde se encuentran los routers WAMP's y desde el que se pueden 
administrar. Pueden estar protegido por mecanismos de autentificación.

Session. Conversación entre dos *Peers* (partes) que están vinculados con un Realm

Transport. Mecanismo donde se ejecuta la sesión. Ofrece un canal sobre el que
los *Peers* intercambian mensajes durante una sesión. Un transporte debe ser:
 
 - basado en mensajes
 - bidireccional
 - fiable
 - ordenado??

 WebSocket es el mecanismo de transporte por defecto del protocolo WAMP.

 Peers y Roles
 =============

 Una sesión WAMP conecta a dos Peers, el cliente y el router. El cliente puede
 tener alguno de los siguientes roles:

 - Caller
 - Callee
 - Subscriber
 - Publisher

Y el router puede implementar alguno de los siguientes roles:

- Dealer
- Broker

La comunicación solo se puede llevar a cabo entre cliente y router, nunca entre
clientes.

Una sesión WAMP es absolutamente simétrica de cara a los componentes de la 
aplicación. Esto significa que cualquier componente puede actuar como Caller, 
Callee, Subscriber y Publisher al mismo tiempo. Por otro lado los routers 
representan la fabrica sobre la que WAMPS se ejecuta como un servicio de 
mensajería simétrico entre aplicaciones.

El código de las aplicaciones
=============================

Los clientes son los encargados de ejecutar el código de las aplicacione, 
mientras que los routers únicamente enrutan mensajes y no ejecutan código
de la aplicación.

Elementos constructivos (building blocks)
=========================================

WAMP se define respecto a los siguientes elementos:

- Identificadores
- Serialización
- Transporte

Identificadores
--------------- 

URI's
~~~~~
WAMP debe identificar los siguientes recursos:

- Tópicos (temas)
- Procedimientos
- Errores

Para lo que se utiliza el conocido esquema URI (Uniform Resorce Indetifiers)

Ejemplos:

- com.myapp.mytopic1
- com.myapp.myprocedure1
- com.myapp.myerror1

ID's
~~~~
WAMP deb identificar los siguientes entidades efímeras (relativas a un scope)

- Sessiones (global scope)
- Publicationes (global scope)
- Subscripciones (router scope)
- Registros (router scope)
- Peticiones (Requests) (session scope)

Serializaciones
---------------

Los mensajes se deben poder serializar en secuencias de octetos, de manera que
se puedan utilizar los siguientes tipos:

- integer (non-negative)
- string (UTF-8 encoded Unicode)
- bool
- list
- dict (with string keys)

WAMP define dos formas de serialización: JSON y MsgPack

Mecanismos de Transporte
------------------------

Deben ser:

- basados en mensajes
- fiables (reliable)
- ordenados?? (ordered)
- bidireccional (full duplex)

El mecanismo de transporte por defecto es WebSocket

WebSocket
~~~~~~~~~

Cada mensaje WAMP es transmitido en un único mensaje WebSocket (no frames)

https://github.com/tavendo/WAMP/blob/master/spec/figure/sessions4.png

Mensajes
========

Todos los mensajes WAMP tienen la misma estructura: una lista en la que el 
primer elemento es el tipo de los mensajes que componen el resto de la lista:

``[MessageType|integer, ... one or more message type specific elements ...]``

Definiciones de los mensajes
----------------------------

HELLO
~~~~~

Lo envía un cliente para conectar con un router

``[HELLO, Realm|uri, Details|dict]``

WELCOME
~~~~~~~

Lo envía un router cuando acepta al cliente. La sesión WAMP se abre entonces.

``[WELCOME, Session|id, Details|dict]``

ABORT
~~~~~

Enviado por cualquier peer para abortar una sesión. No se espera ninguna 
respuesta

``[GOODBYE, Details|dict, Reason|uri]``

ERROR
~~~~~

Enviado por cualquier peer cuando este debe notificar un error.

``[ERROR, REQUEST.Type|int, REQUEST.Request|id, Details|dict, Error|uri]``
``[ERROR, REQUEST.Type|int, REQUEST.Request|id, Details|dict, Error|uri, Arguments|list]``
``[ERROR, REQUEST.Type|int, REQUEST.Request|id, Details|dict, Error|uri, Arguments|list, ArgumentsKw|dict]``

PUBLISH
~~~~~~~

Sent by a Publisher to a Broker to publish an event.

[PUBLISH, Request|id, Options|dict, Topic|uri]
[PUBLISH, Request|id, Options|dict, Topic|uri, Arguments|list]
[PUBLISH, Request|id, Options|dict, Topic|uri, Arguments|list, ArgumentsKw|dict]

PUBLISHED
~~~~~~~~~

Acknowledge sent by a Broker to a Publisher for acknowledged publications.

[PUBLISHED, PUBLISH.Request|id, Publication|id]

SUBSCRIBE
~~~~~~~~~

Subscribe request sent by a Subscriber to a Broker to subscribe to a topic.

[SUBSCRIBE, Request|id, Options|dict, Topic|uri]

SUBSCRIBED
~~~~~~~~~~

Acknowledge sent by a Broker to a Subscriber to acknowledge a subscription.

[SUBSCRIBED, SUBSCRIBE.Request|id, Subscription|id]

UNSUBSCRIBE
~~~~~~~~~~~

Unsubscribe request sent by a Subscriber to a Broker to unsubscribe a subscription.

[UNSUBSCRIBE, Request|id, SUBSCRIBED.Subscription|id]

UNSUBSCRIBED
~~~~~~~~~~~~

Acknowledge sent by a Broker to a Subscriber to acknowledge unsubscription.

[UNSUBSCRIBED, UNSUBSCRIBE.Request|id]

EVENT
~~~~~

Event dispatched by Broker to Subscribers for subscription the event was matching.

[EVENT, SUBSCRIBED.Subscription|id, PUBLISHED.Publication|id, Details|dict]
[EVENT, SUBSCRIBED.Subscription|id, PUBLISHED.Publication|id, Details|dict, PUBLISH.Arguments|list]
[EVENT, SUBSCRIBED.Subscription|id, PUBLISHED.Publication|id, Details|dict, PUBLISH.Arguments|list, PUBLISH.ArgumentsKw|dict]

CALL
~~~~

Call as originally issued by the Caller to the Dealer.

[CALL, Request|id, Options|dict, Procedure|uri]
[CALL, Request|id, Options|dict, Procedure|uri, Arguments|list]
[CALL, Request|id, Options|dict, Procedure|uri, Arguments|list, ArgumentsKw|dict]

RESULT
~~~~~~

Result of a call as returned by Dealer to Caller.

[RESULT, CALL.Request|id, Details|dict]
[RESULT, CALL.Request|id, Details|dict, YIELD.Arguments|list]
[RESULT, CALL.Request|id, Details|dict, YIELD.Arguments|list, YIELD.ArgumentsKw|dict]

REGISTER
~~~~~~~~

A Callees request to register an endpoint at a Dealer.

[REGISTER, Request|id, Options|dict, Procedure|uri]

REGISTERED
~~~~~~~~~~

Acknowledge sent by a Dealer to a Callee for successful registration.

[REGISTERED, REGISTER.Request|id, Registration|id]

UNREGISTER
~~~~~~~~~~

A Callees request to unregister a previsouly established registration.

[UNREGISTER, Request|id, REGISTERED.Registration|id]

UNREGISTERED
~~~~~~~~~~~~

Acknowledge sent by a Dealer to a Callee for successful unregistration.

[UNREGISTERED, UNREGISTER.Request|id]

INVOCATION
~~~~~~~~~~

Actual invocation of an endpoint sent by Dealer to a Callee.

[INVOCATION, Request|id, REGISTERED.Registration|id, Details|dict]
[INVOCATION, Request|id, REGISTERED.Registration|id, Details|dict, CALL.Arguments|list]
[INVOCATION, Request|id, REGISTERED.Registration|id, Details|dict, CALL.Arguments|list, CALL.ArgumentsKw|dict]

YIELD
~~~~~

Actual yield from an endpoint send by a Callee to Dealer.

[YIELD, INVOCATION.Request|id, Options|dict]
[YIELD, INVOCATION.Request|id, Options|dict, Arguments|list]
[YIELD, INVOCATION.Request|id, Options|dict, Arguments|list, ArgumentsKw|dict]

Sesiones
========

El flujo de mensajes entre clientes y routers para abrir y cerrar una sesión
WAMP implica los siguientes mensajes:

- HELLO
- WELCOME
- ABORT
- GOODBYE

Establecimiento de sesión
-------------------------

Cierre de sesión
----------------

Publicación/Subscripción
========================

Subscripción/DeSubscripción
---------------------------

Publicación/Evento
------------------

RPC(Remote Procedure Call)
==========================

Registrar/DeRegistrar
---------------------

Llamadas/Invocaciones
---------------------


