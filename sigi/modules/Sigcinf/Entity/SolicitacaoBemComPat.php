<?php

namespace Sigcinf\Entity;

/** @Entity */
class SolicitacaoBemComPat
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @ManyToOne(targetEntity="\Sigcinf\Entity\BemComPat", inversedBy="SolicitacaoBemComPatList") */
    private $bemcompat;

    /** @ManyToOne(targetEntity="\Sigcinf\Entity\Solicitacao", inversedBy="SolicitacaoBemComPatList") */
    private $solicitacao;

    /** @Column(type="string", length=200, nullable=true) */
    private $observacao;

    // Construtor
    public function __construct()
    {}

    //Set
    public function setBemComPat($bemcompat)
    {
        $this->bemcompat = $bemcompat;
        return $this;
    }

    public function setSolicitacao($solicitacao)
    {
        $this->solicitacao = $solicitacao;
        return $this;
    }

    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;
        return $this;
    }

    //Get
    public function getId()
    {
        return $this->id;
    }

    public function getBemComPat()
    {
        return $this->bemcompat;
    }

    public function getSolicitacao()
    {
        return $this->solicitacao;
    }

    public function getObservacao()
    {
        return $this->observacao;
    }

}
