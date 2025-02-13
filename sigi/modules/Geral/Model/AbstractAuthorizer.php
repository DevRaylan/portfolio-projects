<?php
namespace Geral\Model;

use \Geral\Model\Interfaces\IAuthorizer;
use \Geral\Model\UserSession;

abstract class AbstractAuthorizer implements IAuthorizer {
    protected $userId;
    protected $userName;
    protected $transactionsByUser;
    protected $dataByUser;
    protected $profilesByUser;

    const validAuthSchemes = ['userId', 'webToken'];

    public function __construct(Array $baseLocalOfAuthorization, $authScheme) {
        $this -> userId = $authScheme == 'userId' ? UserSession::getParam('id') : $_SERVER['PHP_AUTH_USER'];
        $this -> userName = $baseLocalOfAuthorization['username'];
        $this -> transactionsByUser = $baseLocalOfAuthorization['transactions'];
        $this -> dataByUser = $baseLocalOfAuthorization['data'];
        $this -> profilesByUser = $baseLocalOfAuthorization['profile'];
    }

    // @overhide
    public function getTransactionsByIdUser($idUser)
    {
        if(!empty($this -> transactionsByUser[$idUser])) {
            return $this -> transactionsByUser[$idUser];
        } else {
            return null;
        }
    }

    public function getDataByIdUser($idUser, $field=null)
    {
        if(!empty($this -> dataByUser[$idUser])) {
            return empty($field) ? $this -> dataByUser[$idUser] : $this -> dataByUser[$idUser][$field];
        } else {
            return null;
        }
    }

    public function getProfilesByIdUser($idUser)
    {
        if(!empty($this -> profilesByUser[$idUser])) {
            return $this -> profilesByUser[$idUser];
        } else {
            return null;
        }
    }

    public function userCanExecuteTransactions(Array $transactions, $idUser)
    {
        if(empty($this -> transactionsByUser[$idUser])) {
            return null;
        }

        foreach ( $this -> transactionsByUser[$idUser] as $transaction ) {
            if ( in_array( $transaction, $transactions ) ) {
                return true;
            }
        }

        return false;
    }

    public function userCanAccessData ($field, $value, $idUser)
    {
        if(empty($this -> dataByUser[$idUser][$field])) {
            return null;
        }

        return  in_array ($value, $this -> dataByUser[$idUser][$field]);
    }

    public function getUsernameByIdUser($idUser)
    {
        if(!empty($this -> userName[$idUser])) {
            return $this -> userName[$idUser];
        } else {
            return null;
        }
    }

    public function isAllowedTransactions(Array $transactions, $idUser = null)
    {
        if ( empty( $idUser ) ) {
            $idUser = $this -> userId;
        }
        if(!empty($this -> transactionsByUser[$idUser])) {
            foreach ( $this -> transactionsByUser[$idUser] as $transaction ) {
                if ( in_array( $transaction, $transactions ) ) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isAllowedData( $field, $value, $idUser = null )
    {
        if ( empty( $idUser ) ) {
            $idUser = $this -> userId;
        }

        return $this -> userCanAccessData($field, $value, $idUser);
    }

    public function getAllowedTransactions ($idUser = null)
    {
        if ( empty( $idUser ) ) {
            $idUser = $this -> userId;
        }

        return $this -> getTransactionsByIdUser($idUser);
    }

    public function getAllowedData ($field, $idUser = null)
    {
        if ( empty( $idUser ) ) {
            $idUser = $this -> userId;
        }

        return $this -> getDataByIdUser($idUser, $field);
    }

    public function getProfiles ($idUser = null)
    {
        if ( empty( $idUser ) ) {
            $idUser = $this -> userId;
        }

        return $this -> getProfilesByIdUser($idUser);
    }

    public function getUserName ($idUser = null)
    {
        if ( empty( $idUser ) ) {
            $idUser = $this -> userId;
        }

        return $this -> getUsernameByIdUser($idUser);
    }
}
