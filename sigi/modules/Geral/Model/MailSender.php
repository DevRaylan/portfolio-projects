<?php
/**
 * @class MailSender
 *
 * Esta classe fornece o serviço de envio de email pelas aplicações.
 * Sua utilização se dá da seguinte forma:
 * 
 * ...
 * use \Geral\Model\MailSender;
 * 
 * $mail = new MailSender;
 * $emails = ['email1@dominio', 'email2@dominio'];
 * $subject = 'Assunto do email';
 * $message = 'Conteúdo do email';
 * 
 * try {
 *     $mail->send($emails, $subject, $message);
 * } catch (\Exception $e) {
 *  throw new ErrorException($e);
 * }
 * ...
 * 
 * Todos os email enviados são registrados no BD e podem ser consultados com a utilização da classe
 * \Geral\Model\Factory\MailMessageFactory::getAll().
 *
 * @version 1.0
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 */

namespace Geral\Model;

require DIR_SYS.'/../libs/phpmailer/vendor/autoload.php'; // Inicia o Doctrine

use \Geral\Model\Config;
use \Geral\Model\Factory\MailMessageFactory;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailSender
{
    private $mail;
    private $replyTo;
    private $replyToName;
    private $log;

    function __construct()
    {
        try {
            $this->mail = new PHPMailer(true);
        } catch (\Exception $e) {
            throw new ErrorException('Erro ao instanciar PHPMailer: '.$e->getMessage());
        }

        $this->mail->SMTPDebug  = 2;
        $this->mail->Debugoutput = function($str, $level) {$this->log .= $str;};
        $this->mail->isSMTP();
        $this->mail->Host       = SMTP_HOST;
        $this->mail->Port       = SMTP_PORT;
        $this->mail->SMTPAuth   = false;
        $this->mail->SMTPSecure = false;
        $this->mail->SMTPAutoTLS = false;
        $this->mail->Username   = SMTP_USER;
        $this->mail->Password   = SMTP_PASSWORD;
        $this->mail->CharSet = "UTF-8";
        $this->mail->Encoding = 'base64';
    
        $fromAddress = SMTP_FROM;
        $fromName = SMTP_FROM_NAME;

        // Caso seja definido o replyTo, utiliza também no FROM do e-mail
        if(Config::constantExist('SMTP_REPLYTO') && !empty(Config::getConstant('SMTP_REPLYTO'))) {
            $this->replyTo = Config::getConstant('SMTP_REPLYTO');
            $fromAddress = $this->replyTo;
        }
        if(Config::constantExist('SMTP_REPLYTO_NAME') && !empty(Config::getConstant('SMTP_REPLYTO_NAME'))) {
            $this->replyToName = Config::getConstant('SMTP_REPLYTO_NAME');
            $fromName = $this->replyToName;
        }

        $this->mail->setFrom($fromAddress, $fromName);
        $this->mail->isHTML(true);
    }
    
    public function send(Array $emails, String $subject, String $contentHtml, String $contentTxt = null)
    {
        $emailMessage = MailMessageFactory::add($emails, $subject, $contentHtml, $contentTxt);

        foreach($emailMessage->getEmails() as &$email) {
            $this->mail->addAddress($email);
        }

        if(!empty($this->replyTo)) {
            $this->mail->addReplyTo($this->replyTo, $this->replyToName);
        }

        $this->mail->Subject = $emailMessage->getSubject();
        $this->mail->Body = $emailMessage->getMessage();
        $this->mail->AltBody = $emailMessage->getMessageTxt();
        $this->mail->send();

        MailMessageFactory::saveLog($emailMessage->getId(), $this->log);
    }
}
