<?php

namespace Sigcinf\Controller;

use \Sigcinf\Model\Factory\UsuarioFactory;
use \Geral\Model\Factory\UserDataFactory;
use \Sigcinf\Model\Factory\UnidadesFactory;
use \Geral\Model\UserSession;

class UsuarioController extends \Geral\Controller\AbstractPrivateController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->AddJs('usuarios-script.js', true);
        $this->view();
    }

    /*
     * Métodos para chamadas AJAX
     * */

    public function getAll()
    {

        $recordSet = UsuarioFactory::getAll(true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getById()
    {
        $id = (int) $this->url->getSegment(4);

        $recordSet = UsuarioFactory::getById($id);
        $this->setSuccess('Usuario localizada com sucesso', ['data' => $recordSet]);

        $this->sendResponse();

    }

/// apenas para teste.
    public function getTeste()
    {
        $cpf = UserSession::getParam('cpf');

        $recordSet = UsuarioFactory::getByCpf1($cpf,true);

        if  (!empty($recordSet)) {
            $this->setSuccess('Usuario localizada com sucesso', ['data' => $recordSet]);    
        } else {
            $this->setError('Não foi possível localizar o usuário!');
        }

        $this->sendResponse();

    }


    public function getByCpf1()
    {
        $cpf = $this->request->post('cpf');

        $recordSet = UsuarioFactory::getByCpf1($cpf,true);

        if  (!empty($recordSet)) {
            $this->setSuccess('Usuario localizada com sucesso', ['data' => $recordSet]);    
        } else {
            try {
                $oUsuarioSession = UserDataFactory::getByCpf($cpf);
                $oCentroUdesc = UnidadesFactory::getByAbrev3($oUsuarioSession->getUnidade());
                $idCentro = 0;
                foreach($oCentroUdesc as $centroUdesc){
                    $idCentro = $centroUdesc->getId();
                }
                if  ($idCentro == 0) {
                    UnidadesFactory::adicionar($oUsuarioSession->getUnidade(), $oUsuarioSession->getUnidade());
                    $oCentroUdesc = UnidadesFactory::getByAbrev3($oUsuarioSession->getUnidade());
                    foreach($oCentroUdesc as $centroUdesc){
                        $idCentro = $centroUdesc->getId();
                    }
                };
                $dados['centro'] = $idCentro;
                $dados['nome'] = $oUsuarioSession->getNome();
                $dados['cpf'] = $cpf;
                $dados['email'] = $oUsuarioSession->getEmail();
                UsuarioFactory::adicionar($dados);
                $recordSet = UsuarioFactory::getByCpf1($cpf,true);
                if  (!empty($recordSet)) {
                    $this->setSuccess('Usuario localizada com sucesso', ['data' => $recordSet]);    
                } else {
                    $this->setError('Não foi possível localizar o usuário 2');
                };
            } catch (\Exception $e) {
                $this->setError('Erro ao adicionar usuário...');
                $this->sendResponse();
            };
        }

        $this->sendResponse();

    }

    public function getByCpf2()
    {
//        $cpf = $this->url->getSegment(4);
        $cpf = $this->request->post('cpf');

        $recordSet = UsuarioFactory::getByCpf2($cpf,true);

        if  (!empty($recordSet)) {
            $this->setSuccess('Usuario localizada com sucesso', ['data' => $recordSet]);    
        } else {
            $this->setError('Não foi possível localizar o usuário!');
        }

        $this->sendResponse();

    }

    public function adicionar()
    {

        $dados['centro'] = $this->request->post('centro');
        $dados['setor'] = $this->request->post('setor');
        $dados['nome'] = $this->request->post('nome');
        $dados['cpf'] = $this->request->post('cpf');
        $dados['email'] = $this->request->post('email');

        if ($this->request->getAllBodyParams() != null) {
            $recordSet = UsuarioFactory::getByCpf1($dados['cpf'],true);
            if  (empty($recordSet)) {                
                try {
                    $registro = UsuarioFactory::adicionar($dados);
                    $this->setSuccess('Usuario adicionada com sucesso.');
                } catch (\Exception $e) {
                    $this->setError('Erro ao adicionar usuário.');
                }
            } else {
                $this->setError('Usuário já existe.');
            }
        } else {
            $this->setError('Usuario inválido.');
        }

        $this->sendResponse();

    }

    public function atualizar()
    {

        $dados['id'] = (int) $_POST['id'];
        $dados['centro'] = $this->request->post('centro');
        $dados['setor'] = $this->request->post('setor');
        $dados['nome'] = $this->request->post('nome');
        $dados['cpf'] = $this->request->post('cpf');
        $dados['email'] = $this->request->post('email');

        try {
            $registro = UsuarioFactory::atualizar($dados);
            $this->setSuccess('Usuário atualizado com sucesso.');
        } catch (\Exception $e) {
            $this->setError('Erro ao atualizar usuário.');
        }

        $this->sendResponse();
    }

}
