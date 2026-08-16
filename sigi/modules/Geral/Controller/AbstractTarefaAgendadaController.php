<?php
namespace Geral\Controller;

use \Geral\Model\Factory\LogFactory;

 /**
 * Esta classe implementa a verificação de um token enviado as tarefas enviadas
 *
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 **/
abstract class AbstractTarefaAgendadaController extends AbstractController
{
    /**
     * @var \Geral\Entity\TarefaAgendada
     */
    protected $tarefaAgendada;

    // Iniciar as variáveis do controller
    public final function __construct(){
        parent::__construct();

        try {
            $this->checkToken();
        } 
        catch(\Exception $e) {
            LogFactory::error($e -> getMessage());
            $this->errorAutenticacao();
            exit;
        }

        $this->afterConstruct();
    }

    /**
     * Método de sobreescrita do construtor para classes filhas
     * @return void
     */
    protected function afterConstruct() { }

    public function index(){
        $this->setError('Método indisponível');
        $this->sendResponse();
    }

    /**
     * Verifica o token repassado
     * @return void
     */
    protected function checkToken(){
        $app        = $this->url->getNameModule();
        $controller = $this->url->getNameController();
        $action     = $this->url->getNameAction();
        $token      = $this->request->post('token', '');
        if(empty($app) || empty($controller) || empty($action) || empty($token)){
            //Se algum parâmetro estiver em branco
            $this->errorAutenticacao();
        }

        $this->tarefaAgendada = \Geral\Model\Factory\TarefaAgendadaFactory::getByAcessData($app, $controller, $action, $token);
        if(!$this->tarefaAgendada || is_null($this->tarefaAgendada)){
            //Se a tarefa não foi encontrada
            $this->errorAutenticacao();
        }
    }

    /**
     * Retorna o erro de autenticação no formato JSON (padrão)
     * @return void
     */
    protected function errorAutenticacao(){
        $this->setError('Erro na autenticação do sistema, verifique se os dados de acesso estão corretos.');
        $this->sendResponse();
    }
}