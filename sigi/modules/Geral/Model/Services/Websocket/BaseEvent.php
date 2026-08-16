<?php

namespace Geral\Model\Services\Websocket;

use Geral\Model\UserSession;

/**
 * Classe abstrata para eventos a serem criados na aplicação
 * 
 *   É necessário no construtor definir o nome do evento, por exemplo:
 * 
 *   public function __construct(){
 *       ...
 *       $this->setEventName('nomeEvento');
 *       ...
 *   }
 * 
 *   Também é necessário especificar o nome do canal e seu tipo através dos métodos private / presence. Exemplo:
 * 
 *   public function __construct(){
 *       ...
 *       $this->setPresenceChannel('reuniao-123');
 *       ...
 *   }
 * 
 *   Obs.: Presence e private são semelhantes, ambos são privados (permitem somente membros autorizados), porém, 
 *    "presence" adiciona funcionalidades em comunicações semelhantes a chats (tem eventos disparados quando um usuário entra, sai)
 * 
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 */
abstract class BaseEvent implements EventInterface{

    //Envia para todos os usuários conectados
    const SEND_ALL = 1;
    //Envia para todos os usuários conectados, exceto quem "dispara" o evento
    const SEND_TO_OTHERS = 2;

    private $channel;
    private $event;
    private $sendType = self::SEND_ALL;

    public function __construct(){
        $this->sendToAll();
    }

    public function setChannel($name){
        $this->channel = $name;
    }

    public function setPresenceChannel($name){
        $this->setChannel('presence-'.$name);
    }

    public function setPrivateChannel($name){
        $this->setChannel('private-'.$name);
    }

    public function getChannelName(){
        return $this->channel;
    }

    public function setEventName($eventName){
        $this->event = $eventName;
    }

    public function getEventName(){
        return $this->event;
    }

    public function sendToAll(){
        $this->sendType = self::SEND_ALL;
    }

    public function sendToOthers(){
        $this->sendType = self::SEND_TO_OTHERS;
    }

    /**
     * Retorna informações básicas do usuário logado
     * @return array
     */
    protected function getUserData(){
        return [
            'id'   => UserSession::getParam('id'),
            'cpf'  => UserSession::getParam('cpf'),
            'nome' => UserSession::getParam('nome')
        ];
    }

    /**
     * Dispara o evento
     * @return mixed
     */
    public function send(){
        $broadcast = new Broadcaster();
        $broadcast->addChannel($this->getChannelName());
        $broadcast->setEvent($this->getEventName());
        $broadcast->setData($this->getData());
        if($this->sendType == self::SEND_TO_OTHERS){
            if(UserSession::paramExists('socket_id')){
                $broadcast->setSocketIdExcept(UserSession::getParam('socket_id'));
            }
        }
        return $broadcast->sendMessage();
    }
}