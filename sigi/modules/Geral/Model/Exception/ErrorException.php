<?php
namespace Geral\Model\Exception;

use \Geral\Model\Exception\FrameworkException;

/**
 * Exceção de errors
 *
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 */
class ErrorException extends FrameworkException
{
    public function __construct($code, $app = null)
    {
        $this -> prefix = 'E';
        parent::__construct($code, $app);
    }
}
