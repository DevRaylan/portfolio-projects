<?php
namespace Geral\Entity;

/**
* @class ConfigConn
*
*
* @version 1.0
* @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
*/

/**
 * @Entity
 * @Table(name="config_conn")
 **/

class ConfigConn
{
    /**
    * @Id @Column(name="id_configconn", type="integer")
    * @GeneratedValue
    **/
    private $id;

    /** @Column(type="string", length=255, nullable=false) **/
    private $descricao;

    /** @Column(type="string", length=255, nullable=false) **/
    private $host;
    
    /** @Column(type="integer", length=6, nullable=false) **/
    private $port;
    
    /** @Column(type="string", length=10, nullable=false) **/
    private $type;

    /** @Column(type="string", length=50, nullable=false) **/
    private $user;

    /** @Column(type="string", length=255, nullable=false) **/
    private $password;
    
    /** @Column(name="root_dir", type="string", length=100, nullable=false) **/
    private $rootDir;
    
    static $TYPES = ['ftp', 'localTemp'];

    function __construct() {}

    // GETS e SETS ====================================================================================================

    public function getId() {
        return $this -> id;
    }
    
    public function setId($id) {
        $this -> id = $id;
    }

    public function getDescricao() {
        return $this -> descricao;
    }
    
    public function setDescricao($descricao) {
        $descricao = trim($descricao);
        
        if(empty($descricao)) {
            throw new \Exception('Campo "descrição" não deve estar vazio.');
        }
        
        $this -> descricao = $descricao;
    }
    
    public function getHost() {
        return $this -> host;
    }
    
    public function setHost($host) {
        if(empty($host)) {
            throw new \Exception('Campo "host" não deve estar vazio.');
        }
        
        $this -> host = $host;
    }
    
    public function getPort() {
        return $this -> port;
    }
    
    public function setPort($port) {
        if(!is_numeric($port)) {
            throw new \Exception('Campo "port" deve conter somente números.');
        }

        $this -> port = $port;
    }
    
    public function getType() {
        return $this -> type;
    }
    
    public function setType($type) {
        if(!in_array($type, self::$TYPES)) {
            throw new \Exception('Tipo de conexão não esperada.');
        }
        
        $this -> type = $type;
    }
    
    public function getUser() {
        return $this -> user;
    }
    
    public function setUser($user) {
        if(empty($user)) {
            throw new \Exception('Campo "usuário" não deve estar vazio.');
        }
        
        $this -> user = $user;
    }
    
    public function getPassword() {
        return $this -> password;
    }
    
    public function setPassword($password) {
        if(empty($password)) {
            throw new \Exception('Campo "password" não deve estar vazio.');
        }
        
        $this -> password = $password;
    }
    
    public function getRootDir() {
        return $this -> rootDir;
    }
    
    public function setRootDir($rootDir) {
        $this -> rootDir = $rootDir;
    }
    
    public function toArray() {
        return [
            'id' => $this -> getId(),
            'descricao' => $this -> getDescricao(),
            'host' => $this -> getHost(),
            'port' => $this -> getPort(),
            'type' => $this -> getType(),
            'user' => $this -> getUser(),
            'password' => $this -> getPassword(),
            'rootDir' => $this -> getRootDir()
        ];
    }
}
