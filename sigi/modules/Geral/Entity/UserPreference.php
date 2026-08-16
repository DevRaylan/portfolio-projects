<?php
namespace Geral\Entity;

/**
 * @Entity
 **/
class UserPreference
{
    /** @Id @Column(type="integer") @GeneratedValue */
    private $id;

    /** @Column(type="string", length=11, nullable=false) */
    private $cpf;

    /** @Column(type="string", nullable=false) **/
    private $preference;

    /** @Column(type="string", length=255) **/
    private $value;

    /** @Column(type="string", length=50) **/
    private $app;

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of param
     */ 
    public function getPreference()
    {
        return $this->preference;
    }

    /**
     * Set the value of param
     *
     * @return  self
     */ 
    public function setPreference($preference)
    {
        $this->preference = $preference;

        return $this;
    }

    /**
     * Get the value of value
     */ 
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value of value
     *
     * @return  self
     */ 
    public function setValue($value)
    {
        $this->value = $value;

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
     * Get the value of app
     */ 
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Set the value of app
     *
     * @return  self
     */ 
    public function setApp($app)
    {
        $this->app = $app;

        return $this;
    }
}
