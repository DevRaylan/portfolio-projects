<?php
namespace Geral\Entity;

use Geral\Model\Interfaces\UserDataInterface;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class UserDataCache implements UserDataInterface
{
    // Dias de validade para o dado do usuário.
    private const DIAS_DE_VALIDADE = 1;

    /** @Id @Column(type="string", length=11) */
    private $cpf;

    /** @Id @Column(type="string", length=50) */
    private $vinculo;

    /** @Column(type="string", length=11) */
    private $matricula;

    /** @Column(type="string", length=1, nullable=true) */
    private $sexo;

    /** @Column(type="string", length=10, nullable=true) */
    private $dataNascimento;

    /** @Column(type="boolean") */
    private $isPrincipal;

    /** @Column(type="string", length=100) */
    private $nome;

    /** @Column(type="string", length=20) */
    private $unidade;

    /** @Column(type="string", length=100) */
    private $setor;

    /** @Column(type="string", length=100) */
    private $cargo;

    /** @Column(type="string", length=100) */
    private $funcao;

    /** @Column(type="string", length=10) */
    private $situacao;

    /** @Column(type="string", length=50) */
    private $email;

    /** @Column(type="string", length=255) */
    private $emailAlias;

    /** @Column(type="string", length=50, nullable=true) */
    private $emailAlternativo;

    /** @Column(type="string", length=20, nullable=true) */
    private $telefoneComercial;

    /** @Column(type="string", length=20, nullable=true) */
    private $telefoneCelular;

    /** @Column(type="string", length=20, nullable=true) */
    private $telefoneResidencial;

    /** @Column(type="datetime") */
    private $dataExpiracao;

    // Foto do usuário codificada em base64.
    /** @Column(type="text", nullable=true) */
    private $foto;

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
        $this->id = $cpf;

        return $this;
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
     * Get the value of dataExpiracao
     */ 
    public function getDataExpiracao()
    {
        return $this->dataExpiracao;
    }

    public function isExpired()
    {
        return $this->dataExpiracao < new \DateTime();
    }

    /** @preFlush */
    public function setDataExpiracao()
    {
        $this->dataExpiracao = new \DateTime('+'.self::DIAS_DE_VALIDADE.' Days');
    }

    /**
     * Get the value of vinculo
     */ 
    public function getVinculo()
    {
        return $this->vinculo;
    }

    /**
     * Set the value of vinculo
     *
     * @return  self
     */ 
    public function setVinculo($vinculo)
    {
        $this->vinculo = $vinculo;

        return $this;
    }

    /**
     * Get the value of unidade
     */ 
    public function getUnidade()
    {
        return $this->unidade;
    }

    /**
     * Set the value of unidade
     *
     * @return  self
     */ 
    public function setUnidade($unidade)
    {
        $this->unidade = $unidade;

        return $this;
    }

    /**
     * Get the value of setor
     */ 
    public function getSetor()
    {
        return $this->setor;
    }

    /**
     * Set the value of setor
     *
     * @return  self
     */ 
    public function setSetor($setor)
    {
        $this->setor = $setor;

        return $this;
    }

    /**
     * Get the value of cargo
     */ 
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * Set the value of cargo
     *
     * @return  self
     */ 
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;

        return $this;
    }

    /**
     * Get the value of funcao
     */ 
    public function getFuncao()
    {
        return $this->funcao;
    }

    /**
     * Set the value of funcao
     *
     * @return  self
     */ 
    public function setFuncao($funcao)
    {
        $this->funcao = $funcao;

        return $this;
    }

    /**
     * Get the value of isPrincipal
     */ 
    public function getIsPrincipal()
    {
        return $this->isPrincipal;
    }

    /**
     * Set the value of isPrincipal
     *
     * @return  self
     */ 
    public function setIsPrincipal($isPrincipal)
    {
        $this->isPrincipal = $isPrincipal;

        return $this;
    }

    /**
     * Get the value of situacao
     */ 
    public function getSituacao()
    {
        return $this->situacao;
    }

    /**
     * Set the value of situacao
     *
     * @return  self
     */ 
    public function setSituacao($situacao)
    {
        $this->situacao = $situacao;

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

    /**
     * Get the value of emailAlias
     */ 
    public function getEmailAlias()
    {
        return $this->emailAlias;
    }

    /**
     * Set the value of emailAlias
     *
     * @return  self
     */ 
    public function setEmailAlias($emailAlias)
    {
        $this->emailAlias = $emailAlias;

        return $this;
    }

    /**
     * Get the value of emailAlternativo
     */ 
    public function getEmailAlternativo()
    {
        return $this->emailAlternativo;
    }

    /**
     * Set the value of emailAlternativo
     *
     * @return  self
     */ 
    public function setEmailAlternativo($emailAlternativo)
    {
        $this->emailAlternativo = $emailAlternativo;

        return $this;
    }

    /**
     * Get the value of telefoneComercial
     */ 
    public function getTelefoneComercial()
    {
        return $this->telefoneComercial;
    }

    /**
     * Set the value of telefoneComercial
     *
     * @return  self
     */ 
    public function setTelefoneComercial($telefoneComercial)
    {
        $this->telefoneComercial = $telefoneComercial;

        return $this;
    }

    /**
     * Get the value of telefoneCelular
     */ 
    public function getTelefoneCelular()
    {
        return $this->telefoneCelular;
    }

    /**
     * Set the value of telefoneCelular
     *
     * @return  self
     */ 
    public function setTelefoneCelular($telefoneCelular)
    {
        $this->telefoneCelular = $telefoneCelular;

        return $this;
    }

    /**
     * Get the value of telefoneResidencial
     */ 
    public function getTelefoneResidencial()
    {
        return $this->telefoneResidencial;
    }

    /**
     * Set the value of telefoneResidencial
     *
     * @return  self
     */ 
    public function setTelefoneResidencial($telefoneResidencial)
    {
        $this->telefoneResidencial = $telefoneResidencial;

        return $this;
    }

    /**
     * Get the value of matricula
     */ 
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * Set the value of matricula
     *
     * @return  self
     */ 
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;

        return $this;
    }

    /**
     * Get the value of sexo
     */ 
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Set the value of sexo
     *
     * @return  self
     */ 
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get the value of dataNascimento
     */ 
    public function getDataNascimento()
    {
        return $this->dataNascimento;
    }

    /**
     * Set the value of dataNascimento
     *
     * @return  self
     */ 
    public function setDataNascimento($dataNascimento)
    {
        $this->dataNascimento = $dataNascimento;

        return $this;
    }

    /**
     * Get the value of foto
     */ 
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * Set the value of foto
     *
     * @return  self
     */ 
    public function setFoto($foto)
    {
        $this->foto = $foto;

        return $this;
    }
}
