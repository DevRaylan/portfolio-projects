<?php
namespace Geral\Controller;

use \Geral\Model\Session;
use \Geral\Model\UserSession;
use \Geral\Entity\DashBoard;
use \Geral\Model\Login;
use \Geral\Model\UdevUpdate;

class LoginController extends AbstractPublicController
{
    public function __construct( )
    {
        parent::__construct( );
    }

    public function index( )
    {
        $this -> url -> redirect( 'Login', 'formLogin' );
    }

    public function formLogin( )
    {
        // Se DEV ou HOM, redirecionar para a tela de sincronização caso pendente.
        if(in_array($_SERVER['AMBIENTE'], ['DEV', 'HOM'])) {

            /*
            * Pega o dashboard do usuário.
            * Se for lançado um erro de tabala inexistente, então o framework não está instalado. 
            */
            try {
                $dashboard = \Geral\Entity\DashBoard::getDefaultFromUser(null);
            } catch (\Exception $e) {
                $GLOBALS['url'] -> redirect('Install');
            }
            
            $udevUpdate = new UdevUpdate();

            if(!$udevUpdate->isSynchronized() && BD_DRIVER != 'pdo_sqlite') {
                $this -> url -> redirect( 'Update', 'index' );
            }
        }

        if(!UserSession::isEmpty()) {
            $this -> url -> redirect( 'Login', 'sair' );
        }

        if(Session::paramExists('msg')) {
            $this->addVarView('msg', Session::getParam('msg'));
            Session::removeParam('msg');
        }

        $this->addVarView('urlRetorno', $_GET['urlRetorno']);
        $this -> view( 'formLogin', array('SEM_MENU','SEM_TOPO') );
    }
    
    public function autenticar( )
    {
        // Verificar se o tipo de login está permitido para o ambiente da aplicação.
        try {
            // Inicializa as classes necessárias para login
            $login = new Login();
        } catch (\Exception $e) {
            Session::setParam('msg', $e->getMessage());
            $this->url->redirect('formLogin');
        }

        $id = isset( $_POST['id'] ) ? str_replace(['.','-'],'',$_POST['id']) : null;

        /*
         * Pega o dashboard do usuário.
         * Se for lançado um erro de tabala inexistente, então o framework não está instalado. 
         */
        try {
            $dashboard = \Geral\Entity\DashBoard::getDefaultFromUser($id);
        } catch (\Exception $e) {
            $GLOBALS['url'] -> redirect('Install');
        }

        $password = isset( $_POST['password'] ) ? $_POST['password'] : null;

        if (isset($_POST['urlRetorno'])) { 
            $urlRetornoPartes = explode("/", $_POST['urlRetorno']);
        }

       /**
         * Se o TIPO_LOGIN for "True", então a simulação de dados é obrigatória.
         * Se outra opção, então no login terá opção de ativar ou não este recurso.
         */
        Session::setParam('simularDados', TIPO_LOGIN == 'True' || !empty($_POST['simularDados']));
        
        // Este é o único controller público que deve acessar dados de usuário sem utilizar simulação de dados.
        // Ver \Geral\Model\Services\UserDataProxyService.php para mais informações.
        // Como o AbstractPublicController define esta variável, então deve-se excluir este parâmetro aqui.
        Session::removeParam('simularDadosRequerido');
        
        try {
            if ( $login -> autenticar( $id, $password ) ) {
                $login -> iniciarSessao();
                
                // Se ainda não existir um dashboard pro usuário, então cria.
                if(empty($dashboard)) {
                    $dashboard = DashBoard::createDefaultDashBoardToUser(UserSession::getParam('cpf'), UserSession::getParam('vinculo'));
                    $GLOBALS['em'] -> persist($dashboard);
                    $GLOBALS['em'] -> flush();
                }
                
                // Salva em sessão o ID do dashboard padrão do usuário.
                UserSession::setParam('defaultDashBoard', $dashboard -> getId());
            } else {
                Session::setParam('msg', 'Usuário ou senha inválido.');
            }
        } catch(\Exception $e) {
            Session::setParam('msg', $e->getMessage());
        }

        if (isset($urlRetornoPartes)) {
            $this -> url -> redirect( $urlRetornoPartes[1], $urlRetornoPartes[2], $urlRetornoPartes[0] );
          } else {
            $this -> url -> redirect( );
         }
    }

    public function sair( )
    {
        $login = new Login();
        $msgTemp = Session::paramExists('msg') ? Session::getParam('msg') : null;
        $login -> encerrarSessao( );

        if(!empty($msgTemp)) {
            session_start();
            Session::setParam('msg', $msgTemp);
        }
        
        $this -> url -> redirect('Login', 'formLogin');
    }
}
