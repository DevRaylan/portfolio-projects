var Realtime = function(){

    this.pusher    = null;
    this.channels  = null;
    this.host      = null;
    this.port      = null;
    this.authPoint = null;
    this.pusherKey = null;

    /**
     * Define o host para conexão
     */
    this.setHost = function(host){
        this.host = host;
    };

    /**
     * Define a porta para conexão
     */
    this.setPort = function(port){
        this.port = port;
    }

    /**
     * Define o endereço para realização da autenticação
     */
    this.setAuthEndPoint = function(authEndPoint){
        this.authEndPoint = authEndPoint;
    };

    /**
     * Define a chave de comunicação
     */
    this.setPusherKey = function(pusherKey){
        this.pusherKey = pusherKey;
    };

    this.init = function(){
        this.channels = {};
        this.loadWebsocketConfig();
        this.error(function(e){
            alert('Não foi possível conectar ao recurso de "RealTime" tente novamente em instantes.');
            console.error(e);
        });
    }

    /**
     * Define as configurações através do objeto repassado pelo backend
     */
    this.loadWebsocketConfig = function(){
        //Busca as configurações
        const config  = udev.getVar('websocketConfig');
        if(!config){
            return;
        }

        this.setConfig(config);
    };

    /**
     * Define as configurações padrões
     */
    this.setConfig = function(config){
        this.setPusherKey(config.pusherKey);
        this.setHost(config.host);
        this.setPort(config.port);
        this.setAuthEndPoint((config.authEndPoint) ? config.authEndPoint : udev.url.getUrlController() + '/websocketAuth');
    };

    /**
     * Inicia a conexão com o servidor
     */
    this.connect = function(){
        if(!this.pusherKey){
            alert('Não foi definida a chave para comunicação com o servidor');
        }

        this.pusher = new Pusher(this.pusherKey, {
            cluster          : 'mt1',
            wsHost           : this.host,
            wsPort           : this.port,
            wssPort          : this.port,
            scheme           : 'https',
            disableStats     : true,
            enabledTransports: ['ws', 'wss'],
            authEndpoint     : this.authEndPoint,
            useTLS           : true,
            encrypted        : true
        }); 
    };

    /**
     * Escuta um evento
     */
    this.listen = function(channelName, eventName, callback){
        return this.channel(channelName).listen(eventName, callback);
    };

    /**
     * Passa a escutar o canal e aciona a função com nome do evento repassado como parâmetro
     */
    this.listenGlobal = function(channelName, objectCallback){
        return this.channel(channelName).listenGlobal(objectCallback);
    };

    /**
     * Envia evento de cliente
     */
    this.sendClient = function(channelName, eventName, data){
        return this.channel(channelName).sendClientEvent(eventName, data);
    };

    /**
     * Cria um canal
     */
    this.channel = function(channelName){
        if(!this.channels[channelName]){
            this.channels[channelName] = new Channel(this.getPusher(), channelName);
        }
        return this.channels[channelName];
    };

    /**
     * Cria um canal privado
     */
    this.privateChannel = function(channelName){
        if(!channelName.startsWith('private-')){
            channelName = 'private-' + channelName;
        }

        if(!this.channels[channelName]){
            this.channels[channelName] = new PrivateChannel(this.getPusher(), channelName);
        }
        return this.channels[channelName];
    };

    /**
     * Cria um canal presence
     */
    this.presenceChannel = function(channelName){
        if(!channelName.startsWith('presence-')){
            channelName = 'presence-' + channelName;
        }

        if(!this.channels[channelName]){
            this.channels[channelName] = new PresenceChannel(this.getPusher(), channelName);
        }
        return this.channels[channelName];
    };

    /**
     * Deixar canal
     */
    this.leaveChannel = function(channelName){
        if (this.channels[channelName]) {
            this.channels[channelName].unsubscribe();
            delete this.channels[channelName];
        }
    };

     /**
      * Retorno a socket ID da conexão
      */
    this.socketId = function(){
        return this.getPusher().connection.socket_id;
    }

    /**
     * Desconecta
     */
    this.disconnect = function(){
        this.getPusher().disconnect();
    }

    /**
     * Ao ocorrer um erro na conexão
     */
    this.error = function(callback){
        this.getPusher().bind('pusher:error', function(data){
            callback(data);
        });
    };

    /**
     * Cria a conexão
     */
    this.getPusher = function(){
        if(!this.pusher){
            this.connect();
        }
        return this.pusher;
    }

    this.init();
};

