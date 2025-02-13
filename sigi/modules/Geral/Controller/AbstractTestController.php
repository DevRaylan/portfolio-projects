<?php
 /**
 * @class AbstractTestController
 *
 * Esta classe permite que aplicações implementem telas para teste de chamadas AJAX ao backend.
 * Se um controller da aplicação estender esta classe passando as configurações necessárias, será disponibilizada uma
 * tela com os testes a serem realizados.
 * 
 * Esta classe também é uma extensão de AbstractPrivateController, assim é necessário existir uma sessão de usuário
 * para o acesso aos métodos definidos nos controllers da aplicação.
 *
 * @version 1.0
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 */
namespace Geral\Controller;

abstract class AbstractTestController extends \Geral\Controller\AbstractPrivateController
{
    private $testConfig = [];

    // Iniciar as variáveis do controller
    public function __construct( )
    {
        if(!in_array($_SERVER['AMBIENTE'], ['DEV', 'HOM'])) {
            $this->setError('Recurso somente disponível nos ambientes de desenvolvimento e homologação.');
            $this->sendResponse();
        }

        parent::__construct();

        if(!$this->auth->isAllowedTransactions(['desenvolvedor'])) {
            $this->setError('Usuário sem permissão para esta ação.');
            $this->sendResponse();
        }
    }

    /**
     * Configura as requisições ao backend para testes.
     * 
     * @param String $method Método da requisição (GET, POST, etc.).
     * @param Array $config
     */
    protected function setTestConfig($method, $config) {
        $this->testConfig[$method] = $config;
    }

    protected function getTestConfig($method = null) {

        return !empty($method) ? $this->testConfig[$method] : $this->testConfig;
    }

    // TELAS ===================================================================

    public function index( )
    {
        $this->addVarView('controller', $this->url->getNameController());
        
        $this->url->setNameController('Test');
        $this->url->setNameModule('Geral');

        $this->addVarView('testConfig', $this->getTestConfig());
        $this -> view('index');
    }
}
?>
