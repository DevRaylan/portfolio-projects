<?php

namespace Sigcinf\Model\Factory;

use \Geral\Model\Exception\ErrorException;
use Sigcinf\Entity\SolicitacaoBemSemPat;


abstract class SolicitacaoBemSemPatFactory
{
    public function __construct()
    { }

    
    static public function getAll($toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\SolicitacaoBemSemPat' ) 
            -> createQueryBuilder('SolicitacaoBemSemPat')

            -> leftJoin('SolicitacaoBemSemPat.solicitacao', 'Solicitacao')
            -> addSelect('Solicitacao')

            -> join('Solicitacao.beneficiado', 'Usuario')
            -> addSelect('Usuario')

            -> getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    static public function getById($id, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\SolicitacaoBemSemPat' ) 
            -> createQueryBuilder('SolicitacaoBemSemPat')
            -> where('SolicitacaoBemSemPat.id = :id')
            -> setParameter('id', $id)

            -> getQuery();

        try {
            $SolicitacaoBemSemPat = $toArray ? $query->getArrayResult() : $query->getSingleResult();
        } catch (\Exception $e) {
            $SolicitacaoBemSemPat = [];
        }

        return $SolicitacaoBemSemPat;

    }

    static public function getBemSemPat($id, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\SolicitacaoBemSemPat' ) 
            -> createQueryBuilder('SolicitacaoBemSemPat')

            -> leftJoin('SolicitacaoBemSemPat.solicitacao', 'Solicitacao')
            -> addSelect('Solicitacao')

            -> leftJoin('SolicitacaoBemSemPat.bemsempat', 'BemSemPat')
            -> addSelect('BemSemPat')

            -> where('Solicitacao.id = :id')
            -> setParameter('id', $id)
            
//            -> orderBy('SolicitacaoBemSemPat.dtInicio, SolicitacaoBemSemPat.dtFim', 'ASC')

            -> getQuery();


        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

//    static public function getBemSemPatByCodigo($codigo, $toArray = false)
//    {
//        $query = $GLOBALS['em'] 
//            -> getRepository( '\Sigcinf\Entity\SolicitacaoBemSemPat' ) 
//            -> createQueryBuilder('SolicitacaoBemSemPat')
//
//            -> leftJoin('SolicitacaoBemSemPat.solicitacao', 'Solicitacao')
//            -> addSelect('Solicitacao')
//
//            -> join('Solicitacao.beneficiado', 'Usuario')
//            -> addSelect('Usuario')
//
//            -> leftJoin('SolicitacaoBemSemPat.bemsempat', 'BemSemPat')
//            -> addSelect('BemSemPat')
//
//            -> where('BemSemPat.codigo = :codigo')
//            -> setParameter('codigo', $codigo)
//
//            -> getQuery();
//
//
//        return $toArray ? $query->getArrayResult() : $query->getResult();
//    }


    static public function getSolicitacoesAbertasByCodigoBemSemPat($codigo, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\SolicitacaoBemSemPat' ) 
            -> createQueryBuilder('SolicitacaoBemSemPat')

            -> leftJoin('SolicitacaoBemSemPat.solicitacao', 'Solicitacao')
            -> addSelect('Solicitacao')

            -> join('Solicitacao.beneficiado', 'Usuario')
            -> addSelect('Usuario')

            -> leftJoin('SolicitacaoBemSemPat.bemsempat', 'BemSemPat')
            -> addSelect('BemSemPat')

            -> where('BemSemPat.codigo = :codigo')
            -> setParameter('codigo', $codigo)

            -> andwhere('Solicitacao.status = :status')
            -> setParameter('status', 'DIGITADA')

            -> getQuery();


        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    static public function getByBuscaGeralCodigo($codigo, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\SolicitacaoBemSemPat' ) 
            -> createQueryBuilder('SolicitacaoBemSemPat')

            -> leftJoin('SolicitacaoBemSemPat.solicitacao', 'Solicitacao')
            -> addSelect('Solicitacao')

            -> leftJoin('SolicitacaoBemSemPat.bemsempat', 'BemSemPat')
            -> addSelect('BemSemPat')

            -> join('Solicitacao.beneficiado', 'Usuario')
            -> addSelect('Usuario')

            -> leftJoin('Solicitacao.responsavel', 'Usuario3')
            -> addSelect('Usuario3')

            -> leftJoin('Solicitacao.cadastrador', 'Usuario2')
            -> addSelect('Usuario2');

            $query = $query
                -> andWhere(
                    $query->expr()->orX(
                        $query->expr()->eq('BemSemPat.codigo', ':codigo'),
                        $query->expr()->like('BemSemPat.descricao', ':descricao')
                    )
                    );

            $query = $query
                -> setParameter('codigo', "$codigo")
                -> setParameter('descricao', "%$codigo%");

            $query = $query
                -> getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    static public function add($solicitacao, $bemsempat, $observacao)
    {
        $solicitacaoBemsempat = new SolicitacaoBemSemPat();
        $solicitacaoBemsempat->setBemSemPat($bemsempat);
        $solicitacaoBemsempat->setSolicitacao($solicitacao);
        $solicitacaoBemsempat->setObservacao($observacao);
//        $solicitacaoBemsempat->setDtInicio(substr($dtIni, 6, 4) .'-'. substr($dtIni, 3, 2) .'-'. substr($dtIni, 0, 2) .' '. substr($hrIni, 0, 5) );
//        $solicitacaoBemsempat->setHrInicio($hrIni);
//        $solicitacaoBemsempat->setDtFim(substr($dtFim, 6, 4) .'-'. substr($dtFim, 3, 2) .'-'. substr($dtFim, 0, 2) .' '. substr($hrFim, 0, 5) );
//        $solicitacaoBemsempat->setHrFim($hrFim);

        $GLOBALS['em']->persist($solicitacaoBemsempat);
        $GLOBALS['em']->flush();
    }

    static public function remove($id)
    {
        $solicitacaoBemsempat = self::getById($id);

        if (empty($solicitacaoBemsempat)) {
            throw new \Exception('SolicitacaoBemSemPat não encontrado.');
        }

        try {
            $GLOBALS['em']->remove($solicitacaoBemsempat);
            $GLOBALS['em']->flush();
        } catch (\Exception $e) {
            throw $e;
        }

    }


}