var Channel = function(pusher, name, options){

    /**
     * Se inscreve no canal
     */
    this.subscribe = function(){
        var me            = this;
        this.subscription = this.pusher.subscribe(this.name);
        this.subscription.bind_global(function(eventName, data){
            me.onDefaultEvent(eventName, data);
        });
    };

    /**
     * Se desinscreve do canal
     */
    this.unsubscribe = function(){
        this.pusher.unsubscribe(this.name);
        this.subscription = null;
    };

    /**
     * Escuta/Registra um evento a ser disparado no canal
     */
    this.listen = function(eventName, callback){
        this.getSubscription().bind(eventName, callback);
    };

    /**
     * Escuta/Registra um evento a ser disparado no canal
     */
    this.listenForWhisper = function(eventName, callback){
        if(!eventName.startsWith('client-')){
            eventName = 'client-'+eventName;
        }
        this.listen(eventName, callback);
    };

    /**
     * Para de escutar um evento
     */
    this.stopListen = function(eventName){
        this.getSubscription().unbind(eventName);
    };

    /**
     * Para de escutar um evento
     */
    this.stopListerForWhisper = function(eventName){
        if(!eventName.startsWith('client-')){
            eventName = 'client-'+eventName;
        }
        this.stopListen(eventName);
    };

    /**
     * Registra função a ser disparada em caso de erro na inscrição
     */
    this.onSubscribeError = function(callback){
        this.registerDefaultEvent('pusher:subscription_error', callback);
    };

    /**
     * Registra função a ser disparada em caso de sucesso na inscrição
     */
    this.onSubscribed = function(callback){
        this.registerDefaultEvent('pusher:subscription_succeeded', callback);
    };

    /**
     * Registra um evento padrão
     */
    this.registerDefaultEvent = function(eventName, callback){
        this.events[eventName] = callback;
        if(this.dispEvents[eventName]){
            //Se o evento havia sido disparado antes de ser vinculada a função
            callback(this.dispEvents[eventName]);
            //Remove ele
            delete this.dispEvents[eventName];
        }
    };

    /**
     * Função disparada sempre ocorrer algum evento
     */
    this.onDefaultEvent = function(eventName, data){
        if(eventName.startsWith('pusher:')){
            //Se for um evento padrão
            if(this.events[eventName]){
                //Se o evento estiver registrado, dispara
                this.events[eventName](data);
            }
            else{
                //Se não tiver registrado, então armazena
                this.dispEvents[eventName] = data;
            }
        }
    };

    /**
     * Passa a escutar o canal e aciona a função com nome do evento repassado como parâmetro
     */
    this.listenGlobal = function(objectCallback){
        var fnBindGlobal = function(eventName, data){
            eventName = eventName.replace('client-', '');
            if(objectCallback[eventName] && typeof objectCallback[eventName] === "function"){
                /** 
                 * No caso de ser um objeto, onde existe uma propriedade com o nome exato do evento: 
                 * var object = {
                 *     'nome-evento' : function(e) {
                 *      } 
                 * }
                 */
                objectCallback[eventName](data);
            }
            else{
                /**
                 * No caso de ser um objeto, onde existe uma função com o nome camelCase do evento
                 * Exemplo para o evento nome-evento
                 * var Objeto = function(){
                 *     this.nomeEvento = function(e){
                 *     }
                 * }
                 */
                var newEventName = eventName.replace(/-([a-z])/g, function (g) { return g[1].toUpperCase(); });
                if(objectCallback[newEventName] && typeof objectCallback[newEventName] === "function"){
                    objectCallback[newEventName](data);
                }
            }
        };
        this.getSubscription().bind_global(fnBindGlobal);
    };

    /**
     * Envia um evento direto do cliente
     */
    this.sendClientEvent = function(eventName, data){
        var me = this;
        if(!eventName.startsWith('client-')){
            eventName = 'client-'+eventName;
        }

        if(!this.subscription){
            //Se não se inscreveu
            this.subscribe();
            this.subscription.bind('pusher:subscription_succeeded', function(e){
                me.pusher.send_event(eventName, data, me.name);
            });
        }
        else{
            //Se já se inscreveu
            this.pusher.send_event(eventName, data, this.name);
        }
    };

    this.getSubscription = function(){
        if(!this.subscription){
            this.subscribe();
        }
        return this.subscription;
    };

    this.subscription = null;
    this.events       = {};
    this.dispEvents   = {};

    if(name){
        this.name    = name;
        this.pusher  = pusher;
        this.options = options | {};
    }
};

var PrivateChannel = function(pusher, name, options){

    //Cria comportamentos especificos para canais privados, se necessário
    Channel.call(this, pusher, name, options);

};
PrivateChannel.prototype = new Channel;

var PresenceChannel = function(pusher, name, options){

    //Cria comportamentos especificos para canais presence, se necessário
    Channel.call(this, pusher, name, options);

    /**
     * Registra função a ser disparada em caso de sucesso na inscrição
     */
    this.here = function(callback){
        this.registerDefaultEvent('pusher:subscription_succeeded', function(data){
            callback(Object.keys(data.members).map((k) => data.members[k]));
        });
    };

    /**
     * Registra função quando usuário está sendo conectado
     */
    this.joining = function(callback){
        this.registerDefaultEvent('pusher:member_added', function(member){
            callback(member.info);
        });
    };

    /**
     * Listen for someone leaving the channel.
     */
    this.leaving = function(callback){
        this.registerDefaultEvent('pusher:member_removed', (member) => {
            callback(member.info);
        });
    };

    /**
     *  Trigger client event on the channel.
     */
    this.whisper = function(eventName, data){
        this.sendClientEvent(eventName, data);
    };

    /**
     * Retorna o usuário
     */
    this.getMeUser = function(){
        if(this.subscription){
            return this.subscription.members.me.info;
        }
        return null;
    }
};
PresenceChannel.prototype = new Channel;