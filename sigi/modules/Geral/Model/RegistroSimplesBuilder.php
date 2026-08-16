<?php

namespace Geral\Model;

use \Geral\Model\Exception\ErrorException;

abstract class RegistroSimplesBuilder
{
    const REGISTRO_SIMPLES_TIPOS = [
        "Setor",
        "Pais",
        "Erros"
    ];

    public function __construct()
    { }

    static public function getEntidadePorTipo($tipo)
    {
        $registro = null;
       
        // Converte para maiúsculas o primeiro caractere de cada palavra 
        $tipoNormalizado = ucwords($tipo);

        if(in_array($tipoNormalizado, self::REGISTRO_SIMPLES_TIPOS)) {
            $nomeClasse = "Geral\\Entity\\" . $tipoNormalizado;
            $registro = new $nomeClasse();
        }else{
            throw new ErrorException("Erro ao instanciar a entidade.");
        }

        return $registro;
    }

    static public function getTiposRegistrosSimples()
    {
        return REGISTRO_SIMPLES_TIPOS;
    }

    static public function getClassePorTipo($tipo){
        // Converte para maiúsculas o primeiro caractere de cada palavra 
        $tipoNormalizado = ucwords($tipo);

        if(in_array($tipoNormalizado, self::REGISTRO_SIMPLES_TIPOS)) {
            $nomeClasse = "Geral\\Entity\\" . $tipoNormalizado;
            return $nomeClasse;
        }else{
            throw new ErrorException("Erro ao localizar a classe pelo tipo.");
        }
    }
}