<?php
namespace Geral\Controller;

use \Geral\Entity\WebClient;
use \Geral\Model\Factory\ConfigConnFactory;
use \SGBR\Entity\BoletoService;

class ConfigConnController extends \Geral\Controller\AbstractPrivateController
{
    public function __construct()
    {
        parent::__construct();
        
        if(!$this -> auth -> isAllowedTransactions(['admin'])) {
            LogFactory::error('Usuário sem permissão para acessar este módulo.');
            die(LogFactory::getLastMsg());
        }
    }

    /***************************************************************************************/
    public function index()
    {
        $this -> addVarView('configs', json_encode(ConfigConnFactory::getAll(true), true));
        $this->view();
    }
    
    /*
    public function getConfigConns()
    {
        $configs = [];
        
        $result = [
            'result' => 'success',
            'configs' => ConfigConnFactory::getAll(true)
        ];

        $this->returnJson($result);
    }

    public function getConfig()
    {
        $idConfig = $this -> url -> getSegment(4);

        $result = [
            'result' => 'success',
            'config' => ConfigConnFactory::getById($idConfig, true)
        ];

        $this->returnJson($result);
    }
    */
    /*=== CRUD  ===============================================================*/
    public function saveConfig()
    {
        $idConfig = $_POST['id'];
        
        try {
            if(!empty($idConfig)) {
                $config = ConfigConnFactory::update($idConfig, $_POST['descricao'], $_POST['host'], $_POST['port'], $_POST['type'], $_POST['user'], $_POST['password'], $_POST['rootDir']);
            } else {
                $config = ConfigConnFactory::create($_POST['descricao'], $_POST['host'], $_POST['port'], $_POST['type'], $_POST['user'], $_POST['password'], $_POST['rootDir']);
            }
            
            $result = [
                'result' => 'success',
                'msg' => 'Operação realizada com sucesso.',
                'config' => $config -> toArray()
            ];
        } catch (\Exception $e) {
            $result = [
                'result' => 'error',
                'msg' => $e -> getMessage()
            ];
            
            $this->returnJson($result);
            exit;
        }
        
        $this -> returnJson($result);
    }

    public function removeConfig()
    {
        $idConfig = $this -> url -> getSegment(4);

        $config = ConfigConnFactory::getById($idConfig);

        if(empty($config)) {
            $result = [ 'result' => 'error', 'msg' => 'Não existe uma configuração com o o id "'.$idConfig.'".' ];
        } else {
            try {
                $this->em->remove($config);
                $this->em->flush();
                $result = ['result' => 'success', 'msg' => 'Configuração removida com sucesso.'];
            } catch (\Exception $e) {
                $result = ['result' => 'error', 'msg' => 'Erro ao excluir configuração.'];
            }
        }

        $this->returnJson($result);
    }
}
?>
