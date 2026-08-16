<?php
namespace Geral\Model\Factory;

use \Geral\Entity\UserDataCache;
use \Geral\Model\Interfaces\UserDataInterface;

abstract class UserDataCacheFactory
{
    static function getByCpf(String $cpf, $toArray = false)
    {
        $userDataCache = $GLOBALS['em']->getRepository('\Geral\Entity\UserDataCache')
            ->findBy(['cpf' => $cpf]);
        
        if(!empty($userDataCache)) {
            foreach($userDataCache as $dataCache) {
                if($dataCache->isExpired()) {
                    self::remove($userDataCache);
                    $userDataCache = null;
                    break;
                }
            }
        }

        if($toArray && !empty($userDataCache)) {
            self::objectCollectionToArray($userDataCache);
        }

        return $userDataCache;
    }

    public static function getVinculosByCpf($cpf)
    {
        $vinculos = [];

        $vinculosDoUsuario = $GLOBALS['em']->getRepository('\Geral\Entity\UserDataCache')
            ->createQueryBuilder('udc')
            ->where('udc.cpf = ?1')
            ->setParameter(1, $cpf)
            ->getQuery()
            ->getResult();

        return $vinculos;
    }

    // CRUD ====================================================================

    static function add(Array $userData)
    {
        $userDataCache = new UserDataCache();

        foreach(UserDataInterface::ATRIBUTOS_DO_USUARIO as $column) {
            $func = 'set'.ucwords($column);
            $userDataCache->$func($userData[$column]); 
        }

        $GLOBALS['em']->persist($userDataCache);
        $GLOBALS['em']->flush();

        return $userDataCache;
    }

    private static function remove(Array $userDataCache)
    {
        foreach($userDataCache as $dataCache) {
            $GLOBALS['em']->remove($dataCache);
        }

        $GLOBALS['em']->flush();
    }

    // Funções auxiliares ======================================================

    private static function toArray(UserDataCache &$userDataCache)
    {
        $output = [];

        foreach(UserDataInterface::ATRIBUTOS_DO_USUARIO as $column) {
            $func = 'get'.ucwords($column);            
            $output[$column] = $userDataCache->$func();
        }

        $output['dataExpiracao'] = $userDataCache->getDataExpiracao();
        $output['isExpired'] = $userDataCache->isExpired();

        return $output;
    }

    private static function objectCollectionToArray(Array &$userDataCache) {
        foreach($userDataCache as &$userData) {
            $userData = self::toArray($userData);
        }
    }

}
