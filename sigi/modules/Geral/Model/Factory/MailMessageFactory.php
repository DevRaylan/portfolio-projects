<?php
namespace Geral\Model\Factory;

use \Geral\Entity\MailMessage;

/**
 * Criação, gravação e consulta de mensagens enviadas via email pelo Framework.
 */

abstract class MailMessageFactory
{
    public static function add(Array $emails, String $subject, String $message, String $messageTxt = null)
    {
        try {
            $mailMessage = new MailMessage();
            $mailMessage->setEmails($emails);
            $mailMessage->setSubject($subject);
            $mailMessage->setMessage($message);
            $mailMessage->setMessageTxt($messageTxt);
            $mailMessage->setApp($GLOBALS['url']->getUrlAction());

            $GLOBALS['em']->persist($mailMessage);
            $GLOBALS['em']->flush();
        } catch (\Exception $e) {
            throw $e;
        }
        
        return $mailMessage;
    }

    public static function saveLog(Int $id, String $log)
    {
        $mailMessage = $GLOBALS['em']->find('\Geral\Entity\MailMessage', $id);

        if(empty($mailMessage)) {
            throw new ErrorException('Id de MailMessage não encontrado.');
        }

        try {
            $mailMessage->setLog($log);

            $GLOBALS['em']->flush();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function getAll($toArray = false)
    {
        $qb = $GLOBALS['em']->createQueryBuilder()
            ->select('e')
            ->from('\Geral\Entity\MailMessage', 'e')
            ->getQuery();
        
        return $toArray ? $qb->getArrayResult() : $qb->getResult();
    }

    public static function getByApp(String $appName, $toArray = false)
    {
        $messagesAll = self::getAll(true);
        $messagesByApp = [];

        foreach($messagesAll as &$message) {
            $app = explode('/', $message['app']);
            $app = $app[1];
            
            if($app != $appName) continue;

            $messagesByApp[] = $message;
        }

        return $messagesByApp;
    }
}
