<?php
namespace Geral\Model\Factory;

use \Geral\Model\Config;
use \Geral\Model\Interfaces\IAuthorizer;
use \Geral\Model\AuthorizerConfig;
use \Geral\Model\AuthorizerWebServer;
use \Geral\Model\AuthorizerLocal;

/*
 * AuthorizerFactory.php
 *
 */

abstract class AuthorizerFactory
{
    static public function create($authScheme = 'userId')
    {
        $authorizerType = '\Geral\Model\Authorizer'.Config::getConstant('AUTHORIZER_TYPE');

        if ( class_exists( $authorizerType ) ) {
            // Instancia a classe com nome igual ao valor de $classLogin
            $authorizer = new $authorizerType($authScheme);
        } else {
            throw new \Exception('Classe referente ao tipo de autorizador não encontrado.');
        }

        // Verificar se a classe atende ao contrato(interface)
        if ( !$authorizer instanceof IAuthorizer ) {
            throw new \Exception('Tipo de Authorizer não está implementando a interface IAuthorizer: '.$authorizerType);
        }

        return $authorizer;
    }
}
