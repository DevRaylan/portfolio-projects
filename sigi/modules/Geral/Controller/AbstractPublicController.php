<?php
/**
 * @class AbstractPublicController
 *
 * Esta classe permite que controllers especializados a partir desta tenham o acesso aos seus respectivos métodos 
 * sem precisar de uma sessão de usuário.
 *
 * @version 2.0
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 **/
namespace Geral\Controller;

use \Geral\Model\Session;

abstract class AbstractPublicController extends AbstractController
{
    // Iniciar as variáveis do controller
    public function __construct( )
    {
        parent::__construct();

        /**
         * Variável utilizada para negar acesso a dados de usuários em controllers estendidos.
         * Assim evita-se que sejam divulgadas informações sensíveis no ambiente de desenvolvimento em acessos sem sessão.
         */
        if($_SERVER['AMBIENTE'] == 'DEV') {
            Session::setParam('simularDadosRequerido', true);
        }
    }
}
?>
