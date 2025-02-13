<?php
namespace Geral\Controller;

use \Geral\Model\Config;
use \Geral\Model\Validacao;

class MessageController extends AbstractPublicController
{
    public function __construct( )
    {
        parent::__construct( );
    }

    public function index( )
    {
        $this -> url -> redirect( 'Install', 'formDados' );
    }
    
    public function error( )
    {
        $this->view();
    }
}
