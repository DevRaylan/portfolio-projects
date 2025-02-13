<?php
namespace Sigcinf\Model\Factory;

use \Sigcinf\Entity\BemSemPat;
use \Geral\Model\Exception\ErrorException;

abstract class BemSemPatFactory
{
    public function __construct( ) {}

    static public function getAll($idCentroModerado, Bool $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\BemSemPat' ) 
            -> createQueryBuilder('BemSemPat')

            -> leftJoin('BemSemPat.unidade', 'Unidades')
            -> addSelect('Unidades')

            -> leftJoin('BemSemPat.setor', 'Setores')
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
            -> getRepository( '\Sigcinf\Entity\BemSemPat' ) 
            -> createQueryBuilder('BemSemPat')

            -> Where('BemSemPat.status = :status')
            -> setParameter('status', 'A')            

            -> getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();

    }

    static public function getById($id, $toArray = false)
    {

        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\BemSemPat' ) 
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
            -> getRepository( '\Sigcinf\Entity\BemSemPat' ) 
            -> createQueryBuilder('BemSemPat')

            -> leftJoin('BemSemPat.unidade', 'Unidades')
            -> addSelect('Unidades')

            -> leftJoin('BemSemPat.setor', 'Setores')
            -> addSelect('Setores') 

            -> where('Unidades.id = :idcentro')
            -> setParameter('idcentro', $centro)

            -> andWhere('Setores.id = :idsetor')
            -> setParameter('idsetor', $setor);

            if (!$busca == '') {
                $query = $query
                    -> andWhere('BemSemPat.codigo = :codigo OR BemSemPat.descricao LIKE :descricao');
                $query = $query
                    -> setParameter('codigo', $codigo);
                $query = $query
                    -> setParameter('descricao', $descricao);            
            };
           
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

    static public function getByCodigo($codigo, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\BemSemPat' ) 
            -> createQueryBuilder('d')
            -> select('d')

            -> where('d.codigo = :codigo')
            -> setParameter('codigo', $codigo)

            -> getQuery();

        $query->execute();

        return $toArray ? current($query->getArrayResult()) : $query->getSingleResult();
    }
                                                                         
    static function adicionar( string $codigo, string $descricao, string $status, string $centro, string $setor)
    {
        $bemsempat = new BemSemPat();
        $bemsempat -> setCodigo( $codigo );
        $bemsempat -> setDescricao( $descricao );
        $bemsempat -> setQtd(0);
        $bemsempat -> setQtdTotal(0);
        $bemsempat -> setStatus( $status );
        $bemsempat -> setUnidade( UnidadesFactory::getById($centro, false) );
        $bemsempat -> setSetor( SetoresFactory::getById($setor, false) );

        $GLOBALS['em'] -> persist( $bemsempat );
        
        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Bem sem patrimônio já existente.');
        }
    }

    static function atualizar( Int $id, string $codigo, string $descricao, string $status, string $centro, string $setor)
    {
        $bemsempat = self::getById($id);
        $bemsempat -> setCodigo( $codigo );
        $bemsempat -> setDescricao( $descricao );
        $bemsempat -> setStatus( $status );
        $bemsempat -> setUnidade( UnidadesFactory::getById($centro, false) );  
        $bemsempat -> setSetor( SetoresFactory::getById($setor, false) );       

        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Bem sem patrimônio já existente.');
        }
    }

    static function atualizarQtd( Int $id, Int $qtd, string $operacao)
    {
        $bemsempat = self::getById($id);

        if ($operacao == 'Entrada') {
            $bemsempat -> setQtd( $bemsempat->getQtd() + $qtd);
        } else {
            $bemsempat -> setQtd( $bemsempat->getQtd() - $qtd);
        };

        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Bem sem patrimônio já existente.');
        }
    }

    static function remover( Int $id )
    {
        $bemsempat = self::getById($id);
    
        $GLOBALS['em'] -> remove( $bemsempat );
        $GLOBALS['em'] -> flush();
    }
    
}
