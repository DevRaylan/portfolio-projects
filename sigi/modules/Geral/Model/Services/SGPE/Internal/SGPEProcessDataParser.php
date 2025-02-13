<?php

namespace Geral\Model\Services\SGPE\Internal;

use ErrorException;
use Geral\Model\Services\SGPE\SGPEProcessData;
use stdClass;

/**
 * Classe para realizar o parser de dados da API do SGPE para o padrão do framework
 * 
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 */
class SGPEProcessDataParser{

    /**
     * @var stdClass
     */
    protected $apiData;

    /**
     * @var \Geral\Model\Services\SGPE\SGPEProcessData
     */
    protected $sgpeProcessData;

    /**
     * Define os dados para formatar
     *
     * @param stdClass $apiData
     * @return void
     */
    public function setApiData($apiData){
        $this->apiData = $apiData;
    }

    /**
     * Retorna o processo formatado
     *
     * @return \Geral\Model\Services\SGPE\SGPEProcessData
     */
    public function getProcessData(){
        return $this->sgpeProcessData;
    }

    /**
     * Realiza a formatação dos dados
     *
     * @return void
     */
    public function format(){
        if(!$this->apiData){
            throw new ErrorException('Os dados repassados não estão em formato válido.');
        }
        //Dados de retorno do processo
        $this->sgpeProcessData = new SGPEProcessData();
        if(is_array($this->apiData) && isset($this->apiData['processo'])){
            //Carrega os dados do processo
            $this->parserProcess($this->apiData['processo']);
        }
        else if(is_object($this->apiData) && isset($this->apiData->processo)){
            //Carrega os dados do processo
            $this->parserProcess($this->apiData->processo);
        }
        else{
            throw new ErrorException('Os dados repassados não estão em formato válido.');
        }
    }

    /**
     * Processa os dados recebidos
     *
     * @param stdClass $process
     * @return void
     */
    protected function parserProcess($process){
        //Recebe um array com as propriedades do processo e deve converter isso para um SGPEProcessData
        foreach($process as $attrName => $attrValue){
            $method = 'set'.ucfirst(strtolower($attrName));
            if(method_exists($this->sgpeProcessData, $method)) {
                call_user_func_array([$this->sgpeProcessData, $method], [$attrValue]);
            }
        }
    }
}