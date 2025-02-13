<?php

namespace Geral\Model\Services\Websocket;

require_once DIR_ROOT.DIR_LIBS.'/pusher/vendor/autoload.php';

use Pusher\Pusher;

/**
 * Classe responsável por disparar uma notificação para o servidor websocket via backend (Servidor de aplicação -> Websocket)
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 */
class Broadcaster{

    protected $channels = [];
    protected $event;
    protected $data;
    protected $socketIdExcept;

    /**
     * Adiciona o canal que se deseja notificar
     * @param string $channel - Nome do canal
     */
    public function addChannel($channel){
        $this->channels[] = $channel;
    }

    /**
     * Atribui todos os canais que deseja-se notificar
     * @param array $channels - Canais
     */
    public function setChannels(array $channels){
        $this->channels = $channels;
    }

    /**
     * Define o evento que será disparado
     * @param string $event - Nome do evento
     */
    public function setEvent($event){
        $this->event = $event;
    }

    /**
     * Define dados/parâmetros a serem enviados juntamente com o evento
     * @param mixed $data - Dados
     */
    public function setData($data){
        $this->data = $data;
    }

     /**
     * Define um id que não deve receber o retorno do websocket
     * @param [string] $socketId
     * @return void
     */
    public function setSocketIdExcept($socketIdExcept){
        $this->socketIdExcept = $socketIdExcept;
    }

    /**
     * Realiza o envio da mensagem para o servidor websocket
     * @return mixed
     */
    public function sendMessage(){
        $pusher = new Pusher(APP_WEBSOCKET_KEY, APP_WEBSOCKET_SECRET, APP_WEBSOCKET_ID, [
            'host'      => WEBSOCKET_HOST,
            'port'      => WEBSOCKET_PORT,
            'encrypted' => false,
            'scheme'    => 'https'
        ]);
        $param = [];
        if($this->socketIdExcept){
            $param['socket_id'] = $this->socketIdExcept;
        }
        return $pusher->trigger($this->channels, $this->event, $this->data, $param, true);
    }
}