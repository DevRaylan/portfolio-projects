<?php
/**
 * @class URL
 *
 * Esta classe possui os métodos e atributos comuns a manipulação de URLS
 *
 * @version 1.0
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 */

namespace Geral\Model;

class Url
{
    // Segmentos da url.
    private $segments;
    
    // Nome da aplicação/módulo da requisição. 
    private $module;

    // Nome do controller (classe controller) da requisição.
    private $controller;

    // Nome da ação/método do controller da requisição.
    private $action;

    /** 
    * Inicializa as variáveis relativas a url da requisição. 
    *  
    * @param String $url Endereço completo da requisição. 
    */ 
    function __construct( $url )
    {
        $this -> segments = explode( '/', $url );

        if ( empty( $this -> segments[1] ) )
            $this -> segments[1] = 'Geral';
        if ( empty( $this -> segments[2] ) )
            $this -> segments[2] = 'Index';
        if ( empty( $this -> segments[3] ) )
            $this -> segments[3] = 'index';

        $this -> module = &$this -> segments[1];
        $this -> controller = &$this -> segments[2];
        $this -> action = &$this -> segments[3];
    }

    /* ###################################################*/

    /**
     * Retorna o conteúdo de um segmento da url
     *
     * @param Integer number Número do segmento da url
     * @return  String Conteúdo do segmento
     **/
    public function getSegment( $number )
    {
        if ( isset( $this -> segments[$number] ) ) {
            return $this -> segments[$number];
        } else {
            return null;
        }
    }
    
    /** 
     * Retorna os segmentos da url a partir de uma posição. 
     * 
     * @param Integer $offset Posição do segmento inicial da url para retorno. 
     * 
     * @return Array Conteúdo do segmento 
     */ 
    public function getSegments( $offset = 3 )
    {
        return array_slice($this -> segments, ($offset+1));
    }

    // Retorna o nome da aplicação/módulo.
    public function getNameModule( )
    {
        return $this -> module;
    }

    // Define o nome da aplicação/módulo.
    public function setNameModule( $module)
    {
        $this -> module = $module;
    }

    // Retorna o nome do controller.
    public function getNameController( )
    {
        return $this -> controller;
    }

    // Define o nome do controller.
    public function setNameController( $controller )
    {
        $this -> controller = $controller;
    }

    // Retorna o nome da ação/método.
    public function getNameAction( )
    {
        return $this -> action;
    }

    // Define o nome da ação/método.
    public function setNameAction( $action )
    {
        $this -> action = $action;
    }

    // Retorna a url completa da requisição.
    public function getFullUrl()
    {
        return (isset($_SERVER['HTTPS']) ? "https" : "http").'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }

    /** 
     * Retorna a url da aplicação/módulo. 
     * Ex: Aplicação Geral => /Geral 
     * 
     * @param Boolean $abs Retornar a url absoluta (Ex: http://.../Geral) ou relativa (/Geral). 
     * 
     * @return  string 
     **/
    public function getUrlModule( $abs = false)
    {
        return ($abs ? (isset($_SERVER['HTTPS']) ? "https" : "http").'://'.$_SERVER['SERVER_NAME'] : '') . '/'.$this -> module;
    }

    /** 
     * Retorna a url do controller. 
     * Ex: Aplicação Geral => /Geral/Index 
     * 
     * @param boolean $abs Retornar a url absoluta (Ex: http://.../Geral/Index) ou relativa (/Geral/Index). 
     * 
     * @return  string 
     **/
    public function getUrlController( $abs = false )
    {
        return $this -> getUrlModule($abs) .'/' . $this -> controller;
    }

    /** 
     * Retorna a url da ação/método. 
     * Ex: Tela de login da aplicação Geral => /Geral/Login/formLogin 
     * 
     * @param boolean $abs Retornar a url absoluta (Ex: http://.../Geral/Login/formLogin) ou relativa (/Geral/Login/formLogin). 
     * 
     * @return  string 
     **/
    public function getUrlAction( $abs = false )
    {
        return $this -> getUrlController($abs) . '/' . $this -> action;
    }

    /** 
     * Redireciona a url para outro local do sistema. 
     * Ex: Tela de login da aplicação Geral => /Geral/Login/formLogin 
     * 
     * @param $controller string Nome do controller de destino. 
     * @param $action string Nome da ação/método de destino. 
     * @param $module string Nome do módulo de destino. 
     * @param $get array Variáveis a serem passadas via método GET para a destino. 
     * 
     * @return  void 
     * @see redirectToUrl() 
     **/
    public function redirect( $controller = null, $action = null, $module = null, Array $get = null)
    {
        $url = empty( $module ) ? $this->getUrlModule() : '/'.$module;

        if ( !empty( $controller ) ) {
            $url .= '/'.$controller;

            if ( !empty( $action ) ) {
                $url .= '/' . $action;
            }
        }

        if(!empty($get)) {
            $url .= '/?'.http_build_query($get);
        }

        $this -> redirectToUrl( $url );
    }

    /** 
     * Redireciona para um dado endereço. 
     * 
     * @param $url string endereço de destino. 
     **/
    public function redirectToUrl( $url )
    {
        header( 'Location: ' . $url );
        exit ;
    }

    /** 
     * Retorna a url relativa as imagens da aplicação. 
     * Função muito utilizada nos arquivos das Views do sistema. 
     * 
     * @return  string
     **/
    public function getUrlImages()
    {
        return '/imagens' . $this->getUrlModule();
    }

    /** 
     * Retorna a url relativa aos arquivos para downloads da aplicação. 
     * Função muito utilizada nos arquivos das Views do sistema. 
     * 
     * @return  string 
     */
    public function getUrlDownloads()
    {
        return DIR_FILES . '/' . $this->getNameModule() . '/' . DIR_NAME_DOWNLOADS;
    }

    /**
     * Verificar se a URL pertence a um serviço do tipo WebService. Esta verificação é feita checando se o nome do 
     * controller inicia com o termo "WSv". Exemplo: a URL /Template/WSv5/user/1 possui o nome "WSv5" no nome do
     * controller e inicia com termo "WSv" utilizado em WebServices do framework. Logo se trata de uma URL de WebService.
     */
    public function isWebService()
    {
        return strpos($this -> getNameController(), 'WSv') === 0;
    }

    /**
     * Verificar se a requisição está sendo feita via Ajax
     */
    public function isAjax()
    {
        $header = ( array_key_exists( 'HTTP_X_REQUESTED_WITH', $_SERVER ) ?
                  $_SERVER['HTTP_X_REQUESTED_WITH'] : '' );

        return ( strcasecmp( $header, 'xmlhttprequest' ) == 0 );
    }
}
