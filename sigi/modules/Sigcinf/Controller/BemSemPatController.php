<?php

namespace Sigcinf\Controller;

use \Sigcinf\Model\Factory\BemSemPatFactory;
use \Sigcinf\Model\Factory\BemSemPatHistoricoFactory;
use \Geral\Model\UserSession;
use Sigcinf\Model\Factory\UsuarioFactory;

class BemSemPatController extends \Geral\Controller\AbstractPrivateController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->AddJs('bemsempat-script.js', true);
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

        $recordSet = BemSemPatFactory::getAll($idCentroModerado, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getAllAtivos()
    {

        $recordSet = BemSemPatFactory::getAllAtivos(true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getById()
    {
        $id = (int) $this->url->getSegment(4);
        $recordSet = BemSemPatFactory::getById($id);
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

        $recordSet = BemSemPatFactory::getByBusca($busca, $centro, $setor, $idCentroModerado, true);

        $this->setSuccess('', ['data' => $recordSet]);    

//        if  (!empty($recordSet)) {
//            $this->setSuccess('Lista localizada com sucesso', ['data' => $recordSet]);    
//        } else {
//            $this->setError('Não foi possível localizar o item!');
//        }

        $this->sendResponse();

    }

// para teste
    public function getByCodigo()
    {
        $codigo = '232323';

        $recordSet = BemSemPatFactory::getByCodigo($codigo,true);

        if  (!empty($recordSet)) {
            $this->setSuccess('Lista localizada com sucesso', ['data' => $recordSet]);    
        } else {
            $this->setError('Não foi possível localizar o item!');
        }

        $this->sendResponse();

    }

    public function adicionar()
    {

        $codigo = $this->request->post('codigo');
        $descricao = $this->request->post('descricao');
        $status = $this->request->post('status');
        $centro = $this->request->post('centro');
        $setor = $this->request->post('setor');

        if ($status == '' || $descricao == '' || $codigo == '') {
            $this->setError('Erro ao adicionar bem sem patrimônio.');
        } else {
            try {      
                $registro = BemSemPatFactory::adicionar($codigo, $descricao, $status, $centro, $setor);
                $this->setSuccess('Bem sem patrimônio adicionado com sucesso.');
            } catch (\Exception $e) {
                $this->setError('Erro ao adicionar o bem sem patrimônio.');
            }
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
        $codigo = $this->request->post('codigo');
        $descricao = $this->request->post('descricao');
        $status = $this->request->post('status');
        $centro = $this->request->post('centro');
        $setor = $this->request->post('setor');

        try {
            $registro = BemSemPatFactory::atualizar($id, $codigo, $descricao, $status, $centro, $setor);
            $this->setSuccess('Bem sem patrimônio atualizado com sucesso.');
        } catch (\Exception $e) {
            $this->setError('Erro ao atualizar o bem sem patrimônio.');
        }

        $this->sendResponse();
    }

    public function atualizarQtd()
    {

        $dados['bemsempat'] = $this->request->post('id');
        $dados['motivo'] = $this->request->post('motivo');
        $dados['operacao'] = $this->request->post('operacao');
        $dados['qtd'] = $this->request->post('qtd');
        $dados['observacao'] = $this->request->post('observacao');

        try {
            $registro = BemSemPatHistoricoFactory::adicionar($dados);
            $registro = BemSemPatFactory::atualizarQtd($dados['bemsempat'], $dados['qtd'], $dados['operacao']);
            $this->setSuccess('Bem sem patrimônio atualizado com sucesso.');

        } catch (\Exception $e) {
            $this->setError('Erro ao atualizar o bem sem patrimônio.');
        }

        $this->sendResponse();
    }
    

}
