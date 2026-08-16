<?php
namespace Geral\Model;

/**
 * Classe para acessar os parâmetros enviados via GET e POST na requisição
 * @author Glauco David Laicht
 */
class RequestParams{

    /**
     * @var Array
     */
    private $query;
    
    /**
     * @var Array
     */
    private $body;

    /**
     * Retorna todo o GET
     * @return Array
     */
    public function getAllQueryParams(){
        if(!isset($this->query)){
            $this->query = $_GET;
            //@todo: Adicionar alguma validação que identifique injeção
            //foreach($_GET as $key => $value){
            //    $this->query[$key] = $value;
            //}
        }
        return $this->query;
    }

    /**
     * Retorna todo o POST
     * @return Array
     */
    public function getAllBodyParams(){
        if(!isset($this->body)){
            $this->body = $_POST;
            //@todo: Adicionar alguma validação que identifique injeção
            //foreach($_GET as $key => $value){
            //    $this->query[$key] = $value;
            //}
        }
        return $this->body;
    }

    /**
     * Retorna um campo que está no Get
     * @return Mixed
     */
    public function get($nomeCampo, $default=null){
        $get   = $this->getAllQueryParams();
        $value = isset($get[$nomeCampo]) ? $get[$nomeCampo] : $default;
        return $value;
    }

    /**
     * Retorna um campo que está no Post
     * @return Mixed
     */
    public function post($nomeCampo, $default=null){
        $post = $this->getAllBodyParams();
        return isset($post[$nomeCampo]) ? $post[$nomeCampo] : $default;
    }

    /**
     * Retorna um campo que está no GET ou no POST
     * @return Mixed
     */
    public function input($nomeCampo, $default=null){
        $get  = $this->getAllQueryParams();
        if(isset($get[$nomeCampo])){
            return $get[$nomeCampo];
        }
        return $this->post($nomeCampo, $default);
    }
}