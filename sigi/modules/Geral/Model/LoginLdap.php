<?php

namespace Geral\Model;

use Geral\Model\Interfaces\LoginInterface;

class LoginLdap implements LoginInterface
{
    public function autenticar($id, $password)
    {
        $ldapConnector = new ConnLdap();

        try{
            $userLocalized = $ldapConnector->getUserLdap($id);
        } catch (\Exception $e) {
            return false;
        }

        if ($userLocalized) {
            // Autenticar usuário e retorno true ou false

            return true;



            return $ldapConnector->autenticar($id, $password);
        } else {
            return false;
        }
    }
}
