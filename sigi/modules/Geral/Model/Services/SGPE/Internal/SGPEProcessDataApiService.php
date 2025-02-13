<?php

namespace Geral\Model\Services\SGPE\Internal;

/**
 * Classe para buscar comunicação com o SGPE através da API
 * 
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 */
class SGPEProcessDataApiService extends SGPEBaseDataApiService implements SGPEProcessDataServiceInterface{

    /**
     * {@inheritDoc}
     */
    public function getProcesso($numero, $ano, $orgao){
        //Realiza a requisição
        $response = $this->processGet($this->getUrl().$this->getEndpointGetProcesso(), [
            'numero' => $numero,
            'ano'    => $ano,
            'orgao'  => $orgao
        ]);
        //Realiza formatação de dados
        $oProcessParser = new SGPEProcessDataParser();
        $oProcessParser->setApiData($response['data']);
        $oProcessParser->format();
        //Retorna os dados do processo
        return $oProcessParser->getProcessData();
    }

    /**
     * Retorna o endpoint para carregar dados do processo
     * 
     * @return string
     */
    protected function getEndpointGetProcesso(){
        return (isset($this->authConfig['endpointGetProcesso']) ? $this->authConfig['endpointGetProcesso'] : 'getProcesso');
    }
}