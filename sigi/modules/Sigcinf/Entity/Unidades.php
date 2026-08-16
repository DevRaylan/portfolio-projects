<?php

namespace Sigcinf\Entity;

/** @Entity */
class Unidades
{
    // Propriedas sem relacionamento

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(type="string", length=60) */
    private $nome;

    /** @Column(type="string", length=10) */
    private $abrev;

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

    public function getAbrev()
    {
        return $this->abrev;
    }

    public function setAbrev($abrev)
    {
        $this->abrev = $abrev;
        return $this;
    }

}