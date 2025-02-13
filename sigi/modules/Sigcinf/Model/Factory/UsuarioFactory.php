<?php
namespace Sigcinf\Model\Factory;

use \Sigcinf\Entity\Usuario;
use \Geral\Model\Exception\ErrorException;

abstract class UsuarioFactory
{
    public function __construct( ) {}

    static public function getAll(Bool $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Usuario' ) 
            -> createQueryBuilder('Usuario')

            -> leftJoin('Usuario.unidade', 'Unidades')
            -> addSelect('Unidades')

            -> leftJoin('Usuario.setor', 'Setores')
            -> addSelect('Setores')

            -> getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();


    }

    static public function getById($id, $toArray = false)
    {
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Usuario' ) 
            -> createQueryBuilder('d')
            -> select('d')
            -> where('d.id = :id')
            -> setParameter('id', $id)
            -> getQuery();

        $query->execute();

        return $toArray ? current($query->getArrayResult()) : $query->getSingleResult();
    }

    static public function getByCpf1($cpf, $toArray = false)
    {
        
        $query = $GLOBALS['em'] 
            -> getRepository( '\Sigcinf\Entity\Usuario' ) 
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
            -> getRepository( '\Sigcinf\Entity\Usuario' ) 
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
            -> getRepository( '\Sigcinf\Entity\Usuario' ) 
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

        $usuario = new Usuario();
        $usuario -> setUnidade( UnidadesFactory::getById($dados['centro'], false) );

        if ($dados['setor'] <> '') {
            $usuario -> setSetor( SetoresFactory::getById($dados['setor'], false) );
        }
        
        $usuario -> setNome( $dados['nome'] );
        $usuario -> setCpf( $dados['cpf'] );
        $usuario -> setEmail( $dados['email'] );

        $GLOBALS['em'] -> persist( $usuario );
        
        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Erro ao adicionar usuário.');
        }
    }

    static function atualizar( $dados )
    {
        $usuario = self::getById($dados['id']);

        $usuario -> setUnidade( UnidadesFactory::getById($dados['centro'], false) );

        if ($dados['setor'] <> '') {
            $usuario -> setSetor( SetoresFactory::getById($dados['setor'], false) );
        }

        $usuario -> setNome( $dados['nome'] );
        $usuario -> setCpf( $dados['cpf'] );
        $usuario -> setEmail( $dados['email'] );

        try {
            $GLOBALS['em'] -> flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ErrorException('Erro ao atualizar usuário.');
        }
    }

    static function remover( Int $id )
    {
        $usuario = self::getById($id);
    
        $GLOBALS['em'] -> remove( $usuario );
        $GLOBALS['em'] -> flush();
    }
}
