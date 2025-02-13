<?php

namespace Geral\Model\Services\SGPE;

use Geral\Model\Services\SGPE\Internal\SGPEProcessDataProxyService;

/**
 * Classe para buscar informações do processo
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 */
abstract class SGPEProcessDataFactory{

    /**
     * @var \Geral\Model\Services\SGPE\Internal\SGPEDataServiceInterface
     */
    private static $service;

    /**
     * Retorna a instância do responsável por buscar as informações do SGPE
     * @return \Geral\Model\Services\SGPE\Internal\SGPEDataProxyService
     */
    private static function getInstance(){
        if(!isset(static::$service)){
            static::$service = new SGPEProcessDataProxyService();
        }
        return static::$service;
    }

    /**
     * Retorna um objeto com dados do processo
     *
     * @param int $numero
     * @param int $ano
     * @param string $orgao
     * 
     * @return \Geral\Model\Services\SGPE\ProcessData
     */
    public static function getProcesso($numero, $ano, $orgao='UDESC'){
        return static::getInstance()->getProcesso($numero, $ano, $orgao);
    }
}