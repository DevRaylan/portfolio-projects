<?php
namespace Geral\Controller;

use \Geral\Model\Exception\ErrorException;
use Geral\Model\UserSession;
use Geral\Model\UserPreference;
use Geral\Model\Factory\UserDataFactory;
use Geral\Model\UserDataServiceOptions;

class IndexController extends AbstractPrivateController
{
    public function __construct( )
    {
        parent::__construct( );
    }

    public function index( )
    {
        $dashboards = \Geral\Entity\DashBoard::getByIdUser(UserSession::getParam('id'));
        $defaultDashboard = $this -> em -> find('\Geral\Entity\DashBoard', UserSession::getParam('defaultDashBoard'));
        $defaultApp = UserSession::getParam('defaultApp');

        if ($defaultApp == 'Geral') {
            $this -> addVarView('dashboards', $dashboards);
            $this -> addVarView('defaultDashboard', $defaultDashboard);
            $this -> view( $defaultDashboard -> getTemplate(), ['SEM_MENU'] );
        } else {
            $this -> url -> redirect('Index', '', $defaultApp);
        }
    }

    public function pageNotFound( )
    {
        $this -> addVarView('url', $_GET['url']);
        $this -> view( );
    }

    public function fixaMenu( )
    {
        try{
            $userPreference = new UserPreference(UserSession::getParam('cpf'));

            $fixaMenu = ($userPreference->preferenceExists('fixaMenu')) ? $userPreference->getPreference('fixaMenu') : false;
            $mudaMenu = (!$fixaMenu) ? true : false;

            $userPreference->setPreference('fixaMenu',$mudaMenu);
            UserSession::setParam('fixaMenu', $mudaMenu);
        
            $fixaMenu = $userPreference->getPreference('fixaMenu');

            $this->setSuccess('A preferência do menu foi atualizada.',['fixaMenu'=>$fixaMenu, 'preferencias' => $userPreference->getPreferences()]);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
        }
        
        $this->sendResponse();
    }

    public function setDefaultDashBoard( )
    {
        $idDash = (int) $_GET['idDash'];
        $dashboards = \Geral\Entity\DashBoard::getByIdUser(UserSession::getParam('id'));

        foreach($dashboards as $j => $dashboard) {
            $dashboard -> setDefault(false);
        }

        $this -> em -> flush();

        UserSession::setParam('defaultDashBoard', $idDash);
        $this -> url -> redirect();
    }

    public function changeDashBoard( )
    {
        $idDash = (int) $_GET['idDash'];

        $dashboards = \Geral\Entity\DashBoard::getByIdUser(UserSession::getParam('id'));

        foreach($dashboards as $j => $dashboard) {
            $dashboard -> setDefault($dashboard -> getId() == $idDash);
        }

        $this -> em -> flush();

        UserSession::setParam('defaultDashBoard', $idDash);
        $this -> url -> redirect();
    }

    public function populateBase( )
    {
        $admin = new AdminController();
        $_POST['entities'] = ['\Geral\Entity\DashBoard'];
        $admin -> recreateDB();

        $dashboards = [];

        foreach(\Geral\Entity\DashBoard::$defaultDashboards as $i => $defaultDashboard) {
            $dashboards[$i] = \Geral\Entity\DashBoard::create($defaultDashboard['descricao'], $defaultDashboard['template'], $defaultDashboard['conf'], 0 );
            $dashboards[$i] -> setLock(true);
            $GLOBALS['em'] -> persist($dashboards[$i]);
        }

        $GLOBALS['em'] -> flush();

        $this -> url -> redirect('Login', 'sair');
    }

    public function meusDados()
    {
        $userOptions = new UserDataServiceOptions();
        $userOptions->setCarregaFoto(true);

        $dados = UserDataFactory::getByCpf($_SESSION['usuario']['cpf'], null, true, $userOptions);

        $this->addVarView('meusDados', $dados);
        $this->view();
    }

    public function mudarVinculo()
    {        
        $vinculoSelecionado = $this->url->getSegment(4);
        $vinculos = UserDataFactory::getVinculosByCpf(UserSession::getParam('cpf'));

        if(empty($vinculoSelecionado)) {
            $this->setError('Nenhum vínculo informado.');
        } else {
            $vinculoExiste = false;

            foreach($vinculos as $vinculo) {
                if($vinculo['vinculo'] == $vinculoSelecionado) {
                    $vinculo = $vinculo['vinculo'];
                    $vinculoExiste = true;
                    break;
                }
            }

            if($vinculoExiste) {
                $userData = UserDataFactory::getByCpf(UserSession::getParam('cpf'), $vinculoSelecionado, true);
                $userData['vinculos'] = $vinculos;
                $userData['vinculo'] = $vinculo;
                $userData['defaultApp'] = UserSession::getParam('defaultApp');

                UserSession::createFromData($userData);

                $this->setSuccess('Vínculo alterado com sucesso.');
                
            } else {
                $this->setError('Vínculo informado não existe para este usuário.');
            }
        }

        $this->sendResponse();
    }

    public function setDefaultApp()
    {
        $app = $this->url->getSegment(4);

        try {
            $userPreference = new UserPreference(UserSession::getParam('cpf'));
            $userPreference->setPreference('defaultApp', $app);

            UserSession::setParam('defaultApp', $app);
            $this->setSuccess('A aplicação "'.$app.'" foi definida como sua aplicação padrão.');
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
        }

        $this->sendResponse();
    }

    public function grafico1( )
    {
        $this->view('',array('ISOLADO'));
    }

    public function grafico2( )
    {
        $this->view('',array('ISOLADO'));
    }
    
}
