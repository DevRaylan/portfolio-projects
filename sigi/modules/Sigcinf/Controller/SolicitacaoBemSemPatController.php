<?php

namespace Sigcinf\Controller;

use Sigcinf\Model\Factory\SolicitacaoBemSemPatFactory;
use \Geral\Model\UserSession;

class SolicitacaoBemSemPatController extends \Geral\Controller\AbstractPrivateController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
//        $this->AddJs('solicitacoes-script.js', true);
//        $this->AddCss('style.css');

//        $this->view();
    }

    /*
     * Métodos para chamadas AJAX
     * */

     public function getAll()
    {

        $recordSet = SolicitacaoBemSemPatFactory::getAll(true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getById()
    {

        $id = (int) $this->url->getSegment(4);

        $recordSet = SolicitacaoBemSemPatFactory::getById($id, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    // para teste
    public function getBemSemPat()
    {
        $id = '1';
        
        $recordSet = SolicitacaoBemSemPatFactory::getBemSemPat($id, true);

        if  (!empty($recordSet)) {
            $this->setSuccess('', ['data' => $recordSet]);    
        } else {
            $this->setError('Não foi possível localizar o usuário.');
        }

        $this->sendResponse();
    }

}

