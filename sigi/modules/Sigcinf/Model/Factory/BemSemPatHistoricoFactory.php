<?php
namespace Sigcinf\Model\Factory;

use \Sigcinf\Entity\BemSemPatHistorico;
use \Geral\Model\Exception\ErrorException;

abstract class BemSemPatHistoricoFactory
{
    public function __construct( ) {}

    static public function getAll(Bool $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\BemSemPatHistorico' ) 
            -> createQueryBuilder('BemSemPatHistorico')

            -> leftJoin('BemSemPatHistorico.bemsempat', 'BemSemPat')
            -> addSelect('BemSemPat')

            -> getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();

    }

    static public function getById($id, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\BemSemPatHistorico' ) 
            -> createQueryBuilder('d')
            -> select('d')
            -> where('d.id = :id')
            -> setParameter('id', $id)
            -> getQuery();

        $query->execute();

        return $toArray ? current($query->getArrayResult()) : $query->getSingleResult();
    }


    static public function getByBemSemPat($busca, Bool $toArray = false)
    {

        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\BemSemPatHistorico' ) 
            -> createQueryBuilder('BemSemPatHistorico')
            -> select('BemSemPatHistorico')

            -> leftJoin('BemSemPatHistorico.bemsempat', 'BemSemPat')
            -> addSelect('BemSemPat')

            -> where('BemSemPat.id = :id')
            -> setParameter('id', $busca)

            -> orderBy('BemSemPatHistorico.dtHrInclusao', 'DESC')

            -> getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();

    }

    static function adicionar($dados)
    {

        $bemsempathistorico = new BemSemPatHistorico();
        $bemsempathistorico -> setBemSemPat( BemSemPatFactory::getById($dados['bemsempat'], false) );
        $bemsempathistorico -> setMotivo( $dados['motivo'] );
        $bemsempathistorico -> setOperacao( $dados['operacao'] );
        $bemsempathistorico -> setDtHrInclusao( date("Y-m-d H:i:s"));
        $bemsempathistorico -> setQtd( $dados['qtd'] );
        $bemsempathistorico -> setObservacao( $dados['observacao'] );

        $GLOBALS['em'] -> persist( $bemsempathistorico );
        
        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Erro ao adicionar historico.');
        }
    }

    static function atualizar( $dados )
    {
        $bemsempathistorico = self::getById($dados['id']);

//        $bemsempathistorico -> setBemSemPat( BemSemPatFactory::getById($dados['bemsempat'], false) );
        $bemsempathistorico -> setMotivo( $dados['motivo'] );
        $bemsempathistorico -> setOperacao( $dados['operacao'] );

        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Erro ao atualizar historico.');
        }
    }

    static function remover( Int $id )
    {
        $bemsempathistorico = self::getById($id);
    
        $GLOBALS['em'] -> remove( $bemsempathistorico );
        $GLOBALS['em'] -> flush();
    }
}
