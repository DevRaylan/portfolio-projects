<?php

namespace Geral\Model\Services\SGPE\Internal;

use ErrorException;
use Exception;
use Geral\Model\Config;
use Geral\Model\Services\RestClient;

/**
 * Classe base para buscar comunicação com o SGPE através da API
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 */
abstract class SGPEBaseDataApiService{

    /**
     * @var \Geral\Model\Services\RestClient
     */
    protected $restClient;

    /**
     * @var array
     */
    protected $authConfig;

    public function __construct(){
        $this->authConfig = Config::getValue('authSGPE', 'Geral');
        $this->restClient = new RestClient();
    }

    /**
     * Retorna a URL para a requisição
     * 
     * @return string
     */
    protected function getUrl(){
        return (isset($this->authConfig['url']) ? $this->authConfig['url'] : '');
    }

    /**
     * Retorna o token para a requisição
     * 
     * @return string
     */
    protected function getToken(){
        return (isset($this->authConfig['tokenSystem']) ? $this->authConfig['tokenSystem'] : '');
    }

    /**
     * Retorna a chave para a requisição
     * 
     * @return string
     */
    protected function getKey(){
        return (isset($this->authConfig['tokenSecret']) ? $this->authConfig['tokenSecret'] : '');
    }

    /**
     * Realiza uma requisição do tipo GET
     * 
     * @param string $url
     * @param array $data
     * @return mixed
     */
    protected function sendGet($url, $data=[]){
        return $this->restClient->sendJsonGet($this->getToken(), $this->getKey(), $url, $data);
    }

    /**
     * Realiza uma requisição do tipo POST
     * 
     * @param string $url
     * @param array $data
     * @return mixed
     */
    protected function sendPost($url, $data=[]){
        return $this->restClient->sendJsonPost($this->getToken(), $this->getKey(), $url, $data);
    }

    /**
     * Processa uma requisição do tipo GET
     *
     * @param string $url
     * @param array $data
     * @return void
     */
    protected function processGet($url, $data=[]){
        return $this->processRequest('GET', $url, $data);
    }

    /**
     * Processa uma requisição do tipo GET
     *
     * @param string $url
     * @param array $data
     * @return void
     */
    protected function processPost($url, $data=[]){
        return $this->processRequest('POST', $url, $data);
    }

    /**
     * Processa uma requisição do tipo POST
     *
     * @param string $url
     * @param array $data
     * @return void
     */
    private function processRequest($type, $url, $data=[]){
        $response = null;
        try{
            //Realiza requisição
            switch($type){
                case 'GET':
                    $response = $this->sendGet($url, $data);
                    break;
                case 'POST':
                    $response = $this->sendPost($url, $data);
                    break;
            }
        }
        catch(Exception $ex){
            $this->catchResponse($ex);
        }
        //Verifica a resposta
        $this->processResponse($response);
        //Devolve a resposta
        return $response;
    }

    /**
     * Trata a exceção por padrão
     *
     * @param Exception $ex
     * @return void
     */
    protected function catchResponse(Exception $ex){
        throw new ErrorException('Não foi possível processar a requisção ao SGP-e. '.$ex->getMessage());
    }

    /**
     * Processa o retorno da requisição
     *
     * @param mixed $response
     * @return void
     */
    protected function processResponse($response){
        if(!$response || !isset($response['result'])){
            //Se não retornou os dados  ou eles não vieram padronizados
            throw new ErrorException('Não foi possível processar a requisção ao SGP-e. Tente novamente em instantes.');
        }

        if($response['result'] != 'success' || !isset($response['data'])){
            //Caso ocorreu um erro padronizado
            $errorMessage = isset($response['msg']) ? ' (' . $response['msg'] . ')': '';
            throw new ErrorException('Não foi possível processar a requisção ao SGP-e. '.$errorMessage);
        }
    }
}