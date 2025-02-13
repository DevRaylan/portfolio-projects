<?php
namespace Geral\Model;
 
use \Geral\Model\AbstractAuthorizer;
use \Geral\Model\Config;
use \Geral\Entity\LocalAuthorizer;
use \Geral\Model\UserSession;
 
class AuthorizerLocal extends AbstractAuthorizer {
    public function __construct($authScheme = 'userId')
    {
        if (! ( $authScheme != null && is_string($authScheme) && in_array($authScheme, parent::validAuthSchemes) ) ) {
            die('AuthorizerWebServer: Esquema de autenticação inválido.');
        }
        
        // TODO analisar como autorizar uma requisição usando um token (usuário-máquina)
        // Pegar transações da aplicação por cpf
        $transactionsByApp = LocalAuthorizer::getByAppAndCpf($GLOBALS['url'] -> getNameModule(), UserSession::getParam('cpf'), true);
        
        if(empty($transactionsByApp)) {
            $transactionsByApp = [];
        } else {
            $transactionsByApp = current($transactionsByApp);
        }
        
        $baseAuthorizer = [
            'userId' => UserSession::getParam('cpf'),
            'transactions' => $this -> convToArrayByCpf($transactionsByApp),
            'data' => [],
            'profile' => [],
            'username' => [UserSession::getParam('nome')]
        ];
        
        parent::__construct($baseAuthorizer, $authScheme);
    }
    
    private function &convToArrayByCpf(&$transactionsByApp)
    {
        $transactions = [];
        
        foreach($transactionsByApp as $transaction => &$data) {
            $transactions[$data['cpf']][] = $transaction;
        }
        
        return $transactions;
    }
}