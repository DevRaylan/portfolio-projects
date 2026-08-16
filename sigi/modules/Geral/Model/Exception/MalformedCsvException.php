<?php
namespace Geral\Model\Exception;

/**
 * Exceção lançada quando for detectado um CSV mal formado durante 
 * o processo de conversão para o formato CSV usando o método
 * <code>FormatConverter::getResult</code>.
 *
 * @author Wagner Saback Dantas &lt;wagner.dantas@udesc.br&gt;
 * @see \Geral\Model\FormatConverter::getResult
 */
class MalformedCsvException extends FormatConverterException
{
    public function __construct($errDetail = "")
    {
        $errMsg = "CSV mal formado (".$errDetail.")";
        parent::__construct($errMsg, 3, null);
    }
}

