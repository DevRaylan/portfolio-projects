<?php
namespace Sigcinf\Entity;

use \Geral\Model\Exception\ErrorException;

/**
 * @Entity
 * @Table(uniqueConstraints = { @UniqueConstraint( columns = { "patrimonio" } ) })
 */
class BemComPat
{
    /**
     * @Column(type = "integer") @GeneratedValue @Id
     */
    private $id;

    /**
     * @Column(type="string", length=10)
     */
    private $patrimonio;

    /**
     * @Column(type="string", length=30)
     */
    private $descricao;

    /**
     * @Column(type="string", length=1, nullable=true)
     */
    private $status;

    /**
     * @ManyToOne( targetEntity = "Unidades" )
     */
    protected $unidade;

    /**
     * @ManyToOne( targetEntity = "Setores" )
     */
    protected $setor;

    public function __construct() { }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of patrimonio
     */ 
    public function getPatrimonio()
    {
        return $this->patrimonio;
    }

    /**
     * Set the value of patrimonio
     *
     * @return  self
     */ 
    public function setPatrimonio($patrimonio)
    {
        $this->patrimonio = $patrimonio;

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
