<?php

namespace Geral\Entity;

/** @MappedSuperclass */
class RegistroSimples
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     **/
    private $id;

    /** @Column(type="string", length=255, nullable=false) **/
    private $descricao;

    public function __construct()
    { }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * Se a subclasse possuir atributo adicional, este metodo deve ser sobrescrito.
     */
    public function setAtributos($atributos = [])
    {
        if (empty($atributos)) {
            return;
        }

        $this->setDescricao(trim($atributos[0]));
    }

    public function getAtributos()
    {
        return array($this->getDescricao());
    }
}

