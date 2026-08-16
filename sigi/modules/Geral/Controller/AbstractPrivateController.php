<?php
 /**
 * @class AbstractPrivateController
 *
 * Esta classe implementa a verificação da existência de sessão do usuário (logado). Os controllers especializados a
 * partir desta somente poderão ter seus métodos acessados se o usuário estiver logado.
 *
 * @version 2.0
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 **/
namespace Geral\Controller;

use \Geral\Model\Factory\AuthorizerFactory;
use \Geral\Model\Factory\LogFactory;
use \Geral\Model\UserSession;

abstract class AbstractPrivateController extends AbstractController
{
    // Armazena a instância do Autorizador de acesso.
    protected $auth;

    // Iniciar as variáveis do controller
    public function __construct( )
    {
        parent::__construct();

        // Se não existir a sessão de usuário, então redireciona para o login.
        if(!$this -> isAutenticado()) $this -> url -> redirect('Login', 'formLogin', 'Geral', array("urlRetorno" => $this->url->getNameModule().'/'.$this->url->getNameController().'/'.$this->url->getNameAction()));

        try {
            // Instancia o autorizador.
            $this -> auth = AuthorizerFactory::create();
        } catch (\Exception $e) {
            LogFactory::error($e -> getMessage());
            die(LogFactory::getLastMsg());
        }
        
    }

    // Verificar se o usuário está autenticado.
    private function isAutenticado()
    {
        return UserSession::paramExists('logado');
    }
}
?>
