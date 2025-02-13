<?php

namespace Sigcinf\Entity;

/** @Entity */
class Categorias
{
    // Propriedas sem relacionamento

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(type="string", length=60) */
    private $nome;

    // Construtor
    public function __construct()
    {
        
    }

    //Get
    public function getId()
    {
        return $this->id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

}