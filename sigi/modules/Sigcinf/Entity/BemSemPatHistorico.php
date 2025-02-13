<?php
namespace Sigcinf\Entity;

use \Geral\Model\Exception\ErrorException;

/**
 * @Entity
 */
class BemSemPatHistorico
{
    /**
     * @Column(type = "integer") @GeneratedValue @Id
     */
    private $id;

    /**
     * @Column(type="string", length=1)
     */
    private $operacao;

    /**
     * @Column(type="string", length=100)
     */
    private $motivo;

    /**
     * @ManyToOne( targetEntity = "BemSemPat" )
     */
    private $bemsempat;

    /**
     * @Column( type = "string", length=20, nullable = false )
     */
    private $dtHrInclusao;

    /**
     * @Column( type = "integer", nullable = false )
     */
    private $qtd;

    /**
     * @Column( type = "string", length=100, nullable = true )
     */
    private $observacao;

    
    public function __construct() { }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    public function getMotivo()
    {
        return $this->motivo;
    }

    public function getOperacao()
    {
        return $this->operacao;
    }

    public function getBemSemPat()
    {
        return $this->bemsempat;
    }

    public function getDtHrInclusao()
    {
        return $this->dtHrInclusao;
    }

    public function getQtd()
    {
        return $this->qtd;
    }

    public function getObservacao()
    {
        return $this->observacao;
    }    

    public function setMotivo($motivo)
    {
        return $this->motivo = $motivo;
    }

    public function setOperacao($operacao)
    {
        return $this->operacao = $operacao;
    }

    public function setBemSemPat($bemsempat)
    {
        return $this->bemsempat = $bemsempat;
    }

    public function setDtHrInclusao($dtHrInclusao)
    {
        return $this->dtHrInclusao = $dtHrInclusao;
    }

    public function setQtd($qtd)
    {
        return $this->qtd = $qtd;
    }    

    public function setObservacao($observacao)
    {
        return $this->observacao = $observacao;
    }    

}
