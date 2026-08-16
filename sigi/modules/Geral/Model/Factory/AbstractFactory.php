<?php

namespace Geral\Model\Factory;

use Geral\Model\Factory\AuditFactory;
/*
 * AbstractFactory.php
 *
 * Factory - Criação, gravação e consulta de objetos
 *
 */

abstract class AbstractFactory
{

    // Retorna um objeto da classe
    // Recebe um array com os parâmetros no formato 'id' => '0001'
    //static public function create($a, $b, $c, $d) {}

    // Persiste um objeto no banco
    // Recebe como parâmetro um objeto da classe
    static public function add($obj)
    {
        if (!is_object($obj)) {
            throw new \Exception("Parâmetro não é um objeto.");
        }

        try {
            $GLOBALS['em']->persist($obj);
            $GLOBALS['em']->flush();
            return $obj;
        } catch (\Exception $e) {
            throw new \Exception("Erro ao salvar objeto no banco de dados => " . $e->getMessage());
        }
    }

    // Retorna a lista dos objetos que possuem o critério especificado.
    // Exemplo de criterio(array): 'id' => '1234'
    //abstract public function find ($criterios);

}
