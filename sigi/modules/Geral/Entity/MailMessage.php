<?php
namespace Geral\Entity;

/**
 * @Entity
 * @Table(name="mailmessage")
 **/
class MailMessage
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     **/
    private $id;
    
    /** @Column(name="dt_criacao", type="datetime", nullable=false) **/
    private $dtCriacao;

    /** @Column(type="text", nullable=false) **/
    private $emails;

    /** @Column(type="string", length=255, nullable=false) **/
    private $subject;

    /** @Column(type="text", nullable=false) **/
    private $message;

    /** @Column(name="message_txt", type="text", nullable=true) **/
    private $messageTxt;

    /** @Column(type="text", nullable=true) **/
    private $log;

    /** @Column(type="string", length=255, nullable=true) **/
    private $status;

    /** @Column(type="string", length=255, nullable=false) **/
    private $app;

    public function __construct() {
        $this -> dtCriacao = new \DateTime;
    }

    public function getId( )
    {
        return $this -> id;
    }

    public function getEmails( )
    {
        return explode(';', $this -> emails);
    }

    public function setEmails(Array $emails )
    {
        $this -> emails = implode(';', $emails);
    }

    public function getSubject( )
    {
        return $this -> subject;
    }

    public function setSubject( $subject )
    {
        $this -> subject = $subject;
    }

    public function getMessage( )
    {
        return $this -> message;
    }

    public function setMessage( $message )
    {
        $this -> message = $message;
    }

    public function getMessageTxt( )
    {
        return $this -> messageTxt;
    }

    public function setMessageTxt( $messageTxt )
    {
        if(!empty($messageTxt)) {
            $this -> messageTxt = strip_tags($messageTxt);
        }
    }

    public function getLog( )
    {
        return $this -> log;
    }

    public function setLog( $log )
    {
        // Definir status de acordo com o conteúdo do log.
        //...

        $this -> log = $log;
    }

    public function getApp( )
    {
        return $this -> app;
    }

    public function setApp( $app )
    {
        $this -> app = $app;
    }

    public function getDtCriacao()
    {
        return $this -> dtCriacao;
    }
}
