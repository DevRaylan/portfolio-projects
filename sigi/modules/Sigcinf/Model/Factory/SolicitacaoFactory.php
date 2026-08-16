<?php
namespace Sigcinf\Model\Factory;

use \Sigcinf\Entity\Solicitacao;
use \Geral\Model\Exception\ErrorException;
use \Sigcinf\Entity\Unidades;
use \Sigcinf\Entity\Setores;

abstract class SolicitacaoFactory
{

    public const ST_DIGITADA = "DIGITADA";
    public const ST_ATENDIDA = "ATENDIDA";
    public const ST_DEVOLVIDA = "DEVOLVIDA";
    public const ST_CANCELADA = "CANCELADA";
    public const ST_PENDENTE = "PENDENTE";
    public const ST_ATRASADA = "ATRASADA";

    public function __construct( ) 
    {}

    static public function getAll(Bool $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Solicitacao' ) 
            -> createQueryBuilder('Solicitacao')

            -> leftJoin('Solicitacao.beneficiado', 'Usuario')
            -> addSelect('Usuario')

            -> getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();

    }

    static public function getByStatus($status, $idCentroModerado, Bool $toArray = false)
    {

        $timezone = new \DateTimeZone('America/Sao_Paulo');
        $currentDate = new \DateTime('now', $timezone);

        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Solicitacao' ) 
            -> createQueryBuilder('Solicitacao')

            -> leftJoin('Solicitacao.beneficiado', 'Usuario2')
            -> addSelect('Usuario2')

            -> leftJoin('Solicitacao.cadastrador', 'Usuario')
            -> addSelect('Usuario')

            -> leftJoin('Solicitacao.unidade', 'Unidades')
            -> addSelect('Unidades');


            if ($status == SolicitacaoFactory::ST_ATENDIDA) {
                $query 
                    -> where('Solicitacao.status = :status')
                    -> setParameter('status', SolicitacaoFactory::ST_ATENDIDA)
                    -> andWhere('Solicitacao.dtHrDevolucao >= :currentDate')
                    -> setParameter('currentDate',   $currentDate);
            } else {
                if ($status == SolicitacaoFactory::ST_ATRASADA) {
                    $query 
                        -> where('Solicitacao.status = :status')
                        -> setParameter('status',  SolicitacaoFactory::ST_ATENDIDA)
                        -> andWhere('Solicitacao.dtHrDevolucao < :currentDate')
                        -> setParameter('currentDate',  $currentDate);
                    } else {
                   $query 
                    -> where('Solicitacao.status = :status')
                    -> setParameter('status',  $status);  
                }            
            }           

            if ($idCentroModerado > 0) {
                $query = $query
                    -> andWhere('Unidades.id = :id')
                    -> setParameter('id',  $idCentroModerado);

            };

            $query = $query
                    -> orderBy('Solicitacao.ano', 'DESC');
            $query = $query
                    -> orderBy('Solicitacao.sequencia', 'DESC');

        $query = $query->getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    static public function getByCpf($cpf, $status, Bool $toArray = false)
    {
        
        $timezone = new \DateTimeZone('America/Sao_Paulo');
        $currentDate = new \DateTime('now', $timezone);

        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Solicitacao' ) 
            -> createQueryBuilder('Solicitacao')

            -> leftJoin('Solicitacao.beneficiado', 'Usuario')
            -> addSelect('Usuario')

            -> leftJoin('Solicitacao.cadastrador', 'Usuario2')
            -> addSelect('Usuario2')

            -> leftJoin('Solicitacao.responsavel', 'Usuario3')
            -> addSelect('Usuario3');

//            if ($status == SolicitacaoFactory::ST_ATENDIDA) {
//                $query 
//                    -> where('Solicitacao.status = :status')
//                    -> setParameter('status', SolicitacaoFactory::ST_ATENDIDA)
//                    -> andWhere('Solicitacao.dtHrDevolucao >= :currentDate')
//                    -> setParameter('currentDate',   $currentDate);
//            } else {
//                if ($status == SolicitacaoFactory::ST_ATRASADA) {
//                    $query 
//                        -> where('Solicitacao.status = :status')
//                        -> setParameter('status',  SolicitacaoFactory::ST_ATENDIDA)
//                        -> andWhere('Solicitacao.dtHrDevolucao < :currentDate')
//                        -> setParameter('currentDate',  $currentDate);
//                    } else {
//                   $query 
//                    -> where('Solicitacao.status = :status')
//                    -> setParameter('status',  $status);  
//                }            
//            }   


            $query = $query
                -> andWhere(
                    $query->expr()->orX(
                        $query->expr()->eq('Usuario.cpf', ':cpfBeneficiado'),
                        $query->expr()->eq('Usuario3.cpf', ':cpfResponsavel')
                    )
                    );

            $query = $query
                -> setParameter('cpfBeneficiado', $cpf)
                -> setParameter('cpfResponsavel', $cpf)
                -> orderBy('Solicitacao.id', 'ASC');

                
            $query = $query
                ->getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    static public function getByBuscaGeralCpf($busca, Bool $toArray = false)
    {
        
        $query = $GLOBALS['em'] 

            -> getRepository( '\Sigcinf\Entity\Solicitacao' ) 
            -> createQueryBuilder('Solicitacao')

            -> leftJoin('Solicitacao.beneficiado', 'Usuario')
            -> addSelect('Usuario')

            -> leftJoin('Solicitacao.cadastrador', 'Usuario2')
            -> addSelect('Usuario2')

            -> leftJoin('Solicitacao.responsavel', 'Usuario3')
            -> addSelect('Usuario3');

            $query = $query
                -> andWhere(
                    $query->expr()->orX(
                        $query->expr()->eq('Usuario.cpf', ':cpfBeneficiado'),
                        $query->expr()->eq('Usuario3.cpf', ':cpfResponsavel')
                    )
                    );

            $query = $query
                -> setParameter('cpfBeneficiado', $busca)
                -> setParameter('cpfResponsavel', $busca);
            
            $query = $query
                -> orderBy('Solicitacao.id', 'ASC');                

            $query = $query
                ->getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    static public function getById($id, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Solicitacao' ) 
            -> createQueryBuilder('d')
            -> select('d')

            -> leftJoin('d.beneficiado', 'Usuario')
            -> addSelect('Usuario')
            -> leftJoin('Usuario.unidade', 'Unidades')
            -> addSelect('Unidades')

            -> leftJoin('d.responsavel', 'Usuario3')
            -> addSelect('Usuario3')
            -> leftJoin('Usuario3.unidade', 'Unidades3')
            -> addSelect('Unidades3')

            -> leftJoin('d.cadastrador', 'UsuarioCadastrador')
            -> addSelect('UsuarioCadastrador')

            -> leftJoin('d.unidade', 'UnidadeSolicitacao')
            -> addSelect('UnidadeSolicitacao')

            -> leftJoin('d.setor', 'SetorSolicitacao')
            -> addSelect('SetorSolicitacao')

            -> leftJoin('d.categoria', 'CategoriaSolicitacao')
            -> addSelect('CategoriaSolicitacao')

            -> where('d.id = :id')
            -> setParameter('id', $id)
            -> getQuery();

        $query->execute();

        return $toArray ? current($query->getArrayResult()) : $query->getSingleResult();
    }

    static public function buscarMaximaSequencia( $ano, Bool $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Solicitacao' )
            -> createQueryBuilder('Solicitacao');

        $query = $query->select('MAX(Solicitacao.sequencia)')
            -> where('Solicitacao.ano = :ano')
            -> setParameter('ano', $ano)
            -> getQuery();

        return $query->getSingleScalarResult();
    }

    static function adicionar($dados)
    {

        $nro = self::buscarMaximaSequencia(date('Y'));
        if ($nro === null) {
            $nro = 1;
        } else {
            $nro = $nro + 1;
        }

        $solicitacao = new Solicitacao();

        $solicitacao -> setSequencia( $nro );
        $solicitacao -> setAno( date('Y') );
        $solicitacao -> setEspecificacao( $dados['especificacao'] );
        $solicitacao -> setStatus( SolicitacaoFactory::ST_DIGITADA );  
        $solicitacao -> setUnidade( UnidadesFactory::getById($dados['centro'], false) );
        $solicitacao -> setSetor( SetoresFactory::getById($dados['setor'], false) );
        $solicitacao -> setCategoria( CategoriasFactory::getById($dados['categoria'], false) );
        $solicitacao -> setBeneficiado ( UsuarioFactory::getByCpf1($dados['beneficiado'], false));
        $solicitacao -> setResponsavel ( UsuarioFactory::getByCpf1($dados['responsavel'], false));
        $solicitacao -> setCadastrador ( UsuarioFactory::getByCpf1($dados['cadastrador'], false));   
        $solicitacao -> setDtHrInclusao( date("Y-m-d H:i:s"));
        $solicitacao -> setDtHrDevolucao( substr($dados['dtDevolucao'], 6, 4) .'-'. substr($dados['dtDevolucao'], 3, 2) . '-'. substr($dados['dtDevolucao'], 0, 2) . " 23:59:59");
        $solicitacao -> setObservacao( $dados['observacao']);

        $GLOBALS['em'] -> persist( $solicitacao );
        
        try {
            $GLOBALS['em'] -> flush();
            return $solicitacao;
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Erro ao inserir.');
        }
    }

    static function atualizar($dados)
    {

        $solicitacao = self::getById($dados['id']);
        $solicitacao -> setDtHrDevolucao( substr($dados['dtDevolucao'], 6, 4) .'-'. substr($dados['dtDevolucao'], 3, 2) . '-'. substr($dados['dtDevolucao'], 0, 2) . " 23:59:59");
        
        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Erro ao atualizar.');
        }
    }

    static function atualizarStatus($dados)
    {

        $solicitacao = self::getById($dados['id']);
        $solicitacao -> setStatus($dados['status']);
        $observacao = $solicitacao->getObservacao();
        $solicitacao -> setObservacao($dados['observacao'] . $observacao);

        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Erro ao atualizar status.');
        }
    }

    static function remover( Int $id )
    {
        $solicitacao = self::getById($id);
    
        $GLOBALS['em'] -> remove( $solicitacao );
        $GLOBALS['em'] -> flush();
    }
}
