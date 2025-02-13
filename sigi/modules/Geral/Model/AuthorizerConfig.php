<?php
namespace Geral\Model;

use \Geral\Model\AbstractAuthorizer;
use \Geral\Model\Config;

class AuthorizerConfig extends AbstractAuthorizer {
    public function __construct($authScheme = 'userId')
    {
        if (! ( $authScheme != null && is_string($authScheme) && in_array($authScheme, parent::validAuthSchemes) ) ) {
            die('AuthorizerConfig: Esquema de autenticação inválido.');
        }

        parent::__construct(Config::getValue('baseAuthorizer'), $authScheme);
    }
}
