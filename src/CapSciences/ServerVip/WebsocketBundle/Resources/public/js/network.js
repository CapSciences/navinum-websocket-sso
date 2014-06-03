(function ($) {

    var methods = {};
    var io = {};
    var bindings = {};
    var eventHandlers = {connected:[], disconnected:[]};
    var params = {
        url:'',
        connected:false,
        debug:true,
        retryDelay:10000,
        socket: undefined
    };

    // Private methods
    methods.init = function () {
        window.IO = io;

        $(document).trigger('io.ready');
    };

    methods.log = function (msg) {
        if (params.debug) {
            console.log(msg);
        }
    };

    methods.trigger = function (eventType) {
        if (eventHandlers[eventType] == undefined) {
            methods.log('unknown event to trigger : ' + eventType);
            return;
        }

        for (var handler in eventHandlers[eventType]) {
            eventHandlers[eventType][handler]();
        }
    };

    methods.onOpen = function (e) {
        params.connected = true;

        methods.log('socket connected');
        $('#websocket_state').text('WS:Connected');

        methods.trigger('connected');

    };

    methods.onClose = function (e) {
        var wasConnected = params.connected;
        params.connected = false;

        methods.log('socket closed;');
        $('#websocket_state').text('WS:NotConnected');

        window.setTimeout(io.open, params.retryDelay);

        if(wasConnected) {
            methods.trigger('disconnected');
        }
    };

    methods.onMessage = function (e) {
        var msg = JSON.parse(e.data);
        if (bindings[msg.command] != undefined) {
            bindings[msg.command](msg.data);
        }
        else {
            methods.log('Unbinded message ' + msg.command);
        }
    };

    // Public methods
    io.isConnected = function () {
        return params.connected;
    };

    io.onConnected = function (callback) {
        eventHandlers.connected.push(callback);
    };

    io.onDisconnected = function (callback) {
        eventHandlers.disconnected.push(callback);
    }

    io.open = function (options) {
        params = $.extend(params, options);

        var noWsSupport = false;

        methods.log('Tying to connect to ' + params.url);
        if (window.MozWebSocket) {
            params.socket = new MozWebSocket(params.url);
        } else if (window.WebSocket) {
            params.socket = new WebSocket(params.url);
        } else {
            noWsSupport = true;
        }
        if (noWsSupport === true) {
            methods.log('Browser dont support websocket');
            return;
        }

        params.socket.onopen = function (event) {
            methods.onOpen(event)
        };
        params.socket.onmessage = function (event) {
            methods.onMessage(event)
        };
        params.socket.onclose = function (event) {
            methods.onClose(event)
        };
    };

    io.send = function (command, data) {
        var msg = {
            command:command,
            data:data
        };
        methods.log(msg);
        var str = JSON.stringify(msg);
        methods.log(str)
        params.socket.send(str);
    };

    io.bind = function (command, callback) {
        methods.log('add binding on message : ' + command);

        bindings[command] = callback;
    };

    // Init
    methods.init();

})(jQuery);