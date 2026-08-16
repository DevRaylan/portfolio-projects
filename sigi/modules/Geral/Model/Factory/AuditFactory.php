<?php

namespace Geral\Model\Factory;

use \Geral\Entity\Log;
use \Geral\Model\Config;
use \Geral\Model\UserSession;

/*
 * AuditFactory.php
 * Responsavel por registrar as informações de auditoria.
 */

abstract class AuditFactory extends \Geral\Model\Factory\AbstractFactory
{
    const AUDIT_LEVEL = 'ALL';

    const TYPE_INFO = 'I';
    const TYPE_SYS = 'S';

    const MSG_ACESSO = 'Acesso ao backend.';
    const MSG_SYS = 'Audit automático do sistema.';

    static private $auditMsgs = [];


    static public function auditInfo($msg = null)
    {
        if (empty($msg)) {
            $msg = self::MSG_ACESSO;
        }

        $detail = 'Dados sensíveis foram restringidos.';
        // Verifica nivel de registro de auditoria (ALL/PRIVATE)
        if (self::AUDIT_LEVEL == Config::getConstant('AUDIT_LEVEL')) {
            $detail = 'POST: ' . json_encode($_POST) . 'GET: ' . json_encode($_GET);
        }

        self::_pushMsg($GLOBALS['url']->getUrlAction(), self::TYPE_INFO, $msg, $detail);
    }

    static public function auditSys()
    {
        // Evita registrar log de login
        if (self::_isAutenticacao()) {
            return;
        }

        // Evita registrar log de navegação (index)
        if (self::_isIndexNavigation()) {
            return;
        }

        // Evita registrar log na instalação pois a tabela ainda nao existe.
        if (self::_isInstallController()) {
            return;
        }

        $detail = 'Nivel de detalhe: dados privados.';
        // Verifica nivel de registro de auditoria (ALL/PRIVATE)
        if (self::AUDIT_LEVEL == Config::getConstant('AUDIT_LEVEL')) {
            $detail = 'POST: ' . json_encode($_POST) . 'GET: ' . json_encode($_GET);
        }

        self::_pushMsg($GLOBALS['url']->getUrlAction(), self::TYPE_SYS, self::MSG_SYS, $detail);
    }


    static public function save()
    {
        foreach (self::$auditMsgs as $msg) {
            parent::add($msg);
        }
    }


    static public function findByParams($sessionId, $dataCriacao, $type = null, $modulo = null, 
                                        $keywords = null, $limit = null, $toArray = false)
    {

        // verifica se limite de registros foi informado, se nao usa o default
        $limit = empty($limit) ? LIMIT_LOG_AUDIT : $limit;

        $qb = $GLOBALS['em']->createQueryBuilder();

        $qb->select('l')
            ->from('\Geral\Entity\Log', 'l');

        // Inclui filtro por sessão
        if (!empty($sessionId)) {
            $qb->andwhere($qb->expr()->andX(
                $qb->expr()->eq('l.sessionId',  "?1")
            ))
                ->setParameter(1, $sessionId);
        }

        // Inclui filtro por tipo de mensagem (Ex: (S)istema, (E)rro)
        if (!empty($type)) {
            $qb->andwhere($qb->expr()->andX(
                $qb->expr()->eq('l.type',  "?2")
            ))
                ->setParameter(2, $type);
        }

        // Inclui filtro por palavra chave na coluna de mensagem
        if (!empty($keywords)) {
            // separa as palavras para buscar no texto todo independendo da posição.
            $paramWords = "";
            foreach (explode(" ", $keywords) as $word) {
                $paramWords = $paramWords . "%" . $word;
            }
            $paramWords = $paramWords . "%";

            // Inclui parametro na consulta
            $qb->andwhere($qb->expr()->andX(
                $qb->expr()->like('l.msg', "?3")
            ))
                ->setParameter(3, "%" . $paramWords . "%");
        }

        // Inclui filtro por módulo/aplicação
        if (!empty($modulo)) {
            $qb->andwhere($qb->expr()->andX(
                $qb->expr()->like('l.url', "?4")
            ))
                ->setParameter(4, "% /" . $modulo . "%");
        }

         // Inclui filtro por data de criação
        if (!empty($dataCriacao)) {
            $dataIni = \DateTime::createFromFormat('Y-m-d', $dataCriacao)->setTime(00, 00, 00);
            $dataFim = \DateTime::createFromFormat('Y-m-d', $dataCriacao)->setTime(23, 59, 59);

            $qb->andwhere($qb->expr()->andX(
                $qb->expr()->between('l.dtCriacao', "?5", "?6")
            ))
                ->setParameter(5, (empty($dataIni) ? 'l.dtCriacao' : $dataIni))
                ->setParameter(6, (empty($dataFim) ? 'l.dtCriacao' : $dataFim));
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

    //===============================================================================================================


    static private function _isIndexNavigation()
    {
        return $GLOBALS['url']->getNameAction() == 'index';
    }

    static private function _isAutenticacao()
    {
        return $GLOBALS['url']->getNameAction() == 'autenticar';
    }

    static private function _isInstallController()
    {
        return $GLOBALS['url']->getNameController() == 'Install';
    }

    static private function _pushMsg($url, $type, $msg, $detail)
    {
        $auditObj = self::_buildAuditObj($url, $type, $msg, $detail);

        array_push(self::$auditMsgs, $auditObj);
    }

    static private function _buildAuditObj($url, $type, $msg, $detail)
    {
        $usuarioLogado = UserSession::getSession();
        $webServiceToken = $_SERVER['PHP_AUTH_USER'];

        if ($usuarioLogado) { // Registra o usuario logado para acesso privado
            $sessionId = $usuarioLogado['id'];
        } elseif ($webServiceToken) { // Registra o token do webservice requerente
            $sessionId = $webServiceToken;
        } else { // Registra acesso publico
            $sessionId = 'Acesso Publico';
        }

        $auditObj = new Log;
        $auditObj->setUrl($_SERVER['REQUEST_METHOD'] . ' ' . $url);
        $auditObj->setType($type);
        $auditObj->setMsg($msg);
        $auditObj->setDetail($detail);
        $auditObj->setSessionId($sessionId);
        $auditObj->setPlataform($_SERVER['HTTP_USER_AGENT']);
        $auditObj->setIp($_SERVER['HTTP_X_REAL_IP']); // REMOTE_ADDR sempre é o ip do Servidor de Cache

        return $auditObj;
    }
}
