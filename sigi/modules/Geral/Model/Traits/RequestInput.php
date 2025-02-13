<?php
namespace Geral\Model\Traits;

/**
 * Trait para acessar valores enviados através da requisição
 * @author Glauco David Laicht
 */
trait RequestInput{

    /**
     * Retorna todos os parâmetros enviados via GET/Query
     * @return Array
     */
    public function getAllQueryParams(){
        return $this->params->getAllQueryParams();
    }

    /**
     * Retorna todos os parâmetros enviados via POST/Body
     * @return Array
     */
    public function getAllBodyParams(){
        return $this->params->getAllBodyParams();
    }

    /**
     * Retorna todos os parâmetros enviados via GET/Query ou POST/Body
     * @return Array
     */
    public function getAll(){
        $query   = $this->params->getAllQueryParams();
        $body    = $this->params->getAllBodyParams();
        return array_merge($query, $body);
    }

    /**
     * Retorna um parâmetro enviado via QueryString/GET
     * @param $nomeCampo string Nome do campo
     * @param $default mixed Valor caso não exista o campo
     * @return mixed
     */
    public function get($nomeCampo, $default=null){
        return $this->params->get($nomeCampo, $default);
    }

    /**
     * Retorna um parâmetro enviado via Body/POST
     * @param $nomeCampo string Nome do campo
     * @param $default mixed Valor caso não exista o campo
     * @return mixed
     */
    public function post($nomeCampo, $default=null){
        return $this->params->post($nomeCampo, $default);
    }

    /**
     * Retorna um parâmetro enviado via QueryString/GET ou Body/POST
     * @param $nomeCampo string Nome do campo
     * @param $default mixed Valor caso não exista o campo
     * @return mixed
     */
    public function input($nomeCampo, $default=null){
        return $this->params->input($nomeCampo, $default);
    }

    /**
     * Retorna um parâmetro enviado via Query/GET ou Body/POST para uma lista de parâmetros desejados
     * @param $campos Array|Mixed Somente os campos que seja ser carregado da QueryString ou Body
     * @return mixed
     */
    public function only($campos){
        $keys    = is_array($campos) ? $campos : func_get_args();
        $query   = $this->params->getAllQueryParams();
        $body    = $this->params->getAllBodyParams();
        $inputs  = array_merge($query, $body);
        $results = [];
        foreach($keys as $key){
            $results[$key] = isset($inputs[$key]) ? $inputs[$key] : null;
        }
        return $results;
    }
}