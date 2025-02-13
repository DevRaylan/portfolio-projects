<?php
namespace Sigcinf\Entity;

use \Geral\Model\Exception\ErrorException;

/**
 * @Entity
 */
class Usuario
{
    /**
     * @Column(type = "integer") @GeneratedValue @Id
     */
    private $id;

    /**
     * @Column(type="string", length=50)
     */
    private $nome;

    /**
     * @Column(type="string", length=11)
     */
    private $cpf;

    /**
     * @Column(type="string", length=100)
     */
    private $email;

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
     * Get the value of nome
     */ 
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     *
     * @return  self
     */ 
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get the value of cpf
     */ 
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * Set the value of cpf
     *
     * @return  self
     */ 
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

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
