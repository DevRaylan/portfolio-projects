<?php

namespace Sigcinf\Entity;

use \Geral\Model\Exception\ErrorException;

/** @Entity */
class BemSemPat
{
    /**
     * @Column(type = "integer") @GeneratedValue @Id
     */
    private $id;

    /**
     * @Column(type="string", length=10) 
     */
    private $codigo;

    /**
     * @Column(type="string", length=50)
     */
    private $descricao;

    /**
     * @Column(type="string", length=1)
     */
    private $status;

    /**
     * @Column(type="integer")
     */
    private $qtdtotal;    

    /**
     * @Column(type="integer")
     */
    private $qtd;  

    /**
     * @ManyToOne( targetEntity = "Unidades" )
     */
    protected $unidade;

    /**
     * @ManyToOne( targetEntity = "Setores" )
     */
    protected $setor;

    public function __construct() {
     }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of codigo
     */ 
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set the value of codigo
     *
     * @return  self
     */ 
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get the value of descricao
     */ 
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set the value of descricao
     *
     * @return  self
     */ 
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * Get the value of quantidade em estoque
     */ 
    public function getQtd()
    {
        return $this->qtd;
    }

    /**
     * Set the value of quantidade em estoque
     *
     * @return  self
     */ 
    public function setQtd($qtd)
    {
        $this->qtd = $qtd;

        return $this;
    }

    /**
     * Get the value of quantidade compradas
     */ 
    public function getQtdTotal()
    {
        return $this->qtdtotal;
    }

    /**
     * Set the value of quantidade compradas
     *
     * @return  self
     */ 
    public function setQtdTotal($qtdtotal)
    {
        $this->qtdtotal = $qtdtotal;

        return $this;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    public function setStatus(String $status)
    {
        if(!in_array($status, ['A', 'I'])) {
            throw new ErrorException('Valor de status "'.$status.'" não suportado. Valores esperados: Ativo (A) ou Inativo (I)');
        }

        $this->status = $status;

        return $this;
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

}
