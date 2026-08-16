<?php

namespace Geral\Entity;

use Geral\Model\Factory\LocalidadeIbgeFactory;

/** @Entity */
class Estado
{

    /**
     * @Id @Column(type="integer")
     **/
    private $id;

    /** @Column(type="string", length=255, nullable=false) **/
    private $nome;

    /** @Column(type="string", length=2, nullable=false) **/
    private $sigla;

    /**
     * @ManyToOne(targetEntity="\Geral\Entity\Pais")
     * @JoinColumn(name="pais_id", referencedColumnName="id")
     */
    private $pais;


    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
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

    public function getSigla()
    {
        return $this->sigla;
    }

    public function setSigla($sigla)
    {
        $this->sigla = $sigla;

        return $this;
    }
    public function getPais()
    {
        return $this->pais;
    }


    public function setPais($pais)
    {
        $this->pais = $pais;

        return $this;
    }

    public function setAtributos($atributos = [])
    {
        if (empty($atributos)) {
            return;
        }

        $this->setId(trim($atributos['id']));
        $this->setNome(trim($atributos['nome']));
        $this->setSigla(trim($atributos['sigla']));
        $this->setPais($atributos['pais']);
    }
}
