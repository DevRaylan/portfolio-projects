<?php

namespace Sigcinf\Entity;

/** @Entity */
class SolicitacaoBemSemPat
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @ManyToOne(targetEntity="\Sigcinf\Entity\BemSemPat", inversedBy="SolicitacaoBemSemPatList") */
    private $bemsempat;

    /** @ManyToOne(targetEntity="\Sigcinf\Entity\Solicitacao", inversedBy="SolicitacaoBemSemPatList") */
    private $solicitacao;

    /** @Column(type="string", length=200, nullable=true) */
    private $observacao;

    // Construtor
    public function __construct()
    {}

    //Set
    public function setBemSemPat($bemsempat)
    {
        $this->bemsempat = $bemsempat;
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

    public function getBemSemPat()
    {
        return $this->bemsempat;
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
