<?php
namespace Geral\Entity;

/**
 * @Entity
 * @Table(name="download")
 **/
class Download
{
    const ACCESS_PUBLIC = 'public';
    const ACCESS_PRIVATE = 'private';
    /**
     * @Id @Column(name="name", type="string", length=255)
     **/
    private $name;
    
    /** @Column(name="dt_create", type="datetime", nullable=false) **/
    private $dtCreate;

    /** @Column(type="string", length=20, nullable=false) **/
    private $application;

    /** @Column(type="string", length=255, nullable=false) **/
    private $url;
    
    // Pode ser cpf, token system ou vazio (publico)
    /** @Column(name="session_id", type="string", length=255, nullable=true) **/
    private $sessionId;

    /** @Column(type="string", length=150, nullable=true) **/
    private $plataform;
    
    /** @Column(type="string", length=15, nullable=true) **/
    private $ip;

    /** @Column(type="string", length=10, nullable=false) **/
    private $access;

    public function __construct() {
        $this -> dtCreate = new \DateTime;
    }

    public function getName( )
    {
        return $this -> name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getApplication( )
    {
        return $this -> application;
    }

    public function setApplication($application)
    {
        $this -> application = $application;
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

    public function getDtCreate()
    {
        return $this -> dtCreate->format('Y-m-d H:i:s');
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

    public function getAccess( )
    {
        return $this -> access;
    }

    public function setAccess($access)
    {
        if (!in_array($access, array(self::ACCESS_PUBLIC, self::ACCESS_PRIVATE))) {
            throw new \InvalidArgumentException("Tipo de acesso ao arquivo não válido.");
        }

        $this->access = $access;
    }

    public function isFromApp($appName)
    {
        return (!empty($appName) && $appName == $this->application);
    }
}
