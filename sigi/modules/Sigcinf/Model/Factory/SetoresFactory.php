<?php
namespace Sigcinf\Model\Factory;

use \Sigcinf\Entity\Setores;
use \Geral\Model\Exception\ErrorException;

abstract class SetoresFactory
{
    public function __construct( ) {}

    static public function getAll(Bool $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Setores' ) 
            -> createQueryBuilder('Setores')

            -> leftJoin('Setores.unidade', 'Unidades')
            -> addSelect('Unidades')

            -> getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();


    }

    static public function getById($id, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Setores' ) 
            -> createQueryBuilder('d')
            -> select('d')
            -> where('d.id = :id')
            -> setParameter('id', $id)
            -> getQuery();

        $query->execute();

        return $toArray ? current($query->getArrayResult()) : $query->getSingleResult();
    }

    static public function getByUnidade($unidade, $toArray = false)
    {
        
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Setores' ) 
            -> createQueryBuilder('d')
            -> select('d')

            -> leftJoin('d.unidade', 'Unidades')
            -> addSelect('Unidades')

            -> where('Unidades.id = :id')
            -> setParameter('id', $unidade)     
            
            -> getQuery();

        $query->execute();            

        return $toArray ? $query->getArrayResult() : $query->getResult();
        

    }

    static public function getByCpf1($cpf, $toArray = false)
    {
        
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Setores' ) 
            -> createQueryBuilder('d')
            -> select('d')

            -> leftJoin('d.unidade', 'Unidades')
            -> addSelect('Unidades')

            -> where('d.cpf = :cpf')
            -> setParameter('cpf', $cpf)     
            
            -> getQuery();

        $query->execute();            

        return $toArray ? current($query->getArrayResult()) : $query->getSingleResult();
        

    }

    static public function getByCpf3($cpf, $toArray = false)
    {
        
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Setores' ) 
            -> createQueryBuilder('d')
            -> select('d')

            -> leftJoin('d.unidade', 'Unidades')
            -> addSelect('Unidades')

            -> where('d.cpf = :cpf')
            -> setParameter('cpf', $cpf)     
            
            -> getQuery();

        $query->execute();            

        return $toArray ? $query->getArrayResult() : $query->getResult();
        

    }
    


    static public function getByCpf2($busca, $toArray = false)
    {
        
        if (is_numeric($busca)) {
            $cpf = $busca;
            $nome = '';
        } else {
            $cpf = '';
            $nome = $busca . '%';
        }

        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Setores' ) 
            -> createQueryBuilder('d')
            -> select('d')

            -> leftJoin('d.unidade', 'Unidades')
            -> addSelect('Unidades')

            -> where('d.cpf = :cpf')
            -> setParameter('cpf', $cpf)     

            -> orWhere('d.nome like :nome')
            -> setParameter('nome',  $nome)
            
            -> getQuery();

            return $toArray ? $query->getArrayResult() : $query->getResult();

    }    

    static function adicionar($dados)
    {

        $setor = new Setores();
        $setor -> setUnidade( UnidadesFactory::getById($dados['centro'], false) );
        $setor -> setNome( $dados['nome'] );
        $setor -> setEmail( $dados['email'] );

        $GLOBALS['em'] -> persist( $setor );
        
        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Erro ao adicionar usuário.');
        }
    }

    static function atualizar( $dados )
    {
        $setor = self::getById($dados['id']);

        $setor -> setUnidade( UnidadesFactory::getById($dados['centro'], false) );
        $setor -> setNome( $dados['nome'] );
        $setor -> setEmail( $dados['email'] );

        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Erro ao atualizar usuário.');
        }
    }

    static function remover( Int $id )
    {
        $setor = self::getById($id);
    
        $GLOBALS['em'] -> remove( $setor );
        $GLOBALS['em'] -> flush();
    }
}
