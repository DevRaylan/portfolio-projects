<?php
namespace Geral\Model\Traits;

/**
 * Trait para acessar valores enviados através da requisição em um determinado formato
 * @author Glauco David Laicht
 */
trait RequestInputType{

    /**
     * Retorna um parâmetro enviado via QueryString/GET formato como int
     * @param $nomeCampo string Nome do campo
     * @param $default mixed Valor caso não exista o campo
     * @return int
     */
    public function getQueryParamAsInt($nomeCampo, $default=null){
        return (int) $this->params->get($nomeCampo, $default);
    }

    /**
     * Retorna um parâmetro enviado via QueryString/GET formato como float
     * @param $nomeCampo string Nome do campo
     * @param $default mixed Valor caso não exista o campo
     * @return float
     */
    public function getQueryParamAsFloat($nomeCampo, $default=null){
        return (float) $this->params->get($nomeCampo, $default);
    }

    /**
     * Retorna um parâmetro enviado via Query/GET formato como boolean
     * @param $nomeCampo string Nome do campo
     * @param $default mixed Valor caso não exista o campo
     * @return boolean
     */
    public function getQueryParamAsBoolean($nomeCampo, $default=null){
        return filter_var($this->params->get($nomeCampo, $default), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Retorna um parâmetro enviado via Body/POST formato como int
     * @param $nomeCampo string Nome do campo
     * @param $default mixed Valor caso não exista o campo
     * @return int
     */
    public function getBodyParamAsInt($nomeCampo, $default=null){
        return (int) $this->params->post($nomeCampo, $default);
    }

    /**
     * Retorna um parâmetro enviado via Body/POST formato como float
     * @param $nomeCampo string Nome do campo
     * @param $default mixed Valor caso não exista o campo
     * @return float
     */
    public function getBodyParamAsFloat($nomeCampo, $default=null){
        return (float) $this->params->post($nomeCampo, $default);
    }

    /**
     * Retorna um parâmetro enviado via Body/POST formato como boolean
     * @param $nomeCampo string Nome do campo
     * @param $default mixed Valor caso não exista o campo
     * @return boolean
     */
    public function getBodyParamAsBoolean($nomeCampo, $default=null){
        return filter_var($this->params->post($nomeCampo, $default), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Retorna um parâmetro enviado via QueryString/GET ou Body/POST formato como int
     * @param $nomeCampo string Nome do campo
     * @param $default mixed Valor caso não exista o campo
     * @return int
     */
    public function getInputAsInt($nomeCampo, $default=null){
        return (int) $this->params->input($nomeCampo, $default);
    }

    /**
     * Retorna um parâmetro enviado via QueryString/GET ou Body/POST formato como float
     * @param $nomeCampo string Nome do campo
     * @param $default mixed Valor caso não exista o campo
     * @return float
     */
    public function getInputAsFloat($nomeCampo, $default=null){
        return (float) $this->params->input($nomeCampo, $default);
    }

    /**
     * Retorna um parâmetro enviado via QueryString/GET ou Body/POST formato como boolean
     * @param $nomeCampo string Nome do campo
     * @param $default mixed Valor caso não exista o campo
     * @return boolean
     */
    public function getInputAsBoolean($nomeCampo, $default=null){
        return filter_var($this->params->input($nomeCampo, $default), FILTER_VALIDATE_BOOLEAN);
    }
}