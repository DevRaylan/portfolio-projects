<?php

namespace Geral\Entity;

use JsonSerializable;

/**
 * Classe referente as tarefas que são agendadas e executadas em um terminado dia ou período
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 * 
 * @Entity
 **/
class TarefaAgendada implements JsonSerializable
{
    /** 
     * @Id @Column(type="integer") 
     * @GeneratedValue 
     */
    private $id;

    /** @Column(type="string", nullable=false) */
    private $nome;

    /** @Column(type="string", nullable=false) **/
    private $descricao;

    /** @Column(type="string", length=100, nullable=false) **/
    private $app;

    /** @Column(type="string", length=100, nullable=false) **/
    private $controller;

    /** @Column(type="string", length=100, nullable=false) **/
    private $action;

    /** @Column(type="text") **/
    private $parametros;

    /** @Column(type="string", length=100) **/
    private $token;

    /** @Column(type="string", length=10) **/
    private $minuto;

    /** @Column(type="string", length=10) **/
    private $hora;

    /** @Column(type="string", length=10) **/
    private $diaMes;

    /** @Column(type="string", length=10) **/
    private $mes;

    /** @Column(type="string", length=10) **/
    private $diaSemana;

    /** @Column(type="boolean", name="situacao", options={"default":"1"}) **/
    private $situacao;

    public function __construct()
    {
        $this->situacao = true;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of app
     */ 
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     *
     * @return self
     */ 
    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * Get the value of app
     */ 
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set the value of nome
     *
     * @return self
     */ 
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
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

    /**
     * Get the value of app
     */ 
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set the value of app
     *
     * @return  self
     */ 
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * Get the value of app
     */ 
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set the value of app
     *
     * @return  self
     */ 
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Get the value of app
     */ 
    public function getParametros()
    {
        return $this->parametros;
    }

    /**
     * Set the value of app
     *
     * @return  self
     */ 
    public function setParametros($parametros)
    {
        $this->parametros = $parametros;
        return $this;
    }

    /**
     * Get the value of app
     */ 
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the value of app
     *
     * @return  self
     */ 
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Get the value of app
     */ 
    public function getMinuto()
    {
        return $this->minuto;
    }

    /**
     * Set the value of app
     *
     * @return  self
     */ 
    public function setMinuto($minuto)
    {
        $this->minuto = $minuto;
        return $this;
    }

    /**
     * Get the value of app
     */ 
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * Set the value of app
     *
     * @return  self
     */ 
    public function setHora($hora)
    {
        $this->hora = $hora;
        return $this;
    }

    /**
     * Get the value of app
     */ 
    public function getDiaMes()
    {
        return $this->diaMes;
    }

    /**
     * Set the value of app
     *
     * @return  self
     */ 
    public function setDiaMes($diaMes)
    {
        $this->diaMes = $diaMes;
        return $this;
    }

    /**
     * Get the value of app
     */ 
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set the value of app
     *
     * @return  self
     */ 
    public function setMes($mes)
    {
        $this->mes = $mes;
        return $this;
    }

    /**
     * Get the value of app
     */ 
    public function getDiaSemana()
    {
        return $this->diaSemana;
    }

    /**
     * Set the value of app
     *
     * @return  self
     */ 
    public function setDiaSemana($diaSemana)
    {
        $this->diaSemana = $diaSemana;
        return $this;
    }

    /**
     * Retorna a situação: 
     *  true : Ativo
     *  false: Desativo
     */
    public function getSituacao(){
        return $this->situacao;
    }

    /**
     * Atribui o valor da propriedade situação
     */
    public function setSituacao($situacao){
        $this->situacao = $situacao;
        return $this;
    }

    /**
     * Gera um token para a tarefa
     *
     * @return void
     */
    public function generateToken(){
        $this->setToken(md5($this->getNome().microtime()));
    }

    public function toSchedule($baseUrl){
        $endereco = $baseUrl . "/" . $this->getApp() . "/" . $this->getController() . "/" . $this->getAction() . "/" . $this->getParametros();
        $cron     = $this->getMinuto() . " " . $this->getHora() . " " . $this->getDiaMes() . " " . $this->getMes() . " " . $this->getDiaSemana();

        return [
            'url'  => $endereco,
            'cron' => $cron,
            'auth' => $this->getToken()
        ];
    }

    public function jsonSerialize(){
        return [
            'id'         => $this->getId(),
            'nome'       => $this->getNome(),
            'descricao'  => $this->getDescricao(),
            'app'        => $this->getApp(),
            'controller' => $this->getController(),
            'action'     => $this->getAction(),
            'parametros' => $this->getParametros(),
            'minuto'     => $this->getMinuto(),
            'hora'       => $this->getHora(),
            'diaMes'     => $this->getDiaMes(),
            'mes'        => $this->getMes(),
            'diaSemana'  => $this->getDiaSemana(),
            'situacao'   => $this->getSituacao()
        ];
    }
}