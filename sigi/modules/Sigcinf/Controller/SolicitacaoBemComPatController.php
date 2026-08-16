<?php

namespace Sigcinf\Controller;

use Sigcinf\Model\Factory\SolicitacaoBemComPatFactory;
use \Geral\Model\UserSession;

class SolicitacaoBemComPatController extends \Geral\Controller\AbstractPrivateController
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

        $recordSet = SolicitacaoBemComPatFactory::getAll(true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getById()
    {

        $id = (int) $this->url->getSegment(4);

        $recordSet = SolicitacaoBemComPatFactory::getById($id, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

}

