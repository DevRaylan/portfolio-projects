<?php
namespace Geral\Model\Exception;

/**
 * Exceção lançada quando for informada uma opção desconhecida de 
 * conversão para o formato CSV usando o método
 * <code>FormatConverter::getResult</code>.
 *
 * @author Wagner Saback Dantas &lt;wagner.dantas@udesc.br&gt;
 * @see \Geral\Model\FormatConverter::getResult
 */
class UnknownOptionException extends FormatConverterException
{
    public function __construct()
    {
        parent::__construct("Opção desconhecida de conversão para o formato CSV", 2, null);
    }
}

