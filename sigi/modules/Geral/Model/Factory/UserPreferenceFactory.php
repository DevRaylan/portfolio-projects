<?php

namespace Geral\Model\Factory;

use \Geral\Entity\UserPreference;

abstract class UserPreferenceFactory
{
    // Métodos de consulta =====================================================

    static function getAllByCpf(String $app, String $cpf, $toArray = false)
    {
        $userPreferences = $GLOBALS['em']->getRepository('\Geral\Entity\UserPreference')
            ->createQueryBuilder('up')
            ->where('up.cpf = ?1')
            ->andWhere('up.app = ?2')
            ->setParameter(1, $cpf)
            ->setParameter(2, $app)
            ->getQuery();

        return $toArray ? $userPreferences->getArrayResult() : $userPreferences->getResult();
    }

    static function getPreferencesByCpf(String $app, String $cpf, String $preference, Bool $toArray = false)
    {
        $userPreferences = $GLOBALS['em']->getRepository('\Geral\Entity\UserPreference')
            ->createQueryBuilder('up')
            ->where('up.cpf = ?1')
            ->andWhere('up.app = ?2')
            ->andWhere('up.preference = ?3')
            ->setParameter(1, $cpf)
            ->setParameter(2, $app)
            ->setParameter(3, $preference)
            ->getQuery();

        return $toArray ? $userPreferences->getArrayResult() : $userPreferences->getResult();
    }

    // Métodos de persistência =================================================

    static function add(String $app, String $cpf, String $preference, $value)
    {
        $value = (String) $value;

        $userPreference = new UserPreference;
        $userPreference->setCpf($cpf);
        $userPreference->setPreference($preference);
        $userPreference->setValue($value);
        $userPreference->setApp($app);

        $GLOBALS['em']->persist($userPreference);
        $GLOBALS['em']->flush();
    }

    static function update(String $app, String $cpf, String $preference, $values)
    {
        self::removeAllPreferenceValues($app, $cpf, $preference);

        if(!is_array($values)) {
            $values = [$values];
        }

        foreach($values as $value) {
            self::add($app, $cpf, $preference, $value);
        }
    }

    static function removeAllPreferenceValues(String $app, String $cpf, String $preference)
    {
        $userPreferences = self::getPreferencesByCpf($app, $cpf, $preference);
        
        foreach($userPreferences as $preference) {
            $GLOBALS['em']->remove($preference);
        }

        $GLOBALS['em']->flush();
    }

    static function removePreferenceValue(String $app, String $cpf, String $preference, String $value)
    {        
        $userPreferences = self::getPreferencesByCpf($app, $cpf, $preference);
        
        foreach($userPreferences as $preference) {
            if($preference->getValue() == $value) {
                $GLOBALS['em']->remove($preference);
            }
        }

        $GLOBALS['em']->flush();
    }
}
