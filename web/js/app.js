ab.debug(true);
var conn = new ab.Session(
        'ws://localhost:8080'
        , function() { //Cuando se establece la conexión
            conn.subscribe('push.tutorial.messages', function(topic, evt) {
                addMessage(topic, evt);
            });
        }
, function() { //Cuando se cierra la conexión
    console.warn('WebSocket connection closed');
}
, {// Parámetros adicionales
    'skipSubprotocolCheck': true
}
);

function publish(message) {
    var myEvent = {body: message, details: ["something happened", "today"]};
    conn.publish('push.tutorial.messages', myEvent, true);
}

function addMessage(topic, data) {
    var messages = document.getElementById('messages');
    messages.innerHTML = messages.innerHTML + '<br/>' + topic + ': ' + data.body;
    console.log(topic + '" : ' + data.body);
}

conn.onopen = function(e) {
    console.log("Connection established!");
};
