<?php

namespace Sigcinf\Entity;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 **/
class Solicitacao
{

    /**
     * @Column( type = "integer" ) @Id @GeneratedValue
     **/
    protected $id;

    /**
     * @Column( type = "integer", nullable = false, options = { "comment" : "Numero sequencial" } )
     */
    protected $sequencia;

    /**
     * @Column( type = "integer", nullable = false, options = { "comment" : "Ano" } )
     */
    protected $ano;

    /**
     * @Column( type = "string", length=200, nullable = false, options = { "comment" : "Especificacao" } )
     */
    protected $especificacao;
  

    /**
     * @Column( type = "string", nullable = true, options = { "comment" : "Status da solicitação" } )
     */
    protected $status;

    /**
     * @ManyToOne( targetEntity = "Usuario" )
     */
    protected $beneficiado;

    /**
     * @ManyToOne( targetEntity = "Usuario" )
     */
    protected $responsavel;

    /**
     * @ManyToOne( targetEntity = "Usuario" )
     */
    protected $cadastrador;

    /**
     * @Column( type = "string", length=20, nullable = false, options = { "comment" : "data e hora que incluiu a solicitacao" } )
     */
    protected $dtHrInclusao;

    /**
     * @Column( type = "string", length=20, nullable = false, options = { "comment" : "data e hora que incluiu a solicitacao" } )
     */
    protected $dtHrDevolucao;

    /**
     * @ManyToOne( targetEntity = "Unidades" )
     */
    protected $unidade;

    /**
     * @ManyToOne( targetEntity = "Setores" )
     */
    protected $setor;

    /**
     * @ManyToOne( targetEntity = "Categorias" )
     */
    protected $categoria;

    /**
     * @Column( type = "string", length=200, nullable = true, options = { "comment" : "Observacao" } )
     */
    protected $observacao;  


    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSequencia()
    {
        return $this->sequencia;
    }

    public function setSequencia($sequencia)
    {
        return $this->sequencia = $sequencia;
    }

    public function getAno()
    {
        return $this->ano;
    }

    public function setAno($ano)
    {
        return $this->ano = $ano;
    }

    public function getEspecificacao()
    {
        return $this->especificacao;
    }

    public function setEspecificacao($especificacao)
    {
        return $this->especificacao = $especificacao;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        return $this->status = $status;
    }

    public function getBeneficiado()
    {
        return $this->beneficiado;
    }

    public function setBeneficiado($beneficiado)
    {
        return $this->beneficiado = $beneficiado;
    }

    public function getResponsavel()
    {
        return $this->responsavel;
    }

    public function setResponsavel($responsavel)
    {
        return $this->responsavel = $responsavel;
    }

    public function getCadastrador()
    {
        return $this->cadastrador;
    }

    public function setCadastrador($cadastrador)
    {
        return $this->cadastrador = $cadastrador;
    }

    public function getDtHrInclusao()
    {
        return $this->dtHrInclusao;
    }

    public function setDtHrInclusao($dtHrInclusao)
    {
        return $this->dtHrInclusao = $dtHrInclusao;
    }

    public function getDtHrDevolucao()
    {
        return $this->dtHrDevolucao;
    }

    public function setDtHrDevolucao($dtHrDevolucao)
    {
        return $this->dtHrDevolucao = $dtHrDevolucao;
    }

    public function getUnidade()
    {
        return $this->unidade;
    }

    public function setUnidade($unidade)
    {
        return $this->unidade = $unidade;
    }

    public function getSetor()
    {
        return $this->setor;
    }

    public function setSetor($setor)
    {
        return $this->setor = $setor;
    }

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function setCategoria($categoria)
    {
        return $this->categoria = $categoria;
    }

    public function getObservacao()
    {
        return $this->observacao;
    }

    public function setObservacao($observacao)
    {
        return $this->observacao = $observacao;
    }

}
