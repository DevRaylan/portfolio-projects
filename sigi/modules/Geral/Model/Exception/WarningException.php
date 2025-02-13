<?php
namespace Geral\Model\Exception;

use \Geral\Model\Exception\FrameworkException;

/**
 * Exceção de warnings
 *
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 */
class WarningException extends FrameworkException
{
    public function __construct($code, $app = null)
    {
        $this -> prefix = 'W';
        parent::__construct($code, $app);
    }
}

