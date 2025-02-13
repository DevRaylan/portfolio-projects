<?php
/**
 * Url do serviço oferecido pelo sistema.
 *
 * @version 1.0
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 */

namespace Geral\Entity;

use \Geral\Model\Date;
use \Geral\Entity\WebClient;

 /**
 * @Entity
 * @Table(name="webclientendpoint")
 **/

class WebClientEndpoint
{
    /** 
     * @Id
     * @ManyToOne(targetEntity="\Geral\Entity\WebClient")
     * @JoinColumn(name="webclient", referencedColumnName="token")
    **/
    private $webClient;

    /** @Id @Column(type="string", length=10, nullable=false) **/
    private $method;

    /** @Id @Column(type="string", length=40, nullable=false) **/
    private $url;

    /** @Column(name="dt_create", type="datetime", nullable=false) **/
    private $dtCreate;

// ====================================================================================================================

    public function __construct( ) {
        $this -> dtCreate = new \DateTime();
    }

    public function setWebClient(WebClient $webClient) {
        $this -> webClient = $webClient;
    }

    public function getWebClient() {
        return $this -> webClient;
    }

    public function setMethod($method) {
        $this -> method = $method;
    }

    public function getMethod() {
        return $this -> method;
    }

    public function setUrl($url) {
        $this -> url = $url;
    }

    public function getUrl() {
        return $this -> url;
    }

    public function getDtCreate($string = false) {
        return $string ? Date::convDatetimeToString($this->dtCreate, Date::DATAHORASEMSEGUNDOS_FORMAT) : $this->dtCreate;
    }
}
