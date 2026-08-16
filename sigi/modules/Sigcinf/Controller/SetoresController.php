<?php

namespace Sigcinf\Controller;

use \Sigcinf\Model\Factory\SetoresFactory;
use \Geral\Model\Factory\UserDataFactory;
use \Sigcinf\Model\Factory\UnidadesFactory;
use \Geral\Model\UserSession;

class SetoresController extends \Geral\Controller\AbstractPrivateController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->AddJs('setores-script.js', true);
        $this->view();
    }

    /*
     * Métodos para chamadas AJAX
     * */

    public function getAll()
    {

        $recordSet = SetoresFactory::getAll(true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getById()
    {
        $id = (int) $this->url->getSegment(4);

        $recordSet = SetoresFactory::getById($id);
        $this->setSuccess('Setor localizada com sucesso', ['data' => $recordSet]);

        $this->sendResponse();

    }

    
/// apenas para teste.
    public function getTeste()
    {
        $cpf = UserSession::getParam('cpf');

        $recordSet = SetoresFactory::getByCpf1($cpf,true);

        if  (!empty($recordSet)) {
            $this->setSuccess('Setor localizada com sucesso', ['data' => $recordSet]);    
        } else {
            $this->setError('Não foi possível localizar o setor!');
        }

        $this->sendResponse();

    }

    public function getByUnidade()
    {
        
        $unidade = $this->request->get('unidadeId');
        $recordSet = SetoresFactory::getByUnidade($unidade,true);

        if  (!empty($recordSet)) {
            $this->setSuccess('Unidade localizada com sucesso', ['data' => $recordSet]);    
        } else {
            $this->setError('Não foi possível localizar a unidade!');
        };

        $this->sendResponse();

    }    

    public function getByCpf1()
    {
        $cpf = $this->request->post('cpf');

        $recordSet = SetoresFactory::getByCpf1($cpf,true);

        if  (!empty($recordSet)) {
            $this->setSuccess('Usuario localizada com sucesso', ['data' => $recordSet]);    
        } else {
            $this->setError('Não foi possível localizar o setor!');
        };

        $this->sendResponse();

    }

    public function getByCpf2()
    {
//        $cpf = $this->url->getSegment(4);
        $cpf = $this->request->post('cpf');

        $recordSet = SetoresFactory::getByCpf2($cpf,true);

        if  (!empty($recordSet)) {
            $this->setSuccess('Usuario localizada com sucesso', ['data' => $recordSet]);    
        } else {
            $this->setError('Não foi possível localizar o setor!');
        }

        $this->sendResponse();

    }

    public function adicionar()
    {

        $dados['centro'] = $this->request->post('centro');
        $dados['nome'] = $this->request->post('nome');
        $dados['email'] = $this->request->post('email');

        if ($this->request->getAllBodyParams() != null) {
            try {
                $registro = SetoresFactory::adicionar($dados);
                $this->setSuccess('Setor adicionada com sucesso.');
            } catch (\Exception $e) {
                $this->setError('Erro ao adicionar usuário.');
            }
        } else {
            $this->setError('Setor inválido.');
        }

        $this->sendResponse();

    }

    public function atualizar()
    {

        $dados['id'] = (int) $_POST['id'];
        $dados['centro'] = $this->request->post('centro');
        $dados['nome'] = $this->request->post('nome');
        $dados['email'] = $this->request->post('email');

        try {
            $registro = SetoresFactory::atualizar($dados);
            $this->setSuccess('Setor atualizado com sucesso.');
        } catch (\Exception $e) {
            $this->setError('Erro ao atualizar setor.');
        }

        $this->sendResponse();
    }

}
