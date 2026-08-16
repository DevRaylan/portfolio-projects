<?php

namespace Geral\Entity;

use Geral\Model\Factory\LocalidadeIbgeFactory;

/** @Entity */
class Municipio
{

    /**
     * @Id @Column(type="integer")
     **/
    private $id;

    /** @Column(type="string", length=255, nullable=false) **/
    private $nome;

    /** @Column(type="string", length=255, nullable=false) **/
    private $microregiao;

    /** @Column(type="string", length=255, nullable=false) **/
    private $mesoregiao;

    /* (A)tivo ou (I)nativo */
    /** @Column(type="string", length=1) **/
    private $status;

    /**
     * @ManyToOne(targetEntity="\Geral\Entity\Estado")
     * @JoinColumn(name="estado_id", referencedColumnName="id")
     */
    private $estado;

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

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    public function getMicroregiao()
    {
        return $this->microregiao;
    }


    public function setMicroregiao($microregiao)
    {
        $this->microregiao = $microregiao;

        return $this;
    }

    public function getMesoregiao()
    {
        return $this->mesoregiao;
    }

    
    public function setMesoregiao($mesoregiao)
    {
        $this->mesoregiao = $mesoregiao;

        return $this;
    }

    public function setAtributos($atributos = [])
    {
        if (empty($atributos)) {
            return;
        }

        // Localiza o estado
        $estado = LocalidadeIbgeFactory::getEstadoPorId($atributos['microrregiao']['mesorregiao']['UF']['id']);

        $this->setId(trim($atributos['id']));
        $this->setNome(trim($atributos['nome']));
        $this->setStatus("A");// Ativo
        $this->setMicroregiao(trim($atributos['microrregiao']['nome']));
        $this->setMesoregiao(trim($atributos['microrregiao']['mesorregiao']['nome']));
        $this->setEstado($estado);
    }

}
