<?php
namespace Geral\Model\Factory;

use \Geral\Entity\Log;
use \Geral\Entity\ConfigConn;
use \Geral\Model\FtpConn;
use \Geral\Model\LocalTempConn;
/*
 * LogFactory.php
 * Criação, gravação e consulta de eventos de Log.
 *
 */

abstract class ConfigConnFactory
{
    static public function create ($descricao, $host, $port, $type, $user, $password, $rootDir)
    {
        $configConn = $GLOBALS['em'] -> getRepository('\Geral\Entity\ConfigConn') -> findBy(['descricao' => $descricao]);
        
        // Uma configuração com a descrição já existe.
        if(!empty($configConn)) {
            throw new \Exception('Configuração "'.$descricao.'" já existe.');
        }
        
        try {
            $config = new ConfigConn;
            $config -> setDescricao($descricao);
            $config -> setHost($host);
            $config -> setPort($port);
            $config -> setType($type);
            $config -> setUser($user);
            $config -> setPassword($password);
            $config -> setRootDir($rootDir);
        } catch(\Exception $e) {
            throw new \Exception($e -> getMessage());
        }
        
        try{
            $GLOBALS['em'] -> persist($config);
            $GLOBALS['em'] -> flush($config);
        } catch(\Exception $e) {
            throw new \Exception("Erro ao gravar dados no banco de dados.");
        }
        
        return $config;
    }
    
    static public function update ($id, $descricao, $host, $port, $type, $user, $password, $rootDir)
    {
        $config = $GLOBALS['em'] -> getRepository('\Geral\Entity\ConfigConn') -> findBy(['descricao' => $descricao]);

        // Uma configuração com a descrição já existe.
        if(!empty($config) && $config[0] -> getId() != $id) {
            throw new \Exception('Configuração "'.$descricao.'" já existe.');
        }
        
        try {
            $config = ConfigConnFactory::getById($id);
            $config -> setDescricao($descricao);
            $config -> setHost($host);
            $config -> setPort($port);
            $config -> setType($type);
            $config -> setUser($user);
            $config -> setPassword($password);
            $config -> setRootDir($rootDir);
        } catch(\Exception $e) {
            throw new \Exception($e -> getMessage());
        }
        
        try{
            $GLOBALS['em'] -> persist($config);
            $GLOBALS['em'] -> flush($config);
        } catch(\Exception $e) {
            throw new \Exception("Erro ao gravar dados no banco de dados.");
        }
        
        return $config;
    }
    
    static public function getConn (ConfigConn $configConn)
    {
        switch($configConn -> getType()) {
            case 'ftp':
            case 'sftp':
                try {
                    return new FtpConn($configConn -> getHost(), $configConn -> getPort(), $configConn -> getUser(), $configConn -> getPassword());
                } catch (\Exception $e) {
                    LogFactory::error(__CLASS__.': '.$e -> getMessage());
                    throw new \Exception(LogFactory::getLastMsg());
                }
                
                break;
            case 'localTemp':
                try {
                    return new LocalTempConn($configConn -> getHost(), $configConn -> getPort(), $configConn -> getUser(), $configConn -> getPassword());
                } catch (\Exception $e) {
                    LogFactory::error(__CLASS__.': '.$e -> getMessage());
                    throw new \Exception(LogFactory::getLastMsg());
                }
                
                break;
            default:
                throw new \Exception("Tipo de conexão não informada ou não suportada.");
                break;
        }
    }
    
    static function getById($id)
    {
        $config = $GLOBALS['em'] -> find('\Geral\Entity\ConfigConn', $id);
        
        if(empty($config)) {
            throw new \Exception("ConfigConn não encontrado.");
        }
        
        return $config;
    }
    
    public static function getAll($toArray = false)
    {
        $configs = $GLOBALS['em'] -> getRepository('\Geral\Entity\ConfigConn') -> findAll();
        
        if($toArray) {
            $configsArray = [];

            foreach($configs as $config) {
                $configsArray[] = $config -> toArray();
            }

            $configs = &$configsArray;
        }
        
        return $configs;
    }
}
