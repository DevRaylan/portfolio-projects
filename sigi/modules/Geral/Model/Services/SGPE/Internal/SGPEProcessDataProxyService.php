<?php

namespace Geral\Model\Services\SGPE\Internal;

/**
 * Classe para buscar comunicação com o SGPE
 * 
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 */
class SGPEProcessDataProxyService implements SGPEProcessDataServiceInterface{

    /**
     * @var \Geral\Model\Services\SGPE\Internal\SGPEDataServiceInterface
     */
    private $SGPEDataService;

    public function __construct(){
        $this->setSGPEDataService(new SGPEProcessDataApiService());
    }

    /**
     * Define o serviço responsável por buscar os dados
     * @param SGPEDataServiceInterface $sgpeDataService
     * @return void
     */
    public function setSGPEDataService(SGPEProcessDataServiceInterface $sgpeDataService){
        $this->SGPEDataService = $sgpeDataService;
    }

    /**
     * {@inheritDoc}
     */
    public function getProcesso($numero, $ano, $orgao){
        return $this->SGPEDataService->getProcesso($numero, $ano, $orgao);
    }
}