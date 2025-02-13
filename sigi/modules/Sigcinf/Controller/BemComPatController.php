<?php

namespace Sigcinf\Controller;

use \Sigcinf\Model\Factory\BemComPatFactory;
use \Geral\Model\UserSession;
use Sigcinf\Model\Factory\UsuarioFactory;

class BemComPatController extends \Geral\Controller\AbstractPrivateController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->AddJs('bemcompat-script.js', true);
        $this->view();
    }

    /*
     * Métodos para chamadas AJAX
     * */

    public function getAll()
    {

        if(!$this->auth->isAllowedTransactions(['dev','admin','gerente'])) {
            $this->setError('Usuário sem permissão de acesso.');
            $this->sendResponse();
            exit;
        }
        
        // pega o Centro do usuario.
        if ($this->auth->isAllowedTransactions(['dev','admin'])) {
            $idCentroModerado = 0;
        } else {
            $cpf = UserSession::getParam('cpf');
            $oUsuario = UsuarioFactory::getByCpf1($cpf);
            $oCentro = $oUsuario->getUnidade();
            $idCentroModerado = $oCentro->getId();
        };

        $recordSet = BemComPatFactory::getAll($idCentroModerado, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();

    }
    
    public function getAllActive()
    {
        $onlyActive = true;
        $recordSet = BemComPatFactory::getAll($onlyActive, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getById()
    {
        $id = (int) $this->url->getSegment(4);
        $recordSet = BemComPatFactory::getById($id);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getByBusca()
    {

        if(!$this->auth->isAllowedTransactions(['dev','admin','gerente'])) {
            $this->setError('Usuário sem permissão de acesso.');
            $this->sendResponse();
            exit;
        }
        
        // pega o Centro do usuario.
//        if ($this->auth->isAllowedTransactions(['dev','admin'])) {
//            $idCentroModerado = 0;
//        } else {
//            $cpf = UserSession::getParam('cpf');
//            $oUsuario = UsuarioFactory::getByCpf1($cpf);
//            $oCentro = $oUsuario->getUnidade();
//            $idCentroModerado = $oCentro->getId();
//        };

        $busca = $this->request->post('busca');
        $centro = $this->request->post('centro');
        $setor = $this->request->post('setor');

        $idCentroModerado = $centro;

        $recordSet = BemComPatFactory::getByBusca($busca, $centro, $setor, $idCentroModerado, true);

        $this->setSuccess('', ['data' => $recordSet]);    

//        if  (!empty($recordSet)) {
//            $this->setSuccess('Lista localizada com sucesso', ['data' => $recordSet]);    
//        } else {
//            $this->setError('Não foi possível localizar o item!');
//        }

        $this->sendResponse();

    }

    public function adicionar()
    {
        
        $codigo = $this->request->post('patrimonio');
        $descricao = $this->request->post('descricao');
        $status = $this->request->post('status');
        $centro = $this->request->post('centro');
        $setor = $this->request->post('setor');

        if ($this->request->getAllBodyParams() != null) {
            try {
                $registro = BemComPatFactory::adicionar($codigo, $descricao, $status, $centro, $setor);
                $this->setSuccess('Bem adicionado com sucesso.');
            } catch (\Exception $e) {
                $this->setError('Erro ao adicionar bem.');
            }
        } else {
            $this->setError('Bem com patrimônio inválido.');
        }

        $this->sendResponse();
    }

    public function atualizar()
    {

        if(!$this->auth->isAllowedTransactions(['dev','admin'])) {
            $this->setError('Usuário sem permissão de acesso.');
            $this->sendResponse();
            exit;
        }

        $id = $this->request->post('id');
        $codigo = $this->request->post('patrimonio');
        $descricao = $this->request->post('descricao');
        $status = $this->request->post('status');
        $centro = $this->request->post('centro');
        $setor = $this->request->post('setor');

        try {
            $registro = BemComPatFactory::atualizar($id, $codigo, $descricao, $status, $centro, $setor);
            $this->setSuccess('Bem atualizado com sucesso.');
        } catch (\Exception $e) {
            $this->setError('Erro ao atualizar o bem.');
        }


        $this->sendResponse();
    }


}
