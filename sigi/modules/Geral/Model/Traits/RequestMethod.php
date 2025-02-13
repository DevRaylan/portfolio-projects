<?php
namespace Geral\Model\Traits;

/**
 * Trait para acessar informações sobre o método da requisição
 * @author Glauco David Laicht
 */
trait RequestMethod{

    /**
     * Retorna o método da request
     * @return string
     */
    public function getMethod(){
        return $this->method;
    }

    /**
     * Retorna se o método da requisição é GET
     * @return bool
     */
    public function isGet(){
        return $this->getMethod() == 'GET';
    }

    /**
     * Retorna se o método da requisição é POST
     * @return bool
     */
    public function isPost(){
        return $this->getMethod() == 'POST';
    }
}