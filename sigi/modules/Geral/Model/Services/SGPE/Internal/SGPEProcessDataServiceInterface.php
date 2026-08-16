<?php

namespace Geral\Model\Services\SGPE\Internal;

/**
 * Interface para estabelecer os padrões da comunicação com SGPE
 * 
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 */
interface SGPEProcessDataServiceInterface{

    /**
     * Função para retornar informações do processo através de seus dados
     *
     * @param int $numero
     * @param int $ano
     * @param string $orgao
     * 
     * @return \Geral\Model\Services\SGPE\SGPEProcessData
     */
    public function getProcesso($numero, $ano, $orgao);
}