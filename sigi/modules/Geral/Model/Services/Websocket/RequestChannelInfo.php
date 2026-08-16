<?php

namespace Geral\Model\Services\Websocket;

require_once DIR_ROOT.DIR_LIBS.'/pusher/vendor/autoload.php';

use Exception;
use Pusher\Pusher;

/**
 * Classe responsável por disparar uma notificação para o servidor websocket para trazer informações dos canais
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 */
class RequestChannelInfo{


    /**
     * Retorna os canais abertos de uma conexão
     * @return void
     */
    public function getChannels(){
        $pusher = $this->newPusher();
        return $pusher->getChannels();
    }

    /**
     * Retorna informações de um canal
     * @param string $channelName
     * @return void
     */
    public function getChannelInfo($channelName){
        $response = null;
        $pusher   = $this->newPusher();
        try{
            $response = $pusher->getChannelInfo($channelName);
        }
        catch(Exception $e){

        }
        return $response;
    }

    /**
     * Retorna os usuários conectados ao WebSocket
     * @param string $channelName
     * @return void
     */
    public function getPresenceUser($channelName){
        $response = null;
        $pusher   = $this->newPusher();
        try{
            $response = $pusher->getPresenceUsers($channelName);
        }
        catch(Exception $e){

        }
        return $response;
    }

    /**
     * Realiza a criação do Pusher
     * @return \Pusher\Pusher
     */
    protected function newPusher(){
        $pusher = new Pusher(APP_WEBSOCKET_KEY, APP_WEBSOCKET_SECRET, APP_WEBSOCKET_ID, [
            'host'      => WEBSOCKET_HOST,
            'port'      => WEBSOCKET_PORT,
            'encrypted' => false,
            'scheme'    => 'https'
        ]);
        return $pusher;
    }
}