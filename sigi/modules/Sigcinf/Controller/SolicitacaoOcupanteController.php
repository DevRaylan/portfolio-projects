<?php

namespace Sigcinf\Controller;

use Sigcinf\Model\Factory\SolicitacaoOcupanteFactory;
use \Geral\Model\UserSession;

class SolicitacaoOcupanteController extends \Geral\Controller\AbstractPrivateController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
    }

    /*
     * Métodos para chamadas AJAX
     * */

     public function getAll()
    {

        $recordSet = SolicitacaoOcupanteFactory::getAll(true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getById()
    {

        $id = (int) $this->url->getSegment(4);

        $recordSet = SolicitacaoOcupanteFactory::getById($id, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getOcupanteByCpf()
    {
        $cpf = UserSession::getParam('cpf');
        $id = '8';
        
        $recordSet = SolicitacaoOcupanteFactory::getOcupanteByCpf($cpf, $id, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

//  para teste
    public function getOcupanteByIdSolicitacao()
    {
        $idSolicitacao = 1;
        $cpf = '01976635918';
        
        $recordSet = SolicitacaoOcupanteFactory::getOcupanteByIdSolicitacao($idSolicitacao, $cpf, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }





}