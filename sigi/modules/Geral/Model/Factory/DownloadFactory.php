<?php
namespace Geral\Model\Factory;

use \Geral\Entity\Download;

/**
 * Criação, gravação e consulta de arquivos para downloads do framework.
 */

abstract class DownloadFactory
{
    public static function add($name, $application, $url, $access, $sessionId)
    {
        try {
            $download = new Download();
            $download->setName($name);
            $download->setApplication($application);
            $download->setUrl($url);
            $download->setAccess($access);
            $download->setSessionId($sessionId);
            $download->setPlataform($_SERVER['HTTP_USER_AGENT']);
            $download->setIp($_SERVER['HTTP_X_REAL_IP']);
            

            $GLOBALS['em']->persist($download);
            $GLOBALS['em']->flush();
        } catch (\Exception $e) {
            throw $e;
        }
        
        return $usuario;
    }

    public static function remove($fileName)
    {
        $download = self::getByName($fileName);

        if(!empty($download)) {
            try {
                $GLOBALS['em']->remove($download);
                $GLOBALS['em']->flush();
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return true;
    }

    public static function getAll($toArray = false)
    {
        $qb = $GLOBALS['em']->createQueryBuilder()
            ->select('d')
            ->from('\Geral\Entity\Download', 'd')
            ->getQuery();
        
        return $toArray ? $qb->getArrayResult() : $qb->getResult();
    }

    public static function getByName($fileName, $toArray = false)
    {
        $qb = $GLOBALS['em']->createQueryBuilder()
            ->select('d')
            ->from('\Geral\Entity\Download', 'd')
            ->where('d.name = ?1')
            ->setParameter('1', $fileName)
            ->getQuery();

        
        try {
            return $toArray ? $qb->getArrayResult() : $qb->getSingleResult();
        } catch(\Doctrine\ORM\NoResultException $e) {
            return $toArray ? [] : null;
        }
    }

    public static function getByApp($appName, $toArray = false)
    {
        $qb = $GLOBALS['em']->createQueryBuilder()
            ->select('d')
            ->from('\Geral\Entity\Download', 'd')
            ->where('d.application = ?1')
            ->setParameter('1', $appName)
            ->getQuery();

        $downloads = [];

        try {
            $downloads = $toArray ? $qb->getArrayResult() : $qb->getResult();
        } catch(\Doctrine\ORM\NoResultException $e) {
            // Mantém $downloads como Array vazio.
        }
        
        return $downloads;
    }

    public static function fileExists($fileName)
    {
        return !empty(self::getByName($fileName));
    }
}
