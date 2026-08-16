<?php
/**
 * Para futura implementação, ainda deve ser melhor analisado
 */
namespace Geral\Controller;

class ErrorController extends AbstractPrivateController
{
    public function __construct( )
    {
        parent::__construct( );
    }

    public function index( ) { }
    
    public function accessDenied( ) {
        $this -> view();
    }
    
    public function pageNotFound( ) {
        $this -> view();
    }
}
