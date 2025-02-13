<?php

namespace Geral\Model\Factory;

use Geral\Model\Exception\ErrorException;
use Geral\Model\Interfaces\UserDataInterface;
use \Geral\Model\UserData;
use Geral\Model\Services\UserDataProxyService;
use Geral\Model\Interfaces\UserDataQueryInterface;
use Geral\Model\UserDataQuery;

abstract class UserDataFactory
{
    public const VINCULO_PROFESSOR      = 'PROFESSOR';
    public const VINCULO_ALUNO          = 'ALUNO';
    public const VINCULO_TECNICO        = 'TECNICO';
    public const VINCULO_FC             = 'FUNCAO CHEFIA';
    public const VINCULO_ESTAGIARIO     = 'ESTAGIARIO';
    public const VINCULO_TERCEIRIZADO   = 'TERCEIRIZADO';
    public const VINCULO_EXTENSAO       = 'EXTENSAO';
    public const VINCULO_AFASTADO       = 'AFASTADO';
    public const VINCULO_APOSENTADO     = 'APOSENTADO';
    public const VINCULO_EXONERADO      = 'EXONERADO';

    public const UNIDADE_REITORIA  =   'REITORIA';
    public const UNIDADE_CEART     =   'CEART';
    public const UNIDADE_CEFID     =   'CEFID';
    public const UNIDADE_ESAG      =   'ESAG';
    public const UNIDADE_FAED      =   'FAED';
    public const UNIDADE_CAV       =   'CAV';
    public const UNIDADE_CCT       =   'CCT';
    public const UNIDADE_CEAD      =   'CEAD';
    public const UNIDADE_CEAVI     =   'CEAVI';
    public const UNIDADE_CEO       =   'CEO';
    public const UNIDADE_CEPLAN    =   'CEPLAN';
    public const UNIDADE_CERES     =   'CERES';
    public const UNIDADE_CESFI     =   'CESFI';

    private static $userDataService;
    private static $userDataQuery;

    private static function getInstance()
    {
        if(empty(self::$userDataService)) {
            self::$userDataService = new UserDataProxyService();
        }

        return self::$userDataService;
    }

    //=== Consultas ============================================================

    static function getByCpf($cpf, $vinculoProcurado = null, $toArray = false, $userDataServiceOptions=null)
    {
        $vinculos = self::getInstance()->getVinculosByCpf($cpf, $userDataServiceOptions);
        $vinculos = self::toCollectionObject($vinculos);
        $vinculo  = self::selVinculo($vinculos, $vinculoProcurado);

        if(empty($vinculo)) {
            throw new ErrorException('Não foi possível encontrar os dados sobre o usuário "'.$cpf.'".');
        }

        return $toArray ? self::toArray($vinculo) : $vinculo;
    }

    static function getVinculosByCpf($cpf, $userDataServiceOptions=null)
    {
        $keys = ['vinculo' => '', 'cargo' => '', 'funcao' => '', 'isPrincipal' => ''];
        $vinculosArray = [];

        foreach(self::getInstance()->getVinculosByCpf($cpf, $userDataServiceOptions) as $vinculo) {
            $dados = array_intersect_key($vinculo, $keys);
            
            $vinculosArray[] = array_intersect_key($vinculo, $keys);
        }

        return $vinculosArray;
    }

    static function getAll($toArray = false)
    {   
        self::createQuery();

        return self::executeQuery($toArray);

    }

    // Query builder ===========================================================

    public static function createQuery() : UserDataQueryInterface
    {
        self::$userDataQuery = new UserDataQuery();

        return self::$userDataQuery;
    }

    public static function executeQuery($toArray = false)
    {
        $dadosUsuario = self::getInstance()->executeQuery(self::$userDataQuery);

        return $toArray ? $dadosUsuario : self::toCollectionObject($dadosUsuario);
    }

    // Funções auxiliares ======================================================

    private static function toObject($data)
    {
        if(!empty($data))
        {            
            $userData = new UserData;

            foreach($userData::ATRIBUTOS_DO_USUARIO as $from => $to) {                
                $func = 'set'.ucwords($to);
                $userData->$func($data[$to]);
            }
        } else {
            $userData = null;
        }

        return $userData;
    }

    private static function toCollectionObject($data) : Array
    {
        $collections = [];

        foreach($data as $_data) {
            $collections[] = self::toObject($_data);
        }

        return $collections;
    }

    public static function toArray(UserData $userData)
    {
        $output = [];

        foreach($userData::ATRIBUTOS_DO_USUARIO as $column) {
            $func = 'get'.ucwords($column);            
            $output[$column] = $userData->$func();
        }

        return $output;
    }

    static private function selVinculo(Array &$vinculos, String $vinculoProcurado = null)
    {
        if(!empty($vinculoProcurado)) {
            if(!in_array($vinculoProcurado, UserDataInterface::VINCULOS_ESPERADOS)) {
                throw new ErrorException('Nome de vínculo "'.$vinculoProcurado.'" não suportado.');
            }
        }

        foreach($vinculos as &$vinculo) {
            if($vinculo->getIsPrincipal() && !$vinculoProcurado ||
               $vinculo->getVinculo() == $vinculoProcurado) {
               return $vinculo;
            }
        }
    }
}