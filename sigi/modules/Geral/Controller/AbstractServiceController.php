<?php
namespace Geral\Controller;
use \Geral\Model\Factory\AuditFactory;
use Geral\Model\Factory\LogFactory;
use \Geral\Model\Config;
use \Geral\Model\Validacao;
use \Geral\Entity\LocalAuthorizer;

abstract class AbstractServiceController extends \Geral\Controller\AbstractPrivateController
{

    public function __construct()
    {
        parent::__construct();

        if(!$this->auth->isAllowedTransactions(['desenvolvedor', 'adminUsers'])) {
            $this->setError('Usuário sem permissão para esta ação.');
            $this->sendResponse();
        }
    }

    public function index() 
    {

    } 
    
    /*** Auditoria / Logs da Aplicação */

    public function audit()
    {
        // Carrega sempre a view do módulo geral, independente do módulo/aplicação que estiver
        $logs = $this->findAuditByParams(true);
        //var_dump($logs);
        $this->addVarView('logs', $logs);
        $this->view('viewLogs', false, 'Geral', 'Admin');
    }    

    public function findAuditByParams($toArray = false)
    {
        if (!$this->auth->isAllowedTransactions(['admin'])) {
            LogFactory::error('Usuário sem permissão para acessar este módulo.');
            die(LogFactory::getLastMsg());
        }

        $sessionId = $_POST['cpf'];
        $type = $this->url->getSegment(4);
        $limit = $this->url->getSegment(5);
        if ($this->url->getSegment(6) && $this->url->getSegment(7) && $this->url->getSegment(8)) {
            $dataCriacao = $this->url->getSegment(8) .'-'. $this->url->getSegment(7) .'-'. $this->url->getSegment(6);
        } else {
            $dataCriacao = null;
        }
        $modulo = $this->url->getNameModule();

        try {
            $logs = AuditFactory::findByParams($sessionId, $dataCriacao, $type, $modulo, null, $limit, true);
            if ($toArray) {
                return $logs;
            } else {
                $this->setSuccess('Consulta realizada com sucesso.'.'  Params:'.$sessionId.'|'.$dataCriacao.'|'.$type.'|'.$modulo, ['logs' => $logs]);
            }
        } catch (\Exception $e) {
            if ($toArray) {
                return false;
            } else {            
                $this->setError('Erro na consulta.');
            }
        }

        if (!$toArray) $this->sendResponse();
    }  


    /*** Gerenciamento de Arquivos */

    public function files()
    {
        // Carrega sempre a view do módulo geral, independente do módulo/aplicação que estiver
        $this->view('files', false, 'Geral', 'Service');
    }

    public function getFileList()
    {
        $dir = DIR_ROOT . '/' . DIR_NAME_FILES . '/' . $this->url->getNameModule() . '/' . DIR_NAME_TEMP;
        
        // Pegar os arquivos
        $files = scandir($dir);
        
        // Remover "." e ".."
        array_shift($files);
        array_shift($files);

        $arquivos = array();
        foreach ($files as $file) {
            $isFolder = is_dir($dir.'/'.$file);
            $arquivos [] = array("name" => $file, "isFolder" => $isFolder);
        }
   
        $this->setSuccess("Listagem dos arquivos do folder '$dir' com sucessso.",['files' => $arquivos]);
        $this->sendResponse();
    }

    public function viewDownloads( )
    {
        $downloads = $this->getFileRepository()->getDownloads(true);

        $this->addVarView('downloads', $downloads);
        $this->view();
    }

    /*** Permissões de usuários */

    public function usuarios()
    {
        if (!$this->auth->isAllowedTransactions(['adminUsers'])) {
            LogFactory::error('Usuário sem permissão gerenciar usuários do sistema.');
            $this->returnJson(['result' => 'error', 'msg' => LogFactory::getLastMsg()]);
            exit;
        }

        $transactionsByUser = LocalAuthorizer::getAll(true);
        $this->addVarView('transactionsByUser', json_encode($transactionsByUser, true));
        
        $cpfsToName = [];
        foreach ($transactionsByUser as $cpf => $app) {
            try {
                $user = \Geral\Model\Factory\UserDataFactory::getByCpf($cpf);
                $cpfsToName[$cpf] = $user->getNome();
            } catch (\Exception $e) {
                $cpfsToName[$cpf] = $e->getMessage();
            }
        }

        $this->addVarView('cpfsToName', json_encode($cpfsToName, true));

        unset($transactionsByUser, $cpfsToName);

        $transactionsByApp = Config::getAllConfigs(['transactions'],$this->url->getNameModule());
        $apps = array_keys($transactionsByApp);

        $this->addVarView('transactionsByApp', $transactionsByApp, true);
        $this->addVarView('app', $this->url->getNameModule());

        // Carrega sempre a view do módulo geral, independente do módulo/aplicação que estiver
        $this->view('formListAuthorizations', false, 'Geral', 'LocalAuthorizer');
    }

    public function saveTransactionsByCpf()
    {
        if (!$this->auth->isAllowedTransactions(['adminUsers'])) {
            LogFactory::error('Usuário sem permissão gerenciar usuários do sistema.');
            $this->returnJson(['result' => 'error', 'msg' => LogFactory::getLastMsg()]);
            exit;
        }

        $cpfs = explode(',', $_POST['cpf']);
        $transactionsByApp = is_array($_POST['transactionsByApp']) ? $_POST['transactionsByApp'] : [];

        // garante que irá alterar apenas transações do módulo corrente
        // caso seja passado transações de outro módulo (ex: Geral), retira do array
        $apps = array_keys($transactionsByApp);
        foreach ($apps as $key) {
            if ($key != $this->url->getNameModule()) {
                unset($transactionsByApp[$key]);
            }
        }

        try {
            foreach ($cpfs as $cpf) {
                Validacao::cpfIsValid($cpf);
                LocalAuthorizer::saveTransactionsByCpf($cpf, $transactionsByApp, $this->url->getNameModule());
                $transactions[$cpf] = LocalAuthorizer::getByCpf($cpf, true);
            }
            $this->setSuccess('Transações salvas com sucesso.',['transactions' => $transactions]);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
        }
        
        $this->sendResponse();
    }

    /*** Próximo bloco */
}
?>