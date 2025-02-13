<?php
namespace Sigcinf\Model\Factory;

use \Sigcinf\Entity\Categorias;
use \Geral\Model\Exception\ErrorException;

abstract class CategoriasFactory
{
    public function __construct( ) {}

    static public function getAll(Bool $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Categorias' ) 
            -> createQueryBuilder('d');

        $query = $query
            -> select('d')
            -> orderBy('d.nome', 'ASC')
            -> getQuery();

        $query->execute();

        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    static public function getById($id, $toArray = false)
    {
        $query = $GLOBALS['em'] -> getRepository( '\Sigcinf\Entity\Categorias' ) -> createQueryBuilder('d')
            -> select('d')
            ->where('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        $query->execute();

        return $toArray ? current($query->getArrayResult()) : $query->getSingleResult();
    }
    
    static public function adicionar( string $nome)
    {
        $categoria = new Categorias();
        $categoria -> setNome( $nome );

        $GLOBALS['em'] -> persist( $categoria );
        
        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Categoria existente.');
        }
    }

    static function atualizar( Int $id, string $nome )
    {
        $categoria = self::getById($id);
        $categoria -> setNome( $nome );

        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Categoria existente.');
        }
    }

    static function remover( Int $id )
    {
        $categoria = self::getById($id);
    
        $GLOBALS['em'] -> remove( $categoria );
        $GLOBALS['em'] -> flush();
    }
}
