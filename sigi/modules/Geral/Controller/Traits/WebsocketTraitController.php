<?php

namespace Geral\Controller\Traits;

use Geral\Model\SystemMessages;
use Geral\Model\UserSession;
use Geral\Model\Websocket\Authentication;

/**
 * Trait para adicionar as funcionalidades padrões de comunicação com Websocket
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 */
trait WebsocketTraitController{

    /**
     * Método que deve verificar se o usuário tem acesso ao canal
     * @param string $channelName - Nome do Canal
     * @return boolean
     */
    protected abstract function checkChannelPermission($channelName);

    /**
     * Método acionado para fazer verificações de autenticação 
     * (Será acionado pelo frontend ao realizar a subscrição em um canal)
     */
    public function websocketAuth(){
        $channelName = $this->getChannelPureName();
        $bPermission = $this->checkChannelPermission($channelName);
        if(!$bPermission){
            //Caso não tenha permissão para acessar
            //Teve que ser definido success pois a requisição é feita pela API JS e sempre é um XHR. 
            //Por padrão o framework redireciona quando ocorre erro em XHT, então tive que simular um "sucesso" com error code 401 (unauthorized) para conseguir disparar um exception

            //Se quiser fazer uma mensagem especifica. Utilizar o mesmo método, só que colocar o httpCode como 400 e tratar no frontend na função "onSubscribeError" se data == 400 disparar a mensagem desejada
            SystemMessages::setResultStatus(SystemMessages::RESULT_SUCCESS_STATUS);
            SystemMessages::setMsg('O usuário não tem permissão para acessar o canal selecionado.');
            SystemMessages::setHttpCode(401);
            $this->sendResponse();
        }
        //Caso tenha permissão, repassa os dados necessários para conectar
        echo $this->checkAuthWebsocket();
    }

    /**
     * Adiciona as configurações para a tela
     */
    protected function addConfigView(){
        $this->addVarView('websocketConfig', [
            'pusherKey' => APP_WEBSOCKET_KEY,
            'host'      => WEBSOCKET_HOST,
            'port'      => WEBSOCKET_PORT
        ]);
    }
   
    /**
     * Procede com a autenticação para logar no canal
     */
    protected function checkAuthWebsocket($extraData=[]){
        //Busca as informações repassadas
        $channel      = $this->request->input('channel_name');
        $socketId     = $this->request->input('socket_id');
        $customData   = array_merge([
            'user_id'   => UserSession::getParam('id'),
            'user_info' => [
                'cpf'   => UserSession::getParam('cpf'),
                'nome'  => UserSession::getParam('nome')
            ]
        ], $extraData);

        $webSocksAuth = new Authentication();
        $webSocksAuth->setChannel($channel);
        $webSocksAuth->setSocketId($socketId);
        $webSocksAuth->setCustomData(json_encode($customData));
        return $webSocksAuth->check();
    }

    /**
     * Retorna o nome do channel, sem os prefixos padrões
     * @return string
     */
    private function getChannelPureName(){
        return str_replace(['presence-', 'private-'], '', $this->request->input('channel_name'));
    }
}