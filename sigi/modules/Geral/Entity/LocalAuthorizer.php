<?php
namespace Geral\Entity;
use \Geral\Model\Date;
/**
 * @class LocalAuthorizer
 * Uma autorização local é composta pelo conjunto {cpf, aplicação e nome da transação}. Ex: {111.111.111-11, SGBR, visualizar}.
 *
 * @version 1.0
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 */

 /**
 * @Entity
 * @Table(name="local_authorizer")
 **/

class LocalAuthorizer
{
    /** @Id @Column(type="string", length=11, nullable=false) **/
    private $cpf;

    /** @Id @Column(type="string", length=40, nullable=false) **/
    private $application;

    /** @Id @Column(name="`transaction`", type="string", length=255, nullable=false) **/
    private $transaction;

    /** @Column(name="dt_cadastro", type="datetime", nullable=false) **/
    private $dtCadastro;

// ====================================================================================================================

    public function __construct( ) {
        $this -> dtCadastro = new \DateTime();
    }

    public function setCpf($cpf) {
        $this -> cpf = $cpf;
    }

    public function getcpf() {
        return $this -> cpf;
    }

    public function setApplication($application) {
        $this -> application = $application;
    }

    public function getApplication() {
        return $this -> application;
    }

    public function setTransaction($transaction) {
        $this -> transaction = $transaction;
    }

    public function getTransaction() {
        return $this -> transaction;
    }

    public function getDtCadastro($string = false) {
        return $string ? Date::convDatetimeToString($this -> dtCadastro, Date::DATAHORASEMSEGUNDOS_FORMAT) : $this -> dtCadastro;
    }

// CRUD ========================================================================

    public static function saveTransactionsByCpf($cpf, $transactionsByApp, $apponly = null)
    {
        if (isset($apponly)) {
            $localAuthorizers = $GLOBALS['em'] -> getRepository('\Geral\Entity\LocalAuthorizer') -> findBy(['cpf' => $cpf, 'application' => $apponly]);
        } else {
            $localAuthorizers = $GLOBALS['em'] -> getRepository('\Geral\Entity\LocalAuthorizer') -> findBy(['cpf' => $cpf]);
        }

        foreach($localAuthorizers as $localAuthorizer) {
            $GLOBALS['em'] -> remove($localAuthorizer);
        }

        try {
            $GLOBALS['em'] -> flush();
        } catch(\Exception $e) {
            throw new \Exception("Não foi possível remover transações do Banco de Dados.");
        }

        foreach($transactionsByApp as $app => $transactions) {
            foreach($transactions as $transaction) {
                $newTransaction = new LocalAuthorizer;
                $newTransaction -> setCpf($cpf);
                $newTransaction -> setApplication($app);
                $newTransaction -> setTransaction($transaction);

                $GLOBALS['em'] -> persist($newTransaction);
            }
        }

        try {
            $GLOBALS['em'] -> flush();
            return true;
        } catch(\Exception $e) {
            throw new \Exception("Não foi possível salvar transações no Banco de Dados.");
        }
    }

    public static function removeTransactionsByCpf($cpf)
    {
        $localAuthorizers = $GLOBALS['em'] -> getRepository('\Geral\Entity\LocalAuthorizer') -> findBy(['cpf' => $cpf]);

        if(empty($transactions)) {
            throw new \Exception("Nenhuma transação encontrada para o usuário.");
        }

        foreach($transactions as $transaction) {
            $GLOBALS['em'] -> remove($transaction);
        }

        try {
            $GLOBALS['em'] -> flush();
            return true;
        } catch(\Exception $e) {
            throw new \Exception("Não foi possível remover as transações do Banco de Dados.");
        }
    }

// Factory =====================================================================

    public static function getByCpf($cpf, $toArray = false)
    {
        if(empty($cpf)) return [];

        $transactionsByApp = $GLOBALS['em'] -> getRepository('\Geral\Entity\LocalAuthorizer') -> findBy(['cpf' => $cpf], ['application' => 'asc']);

        if($toArray) {
            $transactionsByAppArray = [];

            foreach($transactionsByApp as $transaction) {

                $transactionsByAppArray[$transaction -> getApplication()][$transaction -> getTransaction()] = $transaction -> toArray();
            }

            $transactionsByApp = &$transactionsByAppArray;
        }

        return $transactionsByApp;
    }

    public static function getByApp($app, $toArray = false)
    {
        if(empty($app)) return [];

        $transactionsByApp = $GLOBALS['em'] -> getRepository('\Geral\Entity\LocalAuthorizer') -> findBy(['application' => $app], ['cpf' => 'asc']);

        if($toArray) {
            $transactionsByAppArray = [];

            foreach($transactionsByApp as $transaction) {
                $transactionsByAppArray[$transaction -> getApplication()][$transaction -> getTransaction()] = $transaction -> toArray();
            }

            $transactionsByApp = &$transactionsByAppArray;
        }

        return $transactionsByApp;
    }
    
    public static function getByAppAndCpf($app, $cpf, $toArray = false)
    {
        if(empty($app) || empty($cpf)) return [];
        
        $transactionsByApp = $GLOBALS['em'] -> getRepository('\Geral\Entity\LocalAuthorizer') -> findBy(['application' => $app, 'cpf' => $cpf], ['application' => 'asc', 'cpf' => 'asc']);
        
        if($toArray) {
            $transactionsByAppArray = [];

            foreach($transactionsByApp as $transaction) {
                $transactionsByAppArray[$transaction -> getApplication()][$transaction -> getTransaction()] = $transaction -> toArray();
            }

            $transactionsByApp = &$transactionsByAppArray;
        }

        return $transactionsByApp;
    }

    public static function getAll($toArray = false)
    {
        $userAuthorizersByApp = $GLOBALS['em'] -> getRepository('\Geral\Entity\LocalAuthorizer') -> findBy([], ['cpf' => 'ASC', 'application' => 'ASC']);

        if($toArray) {
            $userAuthorizersByAppArray = [];

            foreach($userAuthorizersByApp as $_userAuthorizersByApp) {
                $userAuthorizersByAppArray[$_userAuthorizersByApp -> getCpf()][$_userAuthorizersByApp -> getApplication()][$_userAuthorizersByApp -> getTransaction()] = $_userAuthorizersByApp -> toArray();
            }

            $userAuthorizersByApp = &$userAuthorizersByAppArray;
        }

        return $userAuthorizersByApp;
    }


// =============================================================================

    public function toArray() {
        return [
            'cpf' => $this -> getCpf(),
            'application' => $this -> getApplication(),
            'transaction' => $this -> getTransaction()
        ];
    }

}
