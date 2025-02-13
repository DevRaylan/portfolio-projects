<?php

namespace Sigcinf\Controller;

use \Sigcinf\Model\Factory\UnidadesFactory;
use \Geral\Model\UserSession;
use Sigcinf\Model\Factory\UsuarioFactory;

class UnidadesController extends \Geral\Controller\AbstractPrivateController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->AddJs('unidades-script.js', true);
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
        
        $recordSet = UnidadesFactory::getAll(true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getByCentroModerador()
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

        $recordSet = UnidadesFactory::getByCentroModerador($idCentroModerado, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getAllActive()
    {
        $onlyActive = true;
        $recordSet = UnidadesFactory::getAll($onlyActive, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getById()
    {
        $id = (int) $this->url->getSegment(4);

        $recordSet = UnidadesFactory::getById($id);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getByAbrev()
    {
        $abrev = 'REITORIA';

        $recordSet = UnidadesFactory::getByAbrev($abrev,true);

        if  (!empty($recordSet)) {
            $this->setSuccess('', ['data' => $recordSet]);    
        } else {
            $this->setError('Não foi possível localizar o centro!');
        };

        $this->sendResponse();
    }

    public function adicionar()
    {

        $nome = $this->request->post('nome');
        $abrev = $this->request->post('abrev');

        if ($nome == '' || $abrev == '') {
            $this->setError('Erro ao adicionar Unidades Udesc.');
        } else {
            $recordSet = UnidadesFactory::getByAbrev($abrev,true);
            if  (empty($recordSet)) {
                try {
                    $registro = UnidadesFactory::adicionar($nome, $abrev);
                    $this->setSuccess('Centro UDESC adicionada com sucesso.');
                } catch (\Exception $e) {
                    $this->setError('Erro ao adicionar Centro UDESC.');
                }   
           } else {
               $this->setError('Registro já se encontra na tabela.');
           };
        }
        
        $this->sendResponse();
    }

    public function atualizar()
    {
        $id = (int) $_POST['id'];
        $nome = $_POST['nome'];
        $abrev = $_POST['abrev'];

        try {
            $registro = UnidadesFactory::atualizar($id, $nome, $abrev);
            $this->setSuccess('Centro UDESC atualizada com sucesso.');
        } catch (\Exception $e) {
            $this->setError('Erro ao atualizar Centro UDESC.');
        }


        $this->sendResponse();
    }

}
