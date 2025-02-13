<?php
namespace Geral\Model\Services\Websocket;

require_once DIR_ROOT.DIR_LIBS.'/pusher/vendor/autoload.php';

use Pusher\Pusher;

/**
 * Classe responsável por realizar criar token de autenticação no websocket
 * @author Glauco David Laicht <glauco.laicht@udesc.br> 
 */
class Authentication{

    private $channel;
    private $socketId;
    private $customData;

    public function setChannel($channel){
        $this->channel = $channel;
    }

    public function setSocketId($socketId){
        $this->socketId = $socketId;
    }

    public function setCustomData($customData){
        $this->customData = $customData;
    }

    public function check(){
        return $this->getResponse();
    }

    protected function getResponse(){
        $pusher = new Pusher(APP_WEBSOCKET_KEY, APP_WEBSOCKET_SECRET, APP_WEBSOCKET_ID);
        return $pusher->socket_auth($this->channel, $this->socketId, $this->customData);
    }
}