<?php

namespace Sigcinf\Controller;

use \Sigcinf\Model\Factory\BemSemPatHistoricoFactory;
use \Geral\Model\UserSession;

class BemSemPatHistoricoController extends \Geral\Controller\AbstractPrivateController
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

        $recordSet = BemSemPatHistoricoFactory::getAll(true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getById()
    {
        $id = (int) $this->url->getSegment(4);

        $recordSet = BemSemPatHistoricoFactory::getById($id);
        $this->setSuccess('Historico localizado com sucesso', ['data' => $recordSet]);

        $this->sendResponse();

    }

    public function getByBemSemPat()
    {
        $busca = $this->request->post('busca');
        $recordSet = BemSemPatHistoricoFactory::getByBemSemPat($busca,true);

        if  (!empty($recordSet)) {
            $this->setSuccess('Lista localizada com sucesso', ['data' => $recordSet]);    
        } else {
            $this->setError('Não foi possível localizar o item!');
        }

        $this->sendResponse();

    }

    public function adicionar()
    {

        $dados['bemsempat'] = $this->request->post('bemsempat');
        $dados['motivo'] = $this->request->post('motivo');
        $dados['operacao'] = $this->request->post('operacao');
        $dados['qtd'] = $this->request->post('qtd');
        $dados['observacao'] = $this->request->post('observacao');

        if ($this->request->getAllBodyParams() != null) {
            try {
                $registro = BemSemPatHistoricoFactory::adicionar($dados);
                $this->setSuccess('Historico adicionado com sucesso.');
            } catch (\Exception $e) {
                $this->setError('Erro ao adicionar historico.');
            }
        } else {
            $this->setError('Historico inválido.');
        }

        $this->sendResponse();

    }

    public function atualizar()
    {

        $dados['id'] = (int) $_POST['id'];
        $dados['centro'] = $this->request->post('centro');
        $dados['nome'] = $this->request->post('nome');
        $dados['cpf'] = $this->request->post('cpf');
        $dados['email'] = $this->request->post('email');

        try {
            $registro = BemSemPatHistoricoFactory::atualizar($dados);
            $this->setSuccess('Usuário atualizado com sucesso.');
        } catch (\Exception $e) {
            $this->setError('Erro ao atualizar usuário.');
        }

        $this->sendResponse();
    }

}
