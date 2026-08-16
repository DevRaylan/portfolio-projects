<?php
namespace Geral\Model;

/**
 * Classe para acessar dados da requisição
 * @author Glauco David Laicht
 */
class Request{

    use Traits\RequestInput,
        Traits\RequestInputType,
        Traits\RequestMethod;

    protected $params;
    protected $method;

    public function __construct(){
        $this->params = new RequestParams();
        $this->method = $_SERVER['REQUEST_METHOD'];
    }
}