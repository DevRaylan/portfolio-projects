<?php
namespace Geral\Controller;

use \Geral\Model\Factory\UserDataSimulatorFactory;
use Geral\Model\Services\UserDataSimulatorService;

class UserDataSimulatorController extends \Geral\Controller\AbstractPrivateController
{
    function __construct( )
    {
        parent::__construct( );
    }

    // =================================================================================================================

    public function index( )
    {
        $this->view();
    }

    public function getById()
    {
        try {
            $usuario = UserDataSimulatorFactory::getById($this->url->getSegment(4), true);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            $this->sendResponse();
        }

        $this->setSuccess('', ['user' => $usuario]);
        $this->sendResponse();
    }

    public function getByCpf()
    {
        try {
            $usuario = UserDataSimulatorFactory::getByCpf($this->url->getSegment(4), true);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            $this->sendResponse();
        }
        
        $this->setSuccess('', ['user' => $usuario]);
        $this->sendResponse();
    }

    public function getAll()
    {
        try {
            $usuario = UserDataSimulatorFactory::getAll(true);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            $this->sendResponse();
        }
        
        $this->setSuccess('', ['data' => $usuario]);
        $this->sendResponse();
    }

    public function getAlunos()
    {
        try {
            $usuario = UserDataSimulatorFactory::getAlunos(true);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            $this->sendResponse();
        }
        
        $this->setSuccess('', ['users' => $usuario]);
        $this->sendResponse();
    }

    public function getProfessores()
    {
        try {
            $usuario = UserDataSimulatorFactory::getProfessores(true);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            $this->sendResponse();
        }
        
        $this->setSuccess('', ['users' => $usuario]);
        $this->sendResponse();
    }

    public function getTecnicos()
    {
        try {
            $usuario = UserDataSimulatorFactory::getTecnicos(true);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            $this->sendResponse();
        }
        
        $this->setSuccess('', ['users' => $usuario]);
        $this->sendResponse();
    }

    // CRUD ====================================================================

    public function add()
    {
        UserDataSimulatorFactory::add($_POST['dados']);

        $this->setSuccess('Dados adicionados com sucesso.');
        $this->sendResponse();
    }

    public function update()
    {
        $id = $this->url->getSegment(4);
        $dados = $_POST['dados'];

        try { 
            UserDataSimulatorFactory::update($id, $dados);
            $this->setSuccess('Dados atualizados com sucesso.');
        } catch (\Exception $e) {
            $this->setError('Erro ao salvar dados: '."\n\nDetalhe:\n".$e->getMessage()."\n");
        }
        
        $this->sendResponse();
    }

    public function remove()
    {
        $id = $this->url->getSegment(4);

        try { 
            UserDataSimulatorFactory::remove($id);
            $this->setSuccess('Dados do usuário removidos com sucesso.');
        } catch (\Exception $e) {
            $this->setError('Erro ao remover dados: '."\n\nDetalhe:\n".$e->getMessage()."\n");
        }
        
        $this->sendResponse();
    }

    // Funções auxiliares ======================================================

    public function loadDataFile()
    {
        $dataFileName = $this->url->getSegment(4);

        try {
            UserDataSimulatorFactory::loadFromDataFile($dataFileName);
            //unlink(DIR_FILES.'/Geral/'.DIR_NAME_TEMP.'/'.$dataFileName);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            $this->sendResponse();
        }
        
        $this->setSuccess('Dados simulados carregados com sucesso.');
        $this->sendResponse();        
    }
}