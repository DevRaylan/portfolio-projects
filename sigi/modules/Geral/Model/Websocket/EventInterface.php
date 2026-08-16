<?php

namespace Geral\Model\Websocket;

/**
 * Interface que define os contratos para um evento a ser disparado para o websocket
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 */
interface EventInterface{

    /**
     * Retorna o nome do evento
     * @return string
     */
    public function getEventName();

    /**
     * Retorna o nome do canal
     * @return string
     */
    public function getChannelName();

    /**
     * Retorna o array de dados necessário para enviar ao Websocket
     * @return array
     */
    public function getData();

}