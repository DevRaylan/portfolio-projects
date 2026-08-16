<?php

namespace Sigcinf\Controller;

use \Sigcinf\Model\Factory\CategoriasFactory;
use \Geral\Model\UserSession;
use Sigcinf\Model\Factory\UsuarioFactory;

class CategoriasController extends \Geral\Controller\AbstractPrivateController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->AddJs('categorias-script.js', true);
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
        
        $recordSet = CategoriasFactory::getAll(true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getById()
    {
        $id = (int) $this->url->getSegment(4);

        $recordSet = CategoriasFactory::getById($id);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function adicionar()
    {

        $nome = $this->request->post('nome');

        if ($nome == '' ) {
            $this->setError('Erro ao adicionar Unidades Udesc.');
        } else {
            try {
                $registro = CategoriasFactory::adicionar($nome);
                $this->setSuccess('Categoria adicionada com sucesso.');
            } catch (\Exception $e) {
                $this->setError('Erro ao adicionar categoria.');
            };
        }
        
        $this->sendResponse();
    }

    public function atualizar()
    {
        $id = (int) $_POST['id'];
        $nome = $_POST['nome'];

        try {
            $registro = CategoriasFactory::atualizar($id, $nome);
            $this->setSuccess('Categoria atualizada com sucesso.');
        } catch (\Exception $e) {
            $this->setError('Erro ao atualizar categoria.');
        }

        $this->sendResponse();
    }

}
