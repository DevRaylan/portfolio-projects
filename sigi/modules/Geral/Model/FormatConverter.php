<?php
/**
 * Converter o conteúdo de uma variável em uma string com um determinado padrão,
 * Por exemplo, converter uma array para uma string no formato CSV ou JSON.
  *
 * @version 1.1
 * @author Jean Carlos Oliveira de Abreu &lt;jean.abreu@udesc.br&gt;
 * @author Wagner Saback Dantas &lt;wagner.dantas@udesc.br&gt;
 */

namespace Geral\Model;

use Geral\Model\Exception\MalformedCsvException;
use Geral\Model\Exception\UnknownOptionException;

class FormatConverter
{
    /**
    * Tipos suportados, também realiza o mapeamento do tipo para a função de conversão
    * correspondente.
    */
    static $supportedTypes = [
        'json' => 'toJson',
        'csv' => 'toCsv'
    ];
    
    // Utilizado para a seleção da função de conversão
    private $outputType;
    
    function __construct( $outputType )
    {
        // Verifica se o tipo é suportado
        if(!isset(self::$supportedTypes[$outputType])) {
            throw new \Exception("Formato de conversão não suportado.", 1);
        }
        
        $this -> outputType = $outputType;
    }
    
    private function matchArrays($arr1, $arr2, $opc = 'keyToColumn')
    {
        if ($opc == 'keyToColumn') {
            if (array_keys($arr1) !== $arr2) {
                throw new MalformedCsvException("Linha com definição de coluna diferente do cabeçalho CSV");
            }
        } else { //$opc == 'firstRowToColumn'
            if (count($arr1) != count($arr2)) {
                throw new MalformedCsvException("Número de linhas diferente do número de colunas");
            }
        }
        
        return true;
    }
    
    private function escapeCsvValues($values)
    {
        return array_map(
            function($value) {                
                return '"'.preg_replace('/"/','""', $value).'"';
            },
            $values
        );
    }
    
    private function csvEntryToString($csvDataEntry, $csvColNames = null, $opc = 'keyToColumn')
    {
        if (is_array($csvDataEntry)) {
            if (empty($csvColNames)) {
                return implode(',', $this -> escapeCsvValues($csvDataEntry)); 
            } else {
                $this -> matchArrays($csvDataEntry, $csvColNames, $opc);
                return implode( ',', $this -> escapeCsvValues($csvDataEntry) );
            }
        } else {
            throw new MalformedCsvException("Linha de dados não reconhecida");
        }
    }
    
    /**
    * Converter um array para texto no formato CSV.
    * 
    * Formato da saída em CSV:
    * <ul>
    * <li>Campos do CSV separados por vírgula;</li>
    * <li>Campos do CSV cercados por aspas duplas.</li>
    * </ul>
    * 
    * @param $val Mixed Variável com o conteúdo para conversão
    * @param $opc String <p>Opções para tratamento da variável, as opções possíveis são:
    * <ul>
    * <li>"keyToColumn" => Utilizar as keys do conjunto como coluna</li>
    * <li>"firstRowToColumn" => Utilizar os valores do primeiro conjunto como coluna</li>
    * </ul>
    * </p>
    * @throws UnknownOptionException se $opc não for um valor conhecido.
    * @throws MalformedCSVException se for detectado um CSV mal formado durante a conversão.
    */
    private function toCsv($val, $opc = 'keyToColumn')
    {
        if(empty($val)) return;
        
        if($opc == 'keyToColumn') {
            $csvColNames = array_keys(reset($val));
        } else if ($opc == 'firstRowToColumn') {
            $csvColNames = array_shift($val);
        } else {
            throw new UnknownOptionException();
        }
        
        $csvHeaderEntry[] = $this -> csvEntryToString($csvColNames);
        $csvDataEntries = array_map(
            function($csvDataEntry) use ($csvColNames, $opc) {
                return $this -> csvEntryToString($csvDataEntry, $csvColNames, $opc);
            },
            $val
        );        
        $csv = array_merge($csvHeaderEntry, $csvDataEntries);
        
        $csvString = array_reduce(
            $csv,
            function ($carry, $item) {
                return empty($carry) ? $item : $carry . "\n" . $item;
            });
        
        return $csvString;
    }
    
    /**
    * Converter um array para texto no formato JSON.
    *
    * @param $val Mixed Variável com o conteúdo para conversão
    * @param $formatOuput Boolean Formatar ou não o texto de saída (indentação)
    */
    private function toJson($val, $formatOuput = true)
    {
        if(empty($val)) return;
        
        return $formatOuput ? json_encode($val, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : json_encode($val);
    }
    
// INTERFACES ==================================================================
    /**
    * Converte um conteúdo para um determinado formato de saída.
    * 
    *  @param Mixed $val conteúdo a ser convertido
    *  @param String $opc opção de conversão de formato (opções possíveis: "json" ou "csv"; default: "json")
    *  @throws \Exception exceção lançada quando ocorre algum erro durante a conversão
    */
    public function getResult($val, $opc = 'json')
    {
        try {
            if(empty($opc)) {
                return $this -> {self::$supportedTypes[$this -> outputType]}($val);
            } else {
                return $this -> {self::$supportedTypes[$this -> outputType]}($val, $opc);
            }
        } catch (\Exception $e) {
            // Encaminhar o Exception para tratamento.
            throw $e;
        }
    }
}
