<?php
namespace Geral\Model;

use Geral\Model\Interfaces\LoginInterface;

class LoginTrue implements LoginInterface
{
    public function autenticar( $id, $password )
    {
        // Autenticar usuário e retorno true/false
        $this -> id = $id;
        return true;
    }
}
