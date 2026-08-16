<?php
namespace Geral\Model;

use \Geral\Model\UserSession;
use \Geral\Model\Exception\ErrorException;
use \Geral\Model\Interfaces\LoginInterface;
use \Geral\Model\Factory\UserDataFactory;
use \Geral\Model\UserPreference;
use \Geral\Model\Services\UserDataSimulatorService;

class Login
{
    // Mysq, Ldap, etc.
    private $tipoDeLogin;
    private $id;

    public function __construct( )
    {
        // Utilizar login "True" apenas no ambiente DEV.
        $this->checkTypeRestrictionEnvironment();

        // Nome da classe correspondente ao tipo de login
        $classLogin = '\Geral\Model\Login' . TIPO_LOGIN;

        if ( class_exists( $classLogin ) ) {
            // Instancia a classe com nome igual ao valor de $classLogin
            $this -> tipoDeLogin = new $classLogin;
        } else {
            die( 'Nenhum tipo de login selecionado.' );
        }

        // Verificar se a classe
        if ( !$this -> tipoDeLogin instanceof LoginInterface ) {
            // atende ao contrato(interface)
            die( 'Forma de login não está implementando a interface LoginInterface.' );
        }
    }

    public function autenticar( $id, $password )
    {
        $this->id = $id;

        // Delegar login para a classe instanciada e retornar sua resposta
        if($this -> tipoDeLogin -> autenticar( $id, $password )) {
            if(Session::getParam('simularDados')) {
                $this->checkUserData($id);
            }

            return true;
        } else {
            return false;
        }
    }

    public function iniciarSessao()
    {
        try {
            $userOptions = new UserDataServiceOptions();
            $userOptions->setCarregaFoto(true);

            $userData = UserDataFactory::getByCpf($this->id, null, true, $userOptions);
        } catch(\Exception $e) {
            throw new ErrorException($e->getMessage());
        }

        $vinculos = UserDataFactory::getVinculosByCpf($this->id);
        $vinculoPadrao = '';

        foreach($vinculos as &$vinculo) {
            if($vinculo['isPrincipal'] === true) {
                $vinculoPadrao = $vinculo['vinculo'];
                break;
            }
        }

        if(empty($vinculoPadrao)) {
            throw new ErrorException('Nenhum vinculo padrão foi encontrado para o usuário.');
        }

        $userData['id'] = $this->id;
        $userData['vinculos'] = $vinculos;
        $userData['vinculo'] = $vinculoPadrao;
        
        // Definir a aplicação padrão.
        $userPreference = new UserPreference($userData['cpf']);
        $defaultApp = $userPreference->getPreference('defaultApp');

        $userData['defaultApp'] = !empty($defaultApp) ? $defaultApp : DEFAULT_APP;

        UserSession::createFromData($userData);
    }

    public function encerrarSessao( )
    {
        session_destroy( );
    }

    // Verificar se o tipo de login é permitido no ambiente da aplicação.
    private function checkTypeRestrictionEnvironment()
    {
        // Utilizar login "True" apenas no ambiente DEV.
        if($_SERVER['AMBIENTE'] !== 'DEV' && TIPO_LOGIN == 'True') {
            throw new ErrorException('O tipo de login está configurado com o valor <strong>True</strong>, o qual é permitido apenas no ambiente de <strong>Desenvolvimento</strong>.');
        }
    }

    public function checkUserData($id)
    {
        try{
            UserDataSimulatorService::addDadosSimulados($id);
        } catch (\Exception $e) {
            throw new ErrorException('Erro ao adicionar conta padrão: '.$e->getMessage());
        }
    }
}
