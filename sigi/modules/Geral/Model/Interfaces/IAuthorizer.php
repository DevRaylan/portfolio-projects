<?php
namespace Geral\Model\Interfaces;

interface IAuthorizer {
    public function getTransactionsByIdUser($idUser);
    public function getDataByIdUser($idUser);
    public function isAllowedTransactions(Array $transactions, $idUser = null);
    public function isAllowedData( $field, $value, $idUser = null );
    public function getAllowedTransactions ($idUser = null);
    public function getAllowedData ($field, $idUser = null);
    public function getProfiles ($idUser = null);
    public function getUserName ($idUser = null);
}
