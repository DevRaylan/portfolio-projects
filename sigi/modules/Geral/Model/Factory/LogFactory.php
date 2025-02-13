<?php

namespace Geral\Model\Factory;

use \Geral\Entity\Log;
use \Geral\Model\UserSession;

/*
 * LogFactory.php
 * Criação, gravação e consulta de eventos de Log.
 *
 */

abstract class LogFactory extends \Geral\Model\Factory\AbstractFactory
{
    const TYPE_SUCCESS = 'S'; // success
    const TYPE_ERROR = 'E'; // error
    const TYPE_WARNING = 'W'; // warning

    static private $msgs = [];

    static public function getLastMsg()
    {
        $msg = '';

        if (!empty(self::$msgs)) {
            $msg = end(self::$msgs);
            $msg = $msg[2];
        }

        return $msg;
    }

    static public function create($sessionId, $url, $type, $msg, $detail)
    {
        try {
            $log = new Log;
            $log->setUrl($_SERVER['REQUEST_METHOD'] . ' ' . $url);
            $log->setType($type);
            $log->setMsg($msg);
            $log->setDetail($detail);
            $log->setSessionId($sessionId);
            $log->setPlataform($_SERVER['HTTP_USER_AGENT']);
            $log->setIp($_SERVER['HTTP_X_REAL_IP']); // REMOTE_ADDR sempre é o ip do Servidor de Cache

            return $log;
        } catch (\Exception $e) {
            throw new \Exception("Erro ao criar o objeto Log.");
        }
    }

    static public function add($log)
    {
        if (!$log instanceof Log) {
            throw new \Exception("Log não é um objeto.");
        }

        parent::add($log);
    }

    static public function success($msg, $detail = null)
    {
        self::$msgs[] = [$GLOBALS['url']->getUrlAction(), self::TYPE_SUCCESS, $msg, $detail];
    }

    static public function error($msg, $detail = null)
    {
        self::$msgs[] = [$GLOBALS['url']->getUrlAction(), self::TYPE_ERROR, $msg, $detail];
    }

    static function save($controller)
    {
        if (is_subclass_of($controller, '\Geral\Controller\AbstractPublicController')) {
            $sessionId = '';
        } elseif (is_subclass_of($controller, '\Geral\Controller\AbstractPrivateController')) {
            $sessionId = UserSession::getParam('id');
        } elseif (is_subclass_of($controller, '\Geral\Controller\AbstractWSController')) {
            $sessionId = $controller->getToken();
        } else {
            die('LogFactory:: Tipo de controlador não encontrado.');
        }

        foreach (self::$msgs as $msg) {
            $log = self::create($sessionId, $msg[0], $msg[1], $msg[2], $msg[3]);
            self::add($log);
        }
    }

    static function findAll($limit = null)
    {
        // verifica se limite de registros foi informado, se nao usa o default
        $limit = empty($limit) ? LIMIT_LOG_AUDIT : $limit;

        return $GLOBALS['em']->getRepository('\Geral\Entity\Log')->findBy([], ['dtCriacao' => 'desc'], $limit);
    }

    static public function findByType($type, $limit = null, $toArray = false)
    {
        // verifica se limite de registros foi informado, se nao usa o default
        $limit = empty($limit) ? LIMIT_LOG_AUDIT : $limit;

        $query = $GLOBALS['em']
            ->getRepository('\Geral\Entity\Log')
            ->createQueryBuilder('u')
            ->where("u.type = ?1")
            ->setParameter(1, $type)
            ->setMaxResults($limit)
            ->getQuery();

        $logs = $toArray ? $query->getArrayResult() : $query->getResult();
        return $logs;
    }

    static public function findByTypeAndDate($type, $dataCriacao, $limit = null, $toArray = false)
    {
        // verifica se limite de registros foi informado, se nao usa o default
        $limit = empty($limit) ? LIMIT_LOG_AUDIT : $limit;

        $qb = $GLOBALS['em']->createQueryBuilder();

        $qb->select('l')
            ->from('\Geral\Entity\Log', 'l');
    
        // Inclui filtro por tipo de mensagem (Ex: (S)istema, (E)rro)
        if (!empty($type)) {
            $qb->andwhere($qb->expr()->andX(
                $qb->expr()->eq('l.type',  "?1")
            ))
                ->setParameter(1, $type);
        }

         // Inclui filtro por data de criação
         if (!empty($dataCriacao)) {
            $dataIni = \DateTime::createFromFormat('Y-m-d', $dataCriacao)->setTime(00, 00, 00);
            $dataFim = \DateTime::createFromFormat('Y-m-d', $dataCriacao)->setTime(23, 59, 59);

            $qb->andwhere($qb->expr()->andX(
                $qb->expr()->between('l.dtCriacao', "?2", "?3")
            ))
                ->setParameter(2, (empty($dataIni) ? 'l.dtCriacao' : $dataIni))
                ->setParameter(3, (empty($dataFim) ? 'l.dtCriacao' : $dataFim));
        }

        $qb->orderBy('l.dtCriacao', 'DESC');
        $qb->setMaxResults($limit);
        $query = $qb->getQuery();

        try {
            return $toArray ? $query->getArrayResult() : $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return $toArray ? [] : null;
        }
    }

}
