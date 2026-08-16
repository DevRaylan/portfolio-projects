<?php

namespace Geral\Controller;

use \Geral\Model\Factory\AuditFactory;
use Geral\Model\Factory\LogFactory;
use \Geral\Model\UserSession;

class AuditController extends AbstractServiceController
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view();
    }

    public function findByParams()
    {
        if (!$this->auth->isAllowedTransactions(['admin'])) {
            LogFactory::error('Usuário sem permissão para acessar este módulo.');
            die(LogFactory::getLastMsg());
        }

        $sessionId = $_POST['cpf'];
        if (!$sessionId) $sessionId = UserSession::getParam('id');
        $dataCriacao = $_POST['data'];
        $tipo = $_POST['tipo'];
        $keywords = $_POST['palavrasChave'];
        $limit = $_POST['limit'];

        if (!$tipo) $tipo = 'S';
        $modulo = $this->url->getNameModule();

        try {
            $logs = AuditFactory::findByParams($sessionId, $dataCriacao, $tipo, $modulo, $keywords, $limit, true);
            $this->setSuccess('Consulta realizada com sucesso.'.$modulo, ['logs' => $logs]);
        } catch (\Exception $e) {
            $this->setError('Erro na consulta.');
        }

        $this->sendResponse();
    }
}
