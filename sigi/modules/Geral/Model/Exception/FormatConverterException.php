<?php
namespace Geral\Model\Exception;

use Exception;

/**
 * Exceção genérica lançada quando de um erro de conversão para o
 * formato CSV usando o método <code>FormatConverter::getResult</code>.
 *
 * @author Wagner Saback Dantas &lt;wagner.dantas@udesc.br&gt;
 * @see \Geral\Model\FormatConverter::getResult
 */
class FormatConverterException extends Exception
{
    public function __construct($message = null, $code = 1, $previous = null)
    {
        $baseMessage = "Erro de conversão para o formato CSV";
        $customMessage = $message == null ? $baseMessage : "{$baseMessage}: {$message}";
        parent::__construct($customMessage, $code, $previous);
    }
    
    public function __toString()
    {
        return __CLASS__ . " ({$this->code}) {$this->message}\n";
    }
}

