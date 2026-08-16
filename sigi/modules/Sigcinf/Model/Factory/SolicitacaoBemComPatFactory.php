<?php

namespace Sigcinf\Model\Factory;

use \Geral\Model\Exception\ErrorException;
use Sigcinf\Entity\SolicitacaoBemComPat;


abstract class SolicitacaoBemComPatFactory
{
    public function __construct()
    { }

    
    static public function getAll($toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\SolicitacaoBemComPat' ) 
            -> createQueryBuilder('SolicitacaoBemComPat')

            -> leftJoin('SolicitacaoBemComPat.solicitacao', 'Solicitacao')
            -> addSelect('Solicitacao')

            -> join('Solicitacao.beneficiado', 'Usuario')
            -> addSelect('Usuario')

            -> getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    static public function getById($id, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\SolicitacaoBemComPat' ) 
            -> createQueryBuilder('SolicitacaoBemComPat')
            -> where('SolicitacaoBemComPat.id = :id')
            -> setParameter('id', $id)

            -> getQuery();

        try {
            $SolicitacaoBemComPat = $toArray ? $query->getArrayResult() : $query->getSingleResult();
        } catch (\Exception $e) {
            $SolicitacaoBemComPat = [];
        }

        return $SolicitacaoBemComPat;

    }

    static public function getBemComPat($id, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\SolicitacaoBemComPat' ) 
            -> createQueryBuilder('SolicitacaoBemComPat')

            -> leftJoin('SolicitacaoBemComPat.solicitacao', 'Solicitacao')
            -> addSelect('Solicitacao')

            -> leftJoin('SolicitacaoBemComPat.bemcompat', 'BemComPat')
            -> addSelect('BemComPat')

            -> where('Solicitacao.id = :id')
            -> setParameter('id', $id)
            
            -> getQuery();


        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    static public function getSolicitacoesAbertasByCodigoBemComPat($codigo, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\SolicitacaoBemComPat' ) 
            -> createQueryBuilder('SolicitacaoBemComPat')

            -> leftJoin('SolicitacaoBemComPat.solicitacao', 'Solicitacao')
            -> addSelect('Solicitacao')

            -> join('Solicitacao.beneficiado', 'Usuario')
            -> addSelect('Usuario')

            -> leftJoin('SolicitacaoBemComPat.bemcompat', 'BemComPat')
            -> addSelect('BemComPat')

            -> where('BemComPat.patrimonio = :codigo')
            -> setParameter('codigo', $codigo)

            -> andwhere('Solicitacao.status = :status')
            -> setParameter('status', 'DIGITADA')

            -> getQuery();


        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    static public function getByBuscaGeralPatrimonio($patrimonio, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\SolicitacaoBemComPat' ) 
            -> createQueryBuilder('SolicitacaoBemComPat')

            -> leftJoin('SolicitacaoBemComPat.solicitacao', 'Solicitacao')
            -> addSelect('Solicitacao')

            -> leftJoin('SolicitacaoBemComPat.bemcompat', 'BemComPat')
            -> addSelect('BemComPat')

            -> join('Solicitacao.beneficiado', 'Usuario')
            -> addSelect('Usuario')

            -> leftJoin('Solicitacao.responsavel', 'Usuario3')
            -> addSelect('Usuario3')

            -> leftJoin('Solicitacao.cadastrador', 'Usuario2')
            -> addSelect('Usuario2');

            $query = $query
                -> andWhere(
                    $query->expr()->orX(
                        $query->expr()->eq('BemComPat.patrimonio', ':codigo'),
                        $query->expr()->like('BemComPat.descricao', ':descricao')
                    )
                    );

            $query = $query
                -> setParameter('codigo', "$patrimonio")
                -> setParameter('descricao', "%$patrimonio%");

            $query = $query
                -> getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    static public function add($solicitacao, $bemcompat, $observacao)
    {
        $solicitacaoBemcompat = new SolicitacaoBemComPat();
        $solicitacaoBemcompat->setBemComPat($bemcompat);
        $solicitacaoBemcompat->setSolicitacao($solicitacao);
        $solicitacaoBemcompat->setObservacao($observacao);

        $GLOBALS['em']->persist($solicitacaoBemcompat);
        $GLOBALS['em']->flush();
    }

    static public function remove($id)
    {
        $solicitacaoBemcompat = self::getById($id);

        if (empty($solicitacaoBemcompat)) {
            throw new \Exception('SolicitacaoBemComPat não encontrado.');
        }

        try {
            $GLOBALS['em']->remove($solicitacaoBemcompat);
            $GLOBALS['em']->flush();
        } catch (\Exception $e) {
            throw $e;
        }

    }


}