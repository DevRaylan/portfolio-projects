<?php
namespace Sigcinf\Model\Factory;

use \Sigcinf\Entity\Unidades;
use \Geral\Model\Exception\ErrorException;

abstract class UnidadesFactory
{
    public function __construct( ) {}

    static public function getAll(Bool $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Unidades' ) 
            -> createQueryBuilder('d');

        $query = $query
            -> select('d')
            -> orderBy('d.abrev', 'ASC')
            -> getQuery();

        $query->execute();

        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    static public function getByCentroModerador($idCentroModerado, Bool $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Unidades' ) 
            -> createQueryBuilder('d');

        if($idCentroModerado > 0) {
            $query = $query
                -> andWhere('d.id = :id');
            $query = $query
                -> setParameter('id',  $idCentroModerado);
        }; 

        $query = $query
            -> select('d')
            -> orderBy('d.abrev', 'ASC')
            -> getQuery();

        $query->execute();

        return $toArray ? $query->getArrayResult() : $query->getResult();
    }


    static public function getById($id, $toArray = false)
    {
        $query = $GLOBALS['em'] -> getRepository( '\Sigcinf\Entity\Unidades' ) -> createQueryBuilder('d')
            -> select('d')
            ->where('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        $query->execute();

        return $toArray ? current($query->getArrayResult()) : $query->getSingleResult();
    }

    static public function getByAbrev($abrev, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Unidades' ) 
            -> createQueryBuilder('d')
            -> select('d')
            -> where('d.abrev = :abrev')
            -> setParameter('abrev', $abrev)
            -> getQuery();

        $query->execute();

        return $toArray ? current($query->getArrayResult()) : $query->getSingleResult();

    }

    static public function getByAbrev3($abrev, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Unidades' ) 
            -> createQueryBuilder('d')
            -> select('d')
            -> where('d.abrev = :abrev')
            -> setParameter('abrev', $abrev)
            -> getQuery();

        $query->execute();

        return $toArray ? $query->getArrayResult() : $query->getResult();

    }    

    
    static public function adicionar( string $nome, string $abrev)
    {
        $centro = new Unidades();
        $centro -> setNome( $nome );
        $centro -> setAbrev( $abrev );

        $GLOBALS['em'] -> persist( $centro );
        
        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Centro UDESC já existente.');
        }
    }

    static function atualizar( Int $id, string $nome, string $abrev )
    {
        $centro = self::getById($id);
        $centro -> setNome( $nome );
        $centro -> setAbrev( $abrev );

        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Centro UDESC já existente.');
        }
    }

    static function remover( Int $id )
    {
        $centro = self::getById($id);
    
        $GLOBALS['em'] -> remove( $centro );
        $GLOBALS['em'] -> flush();
    }
}
