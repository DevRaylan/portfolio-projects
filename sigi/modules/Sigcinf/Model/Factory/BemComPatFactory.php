<?php
namespace Sigcinf\Model\Factory;

use \Sigcinf\Entity\BemComPat;
use \Geral\Model\Exception\ErrorException;

abstract class BemComPatFactory
{
    public function __construct( ) {}

    static public function getAll($idCentroModerado, Bool $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\BemComPat' ) 
            -> createQueryBuilder('BemComPat')

            -> leftJoin('BemComPat.unidade', 'Unidades')
            -> addSelect('Unidades')

            -> leftJoin('BemComPat.setor', 'Setores')
            -> addSelect('Setores');

            if($idCentroModerado > 0) {
                $query = $query
                    -> andWhere('Unidades.id = :id');
                $query = $query
                    -> setParameter('id',  $idCentroModerado);
            };

        $query = $query
            ->getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();

    }

    static public function getAllAtivos(Bool $toArray = false)
    {

        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\BemComPat' ) 
            -> createQueryBuilder('BemComPat')

            -> Where('BemComPat.status = :status')
            -> setParameter('status', 'A')            

            -> getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();

    }

    static public function getById($id, $toArray = false)
    {

        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\BemComPat' ) 
            -> createQueryBuilder('d')
            -> select('d')

            -> where('d.id = :id')
            -> setParameter('id', $id)

            -> getQuery();

        $query->execute();

        return $toArray ? current($query->getArrayResult()) : $query->getSingleResult();

    }
                                      
    static public function getByBusca($busca, $centro, $setor, $idCentroModerado, Bool $toArray = false)
    {

        if (is_numeric($busca)) {
            $codigo = $busca;
            $descricao = '';
        } else {
            $codigo = '';
            $descricao = '%' . $busca . '%';
        }

        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\BemComPat' ) 
            -> createQueryBuilder('BemComPat')

            -> leftJoin('BemComPat.unidade', 'Unidades')
            -> addSelect('Unidades')

            -> leftJoin('BemComPat.setor', 'Setores')
            -> addSelect('Setores')            

            -> where('Unidades.id = :idcentro')
            -> setParameter('idcentro', $centro)

            -> andWhere('Setores.id = :idsetor')
            -> setParameter('idsetor', $setor);

            if (!$busca == '') {
                $query = $query
                    -> andWhere('BemComPat.patrimonio = :codigo OR BemComPat.descricao LIKE :descricao');
                $query = $query
                    -> setParameter('codigo', $codigo);
                $query = $query
                    -> setParameter('descricao', $descricao);
            };

            if($idCentroModerado > 0) {
                $query = $query
                    -> andWhere('Unidades.id = :idunidade');
                $query = $query
                    -> setParameter('idunidade',  $idCentroModerado);
            };            

            $query = $query
            ->getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();


    }

    static public function getByCodigo($codigo, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\BemComPat' ) 
            -> createQueryBuilder('d')
            -> select('d')

            -> where('d.patrimonio = :codigo')
            -> setParameter('codigo', $codigo)

            -> getQuery();

        $query->execute();

        return $toArray ? current($query->getArrayResult()) : $query->getSingleResult();
    }    

    static function adicionar(string $codigo, string $descricao, string $status, string $centro, string $setor)
    {
        $bemcompat = new BemComPat();
        $bemcompat -> setPatrimonio( $codigo );
        $bemcompat -> setDescricao( $descricao );
        $bemcompat -> setStatus( $status);
        $bemcompat -> setUnidade( UnidadesFactory::getById($centro, false) );
        $bemcompat -> setSetor( SetoresFactory::getById($setor, false) );

        $GLOBALS['em'] -> persist( $bemcompat );
        
        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Erro ao adicionar bem com patrimônio.');
        }
    }

    static function atualizar( Int $id, string $codigo, string $descricao, string $status, string $centro, string $setor )
    {
        $bemcompat = self::getById($id);
        $bemcompat -> setPatrimonio( $codigo );
        $bemcompat -> setDescricao( $descricao );
        $bemcompat -> setStatus( $status);
        $bemcompat -> setUnidade( UnidadesFactory::getById($centro, false) );
        $bemcompat -> setSetor( SetoresFactory::getById($setor, false) );

        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Erro ao atualizar o bem com patrimônio.');
        }
    }

    static function remover( Int $id )
    {
        $bemcompat = self::getById($id);
    
        $GLOBALS['em'] -> remove( $bemcompat );
        $GLOBALS['em'] -> flush();
    }
}
