<?php

namespace Geral\Controller;

/**
 *               ______________
 *              /              \
 *       #####  |    ATENÇÃO   |  #####
 *              \______________/
 * 
 * Este controller serve para executar correções 
 * emergenciais nos ambientes DEV, HOMOLOGAÇÃO E PRODUÇÃO!
 *
 */

class HotFixController extends AbstractPublicController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        // Anular exceção lançada pelo doctrine ao auditar esta ação.
    }

    public function index()
    {
        $this->url->redirect('Update', 'check');
    }

    // Remove dados sensiveis dos registros de log referentes a autenticação.
    public function limpaDadosAutenticacaoLog()
    {
        $sqlClearDadosSensiveis = "
            UPDATE log
            SET detail = 'dados restritos'
            WHERE url = 'POST /Geral/Login/autenticar'
            AND detail <> 'dados restritos'; 
       ";

        // executa SQL
        $db = $GLOBALS['em']->getConnection()->getWrappedConnection();

        $querySet = $db->prepare($sqlClearDadosSensiveis);
        $querySet->execute();

        echo '--> Dados sensiveis removidos.' . PHP_EOL;
    }
}
