<?php

namespace Geral\Model\Factory;

use Geral\Entity\UserDataSimulator;
use Geral\Model\Exception\ErrorException;
use Geral\Model\Interfaces\UserDataInterface;

abstract class UserDataSimulatorFactory
{
    //=== Consultas ============================================================

    public static function getById($id, $toArray = false)
    {
        $registro = self::searchEntry('id', $id, $toArray);
        
        if(empty($registro)) {
            throw new \Exception('Usuário com o ID "'.$id.'" não encontrado.');
        }

        return current($registro);
    }

    public static function getByCpf($cpf, $toArray = false)
    {
        $registro = self::searchEntry('cpf', $cpf, $toArray);
        
        if(empty($registro)) {
            throw new \Exception('Usuário com o CPF "'.$cpf.'" não encontrado.');
        }

        return $registro;
    }

    public static function getByVinculo($vinculo, $toArray = false)
    {
        $registros = self::searchEntry('vinculo', $vinculo, $toArray);

        return $registros;
    }

    public static function getAll($toArray = false)
    {
        $registros = $GLOBALS['em']
            ->getRepository('\Geral\Entity\UserDataSimulator')
            ->createQueryBuilder('u')
            ->getQuery();

        return $toArray ? $registros->getArrayResult() : $registros->getResult();
    }

    public static function getByFilter($filter, $toArray = false)
    {        
        $query = $GLOBALS['em']
            ->getRepository('\Geral\Entity\UserDataSimulator')
            ->createQueryBuilder('uds');

        foreach($filter as $attribute => $valores) {
            $query->andWhere('uds.'.$attribute.' IN (:'.$attribute.')')
            ->setParameter($attribute, $valores);
        }   
        
        $query = $query->getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    //=== CRUD =================================================================

    public static function add(Array $dados)
    {
        $usuario = new UserDataSimulator();

        self::prepare($usuario, $dados);
        
        $GLOBALS['em']->persist($usuario);
        
        if(!isset($dados['noExecFlush'])) {
            $GLOBALS['em']->flush();
        }
    }

    public static function update($id, $dados)
    {
        $usuario = self::getById($id);

        self::prepare($usuario, $dados);

        $GLOBALS['em']->flush();
    }

    public static function remove($id)
    {
        $usuario = self::getById($id);

        $GLOBALS['em']->remove($usuario);
        $GLOBALS['em']->flush();
    }

    // FUNÇÕES AUXILIARES ======================================================
    
    public static function loadFromDataFile(String $dataFileName)
    {
        $registros = self::getAll();

        foreach($registros as $registro) {
            $GLOBALS['em']->remove($registro);
        }

        $GLOBALS['em']->flush();

        $fd = fopen(DIR_FILES.'/Geral/'.DIR_NAME_TEMP.'/'.$dataFileName, 'r');
        $columns = fgetcsv($fd);

        $diff = array_diff(UserDataInterface::ATRIBUTOS_DO_USUARIO,$columns);

        if(!empty($diff)) {
            throw new \Exception('As colunas "'.implode(', ', $diff).'" não estão presentes no arquivo.');
        }

        $cpfsAndVinculo = [];

        while(($dados = fgetcsv($fd)) !== false) {
            $dados = array_combine($columns, $dados);
            $dados['vinculo'] = strtoupper($dados['vinculo']);
            $compostKey = $dados['cpf'].'_'.$dados['vinculo'];
            
            if(in_array($compostKey, $cpfsAndVinculo)) {
                throw new ErrorException('Já existe um registro com o cpf "'.$dados['cpf'].'" e vínculo
                    '.$dados['vinculo'].'.');
            }

            $cpfsAndVinculo[] = $compostKey;
            $dados['createdOnLogin'] = false;
            $dados['noExecFlush'] = true;

            self::add($dados);
        }

        $GLOBALS['em']->flush();
    }

    private static function searchEntry($field, $value, $toArray = true)
    {
        $registros = $GLOBALS['em']
            ->getRepository('\Geral\Entity\UserDataSimulator')
            ->createQueryBuilder('u')
            ->where('u.'.$field.' = :'.$field)
            ->setParameter($field, $value)
            ->getQuery();

        return $toArray ? $registros->getArrayResult() : $registros->getResult();
    }

    private static function prepare(UserDataSimulator $usuario, $dados)
    {
        foreach(UserDataInterface::ATRIBUTOS_DO_USUARIO as $from => $to) {                
            $func = 'set'.ucwords($to);
            $usuario->$func($dados[$to]);
        }

        $usuario->setCreatedOnLogin($dados['createdOnLogin']);
    }

    public static function getQtdFakes()
    {
        $qtd = $GLOBALS['em']
            ->getRepository('\Geral\Entity\UserDataSimulator')
            ->createQueryBuilder('u')
            ->select('COUNT(u.id) as qtd')
            ->where('u.createdOnLogin = ?1')
            ->setParameter(1, true)
            ->getQuery()
            ->getSingleScalarResult();

        return $qtd;
    }
}
