<?php
namespace Geral\Entity;

/**
 * @Entity
 * @Table(name="log")
 **/
class Log
{
    /**
     * @Id @Column(name="id_log", type="integer")
     * @GeneratedValue
     **/
    private $id;
    
    /** @Column(name="dt_criacao", type="datetime", nullable=false) **/
    private $dtCriacao;

    /** @Column(type="string", length=255, nullable=false) **/
    private $url;
    
    // Pode ser cpf, token system ou vazio (publico)
    /** @Column(name="session_id", type="string", length=255, nullable=true) **/
    private $sessionId;

    /** @Column(type="string", length=2, nullable=false) **/
    private $type;

    /** @Column(type="string", length=255, nullable=false) **/
    private $msg;

    /** @Column(type="text", nullable=true) **/
    private $detail;
    
    /** @Column(type="string", length=150, nullable=true) **/
    private $plataform;
    
    /** @Column(type="string", length=15, nullable=true) **/
    private $ip;

    public function __construct() {
        $this -> dtCriacao = new \DateTime;
    }

    public function getId( )
    {
        return $this -> id;
    }

    public function getSessionId()
    {
        return $this -> sessionId;
    }

    public function setSessionId( $sessionId )
    {
        $this -> sessionId = $sessionId;
    }

    public function getUrl()
    {
        return $this -> url;
    }

    public function setUrl( $url )
    {
        $this -> url = $url;
    }

    public function getType()
    {
        return $this -> type;
    }

    public function setType( $type )
    {
        $this -> type = $type;
    }

    public function getMsg()
    {
        return $this -> msg;
    }

    public function setMsg( $msg )
    {
        $this -> msg = $msg;
    }

    public function getDetail()
    {
        return $this -> detail;
    }

    public function setDetail( $detail )
    {
        $this -> detail = $detail;
    }

    public function getDtCriacao()
    {
        return $this -> dtCriacao;
    }
    
    public function getPlataform()
    {
        return $this -> plataform;
    }

    public function setPlataform( $plataform )
    {
        $this -> plataform = $plataform;
    }
    
    public function getIp()
    {
        return $this -> ip;
    }

    public function setIp( $ip )
    {
        $this -> ip = $ip;
    }
}
