ab.debug(true);

var wsuri = 'ws://localhost:8080';

var session = null;

ab.connect(
        // The WebSocket URI of the WAMP server
        wsuri,
        // The onconnect handler
                function(newSession) {
                    console.log("Connected to " + wsuri);

                    session = newSession;

                    // subscribe to a topic
                    session.subscribe('push.tutorial.messages', function(topic, evt) {                        
                        addMessage(topic, evt);
                    });

                },
                // The onhangup handler
                        function(code, reason, detail) {
                            // WAMP session closed here ..
                        },
                        // The session options
                                {
                                    'maxRetries': 60,
                                    'retryDelay': 2000
                                }
                        );

function addMessage(topic, data) {
    var messages = document.getElementById('messages');
    messages.innerHTML = messages.innerHTML + '<br/>' + topic + ': ' + data.body;
    console.log(topic + '" : ' + data.body);
}

function publish(message) {
    var myEvent = {body: message, details: ["something happened", "today"]};
    
    session.publish('push.tutorial.messages', myEvent, true);
}


