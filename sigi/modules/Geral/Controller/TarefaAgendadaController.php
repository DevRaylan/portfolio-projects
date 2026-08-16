<?php

namespace Geral\Controller;

use \Geral\Model\Exception\ErrorException;
use Geral\Model\Factory\TarefaAgendadaFactory;

/**
 * Classe de controlador para gerenciamento de tarefas agendadas
 * 
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 */
class TarefaAgendadaController extends AbstractPrivateController{

    public function __construct(){
        parent::__construct();
        // Acesso somente a usuários com a transação "admin"
        if (!$this->auth->isAllowedTransactions(['admin'])) {
            throw new ErrorException("Usuário sem permissão de acesso.");
        }
    }

    public function index(){
        $this->view();
    }

    public function form(){
        $id       = $this->url->getSegment(4);
        if($id){
            $registro = TarefaAgendadaFactory::getById($id, false);
            $this->addVarView('registro', $registro);
        }
        $this->view();
    }

    /**
     * Retorna registros pelo Id
     */
    public function getById(){
        $id       = $this->url->getSegment(4);
        $registro = TarefaAgendadaFactory::getById($id);
        
        $this->setSuccess('Registros localizados com sucesso.', ["registro" => $registro]);
        $this->sendResponse();
    }

    /**
     * Retorna todos os registros
     */
    public function getAll(){
        $registros = TarefaAgendadaFactory::getAll(true);
        $this->setSuccess('Registros localizados com sucesso.', ["registros" => $registros]);
        $this->sendResponse();
    }

    /**
     * Salva um registro
     * 
     */
    public function save()
    {
        $id       = $_POST['id'];
        $data     = [
            'nome'       => $_POST['nome'],
            'descricao'  => $_POST['descricao'],
            'app'        => $_POST['app'],
            'controller' => $_POST['controller'],
            'action'     => $_POST['action'],
            'parametros' => $_POST['parametros'],
            'minuto'     => $_POST['minuto'],
            'hora'       => $_POST['hora'],
            'diaMes'     => $_POST['diaMes'],
            'mes'        => $_POST['mes'],
            'diaSemana'  => $_POST['diaSemana']
        ];

        $this->validate($data);

        if(!empty($id)) {
            $this->atualizar($id, $data);
        }
        else{
            $this->add($data);
        }

        $this->sendResponse();
    }

    /**
     * Altera um registro
     */
    public function update()
    {
        $this->save();
    }

    /**
     * Remomve um registro
     */
    public function remove()
    {
        $id = (int) $this->url->getSegment(4);
        try {
            TarefaAgendadaFactory::remove($id);
            $this->refreshSchedule();
            $this->setSuccess('Registro removido com sucesso.');
        } 
        catch (\Exception $e) {
            throw new ErrorException("Erro ao remover o registro.");
        }
        $this->sendResponse();
    }

    /**
     * Atualiza o status da tarefa
     */
    public function changeStatus()
    {
        $id = (int) $this->url->getSegment(4);
        try {
            $tarefa = TarefaAgendadaFactory::changeStatus($id);
            $this->refreshSchedule();
            $this->setSuccess('Registro '.($tarefa->getSituacao() ? 'Ativado' : 'Desativado').' com sucesso.');
        } 
        catch (\Exception $e) {
            throw new ErrorException("Erro ao alterar situação do registro.");
        }
        $this->sendResponse();
    }

    /**
     * Realiza validação de dados
     */
    private function validate($data){
        $required = ['nome', 'app', 'controller', 'action', 'minuto', 'hora', 'diaMes', 'mes', 'diaSemana'];
        $message  = [
            'diaMes'    => 'Dia do Mês',
            'diaSemana' => 'Dia da Semana'
        ];

        $empty = [];
        foreach($required as $field){
            $value = $data[$field];
            if(empty($value) && $value != '0'){
                if(isset($message[$field])){
                    $empty[] = ucfirst($message[$field]);
                }
                else{
                    $empty[] = ucfirst($field);
                }
            }
        }

        if(count($empty) > 0){
            $this->setError('Os campos a seguir não foram preenchidos: '.implode(', ', $empty));
            $this->sendResponse();
        }
    }

    /**
     * Atualiza
     */
    private function atualizar($id, $data){
        try {
            TarefaAgendadaFactory::update($id, $data);
            $this->refreshSchedule();
            $this->setSuccess('Registro atualizado com sucesso.');
        } 
        catch (\Exception $e) {
            throw new ErrorException("Erro ao atualizar o registro.".$e->getMessage());
        }
    }

    /**
     * Adiciona
     */
    private function add($data){
        try {
            TarefaAgendadaFactory::add($data);
            $this->refreshSchedule();
            $this->setSuccess('Registro adicionado com sucesso.');
        }
        catch (\Exception $e) {
            throw new ErrorException("Erro ao atualizar o registro.".$e->getMessage());
        }
    }

    /**
     * Executa procedimento que atualiza as tarefas na agenda do Sistema Operacional
     */
    public function refreshSchedule(){
        TarefaAgendadaFactory::refreshSchedule();
    }
}