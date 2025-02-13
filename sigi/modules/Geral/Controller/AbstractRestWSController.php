<?php
/**
 * @class AbstractPublicController
 * Esta classe especializa o AbstractWSController, implementando funções específicas de
 * serviços REST Full.
 * O retorno será de acordo com o escolhido pelo consumidor do serviço (Ex: JSON, CSV, etc.)
 * Retornos com código HTTP diferente de 2xx são feitos sempre em formato JSON,
 * onde conterá mais detalhes sobre o erro ocorrido.
 * 
 * Referência: https://www.restapitutorial.com
 **/
namespace Geral\Controller;

use \Geral\Model\Config;
use Geral\Model\SystemMessages;

abstract class AbstractRestWSController extends \Geral\Controller\AbstractWSController
{
    // Códigos HTTP suportados
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    
    private $routes = [];
    
    public function __construct()
    {
        parent::__construct();

        /**
        * Configuração das rotas de acordo com regras definidas.
        *
        * @see AbstractRestWSController::setMethod
        */
        
        foreach(Config::getValue('endpoints') as $method => &$routes) {
            $toEndpoints = [];
            
            foreach($routes as $url => &$route) {
                $url = str_replace($this->url->getUrlAction(), '', $url);
                $toEndpoints[$url] = $route['to'];
            }
            
            unset($route);
            $this->setMethod($method, $toEndpoints);
        }
        
        $this->run();
    }
    
    /**
    * Este método configura as rotas de um endpoint.
    * $this->routes tem a seguinte estrutura:
    * [
    *    <GET|POST|...> => [
    *        <indiceSegUrl> => [
    *            'to' => <método do ctrl>
    *            'params' => [var_1, var_2, ...]
    *        ]
    *    ]
    * ]
    *
    * @param $method Tipo do método HTTP (POST, GET, etc.)
    * @param $routes Rotas no formato <padrão da url> => método da classe.
    * Exemplo:
    * $this->setMethod('GET', [ 
    *   <url1> => <método1 do controller>,
    *   <url2> => <método2 do controller>
    * ]);
    */ 
    private function setMethod($method, Array $routes)
    {
        // Iterar sobre as rotas
        foreach($routes as $url => $route) {
            // Separar os segmentos
            $params = explode('/', $url);

            /**
             * Retira o primeiro elemento, pois é o parametro zero do endpoint.
             * Ex: /<app>/<ctrl>/<method>[/param1/param2]
             * o indice 0 do array é vazio se a url não tiver parametros.
             */
            array_shift($params);
            
            // Pegar o número de parâmetros
            $qtdSegments = count($params);

            $this -> routes[$method][$qtdSegments][$url]['to'] = $route;
            
            // Percorrer cada parametro da URL.
            foreach($params as $i => &$param) {
                if($this -> isVariable($param)) {
                    $this -> routes[$method][$qtdSegments][$url]['params'][$i] = $param;
                }
            }

            unset($param);
        }
    }
    
    // Faz o roteamento com base no $this -> routes
    private function run()
    {
        // Verificar se existe rotas para o método de requisição (POST, GET, etc.).
        if(!isset($this -> routes[parent::getMethod()])) {
            throw new \Exception('Método não válido para esta requisição.', 1);
        }
        
        // Pegar os seguimentos da url.
        $segments = $this -> url -> getSegments();
        $segments2 = $segments;
        
        // Pegar o número de segmentos.
        $qtdSegments = count($segments);

        $methodFound = false;
        
        foreach($this -> routes[parent::getMethod()][$qtdSegments] as $url => $route) {
            // Se não existir rotas...
            if(empty($route)) {
                throw new \Exception('Requisição com quantidade de parâmetros não suportada.', 1);
            }
        
            // Verificar se tem alvo para a rota.
            if(empty($route['to'])) {
                die('Função não encontrada.');
            }
        
            $params = [];
            
            // Se for esperado parâmetros para a rota.
            if(!empty($route['params'])) {

                // Percorrer os parâmetros esperados.
                foreach($route['params'] as $iSegment => $key) {
                    $segments2[$iSegment] = $key;

                    // Se o parâmetro for variável (ex: /{id} => / 2 mapea id=2)
                    if($this -> isVariable($key)) {
                        $key = str_replace(['{', '}'], '' , $key);
                        
                        // Se o parâmetro não está presente na requisição.
                        if(empty($segments[$iSegment])) {
                            $this -> setError(self::HTTP_BAD_REQUEST);
                            exit;
                            //throw new \Exception('Segmento correspondente a "'.$key.'" não pode ser vazio..', 1);
                        }
                        
                        // Pegar o valor do parâmetro na URL.
                        $params[$key] = $segments[$iSegment];
                    }
                }

                // Reescrever os parametros de route com as substituições das variáveis esperadas na URL pelos valores da requisição.
                $route['params'] = &$params;
            } else {
                // Params sempre tem que ser um array
                $route['params'] = [];
            }

            // Montar Endpoint com o curinga {} para comparar. Ex: /1/teste => /{id}/teste e comparar com o endpoint /{id}/teste do config.
            $urlIn = '/'.implode('/', $segments2);

            // Adiciona barra pra igualar com o $urlIn quando sem parâmetros
            if(empty($segments2)) {
                $url = '/';
            }

            // Se endpoint encontrado, então interrompe o laço e continua o processo.
            if($url == $urlIn) {
                $methodFound = true;
                break;
            }
        }

        if(!$methodFound) {
            //throw new \Exception('Método "'.$segments[$iSegment].'" não encontrado.', 1);
            $this -> setError(self::HTTP_NOT_FOUND);
            $this -> sendResponse();
        }
    
        // Chamar método responsável por tratar a requisição.
        $this -> {$route['to']}($route['params']);
    }
    
    /**
     * Verificar se o parãmetro se trata de uma variável e não de uma string contida na url. Para isso é apenas
     * verificado se o caracter '{' está presente no valor.
     */
    private function isVariable($var)
    {
        return strpos($var, '{') !== false;
    }
    
    /**
    * @override
    * Retorna o erro de autenticação utilizando código HTTP.
    * Esta função reescreve o comportamento padrão (formato JSON com return e msg), enviando
    * o código equivalente para autenticações feitas sem sucesso.
    */
    protected function returnAuthenticationError()
    {
        $this -> setError(self::HTTP_UNAUTHORIZED);
        $this -> sendResponse();
    }
    
    /**
    * @override
    * Função utilizada para retornos de requisições processadas com sucesso.
    */
    protected function setSuccess($data = [], $code = self::HTTP_OK)
    {
        if(is_array($data)) {
            SystemMessages::setHttpCode($code);
            $msg = '';
        } else {
            $msg = $data;
            $data = [];
        }

        parent::setSuccess($msg, $data);
    }
    
    /**
    * @override
    * Função utilizada para retornos de erros ocorridos ao processar requisições.
    */
    protected function setError($code, $data = [])
    {
        if(is_string($code)) {
            $msg = $code;
            $code = self::HTTP_OK;
        } else {
            $msg = 'Erro ao processar requisição.';
        }

        SystemMessages::setHttpCode($code);
        
        parent::setError($msg, $data);
    }
    
    /**
    * @override
    * Envia a resposta ao consumidor do serviço, verificando se o retorno desejado
    * é suportado (Ex: JSON, CSV, etc.)
    */
    protected function sendResponse($outputFormat = 'json', $opc = null)
    {   
        // Processar conteúdo de retorno
        parent::sendResponse($outputFormat, $opc);
    }
}