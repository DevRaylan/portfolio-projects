<?php

namespace Geral\Entity;

use Geral\Entity\RegistroSimples;

/** @Entity */
class Setor extends RegistroSimples
{

    /** @Column(type="string", length=255) **/
    private $sigla;

    public function __construct()
    { }

    public function getSigla()
    {
        return $this->sigla;
    }

    public function setSigla($sigla)
    {
        $this->sigla = $sigla;
    }

    public function setAtributos($atributos = [])
    {
        if (empty($atributos)) {
            return;
        }

        $this->setSigla(trim($atributos[0]));
        $this->setDescricao(trim($atributos[1]));
    }

    public function getAtributos()
    {
        return array($this->getSigla(), $this->getDescricao());
    }
}
