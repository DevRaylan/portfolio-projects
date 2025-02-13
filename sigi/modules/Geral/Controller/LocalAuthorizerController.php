<?php

/**
 * Arquivo que contém a classe AdminController
 * @file AdminController.php
 */

/**
 * @class AdminController
 *
 * Esta classe possui os métodos e atributos relacionados a administração do framework
 *
 * @version 1.0
 * @author Jean Carlos Oliveira de Abreu
 <jean.abreu@udesc.br>
 */

namespace Geral\Controller;

use \Geral\Entity\LocalAuthorizer;
use \Geral\Model\Config;
use \Geral\Model\Validacao;
use \Geral\Model\Factory\LogFactory;

class LocalAuthorizerController extends AbstractPrivateController
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->auth->isAllowedTransactions(['admin','adminUsers'])) {
            LogFactory::error('Usuário sem permissão para acessar este módulo.');
            die(LogFactory::getLastMsg());
        }
    }

    public function index()
    {
        if (!$this->auth->isAllowedTransactions(['admin','adminUsers'])) {
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

        $transactionsByApp = Config::getAllConfigs(['transactions']);
        $apps = array_keys($transactionsByApp);

        $this->addVarView('transactionsByApp', $transactionsByApp, true);

        $this->view('formListAuthorizations');
    }

    public function saveTransactionsByCpf()
    {
        $cpfs = explode(',', $_POST['cpf']);
        $transactionsByApp = is_array($_POST['transactionsByApp']) ? $_POST['transactionsByApp'] : [];

        try {
            foreach ($cpfs as $cpf) {
                Validacao::cpfIsValid($cpf);
                LocalAuthorizer::saveTransactionsByCpf($cpf, $transactionsByApp);
                $transactions[$cpf] = LocalAuthorizer::getByCpf($cpf, true);
            }
            $this->setSuccess('Transações salvas com sucesso.',['transactions' => $transactions]);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
        }
        $this->sendResponse();
    }
}
