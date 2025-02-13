<?php
namespace Geral\Controller;

use Geral\Entity\WebClient;
use Geral\Model\Factory\LogFactory;
use Geral\Model\Factory\WebClientEndpointFactory;
use Geral\Model\Config;

abstract class AbstractWSController extends \Geral\Controller\AbstractController
{
    private $webClient;
    private $endpoints;
    
    public function __construct()
    {
        parent::__construct();
        
        try {
            $this -> autenticar();
        } catch(\Exception $e) {
            $this -> returnAuthenticationError();
            exit;
        }

        $this->endpoints = Config::getValue('endpoints');
        //$this->endpoints = is_array($endpoints) ? array_keys($endpoints) : [];
        
        if(!$this -> endpointAccessAllowed()) {
            die('Sistema não autorizado para esta requisição.');
            //$this->setError('Sistema não autorizado para esta requisição.');
            $this -> returnAuthenticationError();
        }
    }

    protected function getWebClient()
    {
        return $this -> webClient;
    }

    public function getToken()
    {
        return isset($this -> webClient) ? $this -> webClient -> getToken() : '';
    }

    public function index()
    {
        //die('Módulo => Serviço de geração de boleto.');
        $this->view();
    }

    private function autenticar()
    {
        $this -> webClient = WebClient::getByToken($_SERVER['PHP_AUTH_USER']);
        
        if(empty($this -> webClient) || !$this -> webClient -> keySecretIsValid($_SERVER['PHP_AUTH_PW']) || !$this -> webClient -> isAllowedIp($_SERVER['HTTP_X_REAL_IP'])) {
            //401 Unauthorized
            $this->returnAuthenticationError();
        }
    }

    private function endpointAccessAllowed()
    {
        // Requisição
        $endpoint = $_SERVER['REDIRECT_URL'];

        // Verificar se existe enpoints para o método da requisição (POST, GET, etc.).
        if(!isset($this->endpoints[$this->getMethod()])) {
            return false;
        }
        
        // Endpoints da aplicação
        $appEndpoints = array_keys($this->endpoints[$this->getMethod()]);
        
        // Endpoints que o cliente pode consumir
        $webClientEndpoints = WebClientEndpointFactory::getByWebClientUrls($this->webClient);

        // Verificar se existem endpoints acessíveis pelo cliente no método da requisição.
        if(!isset($webClientEndpoints[$this->getMethod()])) {
            return false;
        }

        // Selecionar apenas endpoints da aplicação que esteja presente nos endpoints que a aplicação pode consumir
        $webClientEndpoints = array_intersect($webClientEndpoints[$this->getMethod()], $appEndpoints);
        $allowedEndpoint = false;

        // Percorrer apenas os endpoints utilizáveis pelo cliente
        foreach($webClientEndpoints as $i => $webClientEndpoint) {
            // Se o endpoint possuir parâmetros do tipo variável
            if(strpos($webClientEndpoint, '{') !== false) {
                $endpoint2 = explode('/',$endpoint);
                $webClientEndpoint = explode('/', $webClientEndpoint);
                
                // Percorrer os segmentos do endpoint
                foreach($webClientEndpoint as $i => &$segmento) {
                    // Se o segmento contiver "{", então substitui o conteúdo do segmento por * se o indice deste segmento
                    // também existir ba requisição
                    if(strpos($segmento, '{') !== false && isset($endpoint2[$i])) {
                        // Setar o segmento na requisição com *
                        $endpoint2[$i] = '*';
                        // Setar o segmento no endpoint configurado como *
                        $segmento = '*';
                    }
                }
                
                // Neste momento as URLS de webClientEndpoint e endpoint2 terão seu conteúdo com as variáveis substituídas por *
                $webClientEndpoint = implode('/', $webClientEndpoint);
                $endpoint2 = implode('/', $endpoint2);
                
                // Se a requisição e o endpoint forem iguais, então o cliente pode acessá-la
                if(strcmp($webClientEndpoint, $endpoint2) === 0) {
                    $allowedEndpoint = true;
                    break;
                }
            }
            // O endpoint não possui parâmetros do tipo variável
            else {
                // Se a requisição e o endpoint forem iguais, então o cliente pode acessá-la
                if(strcmp($webClientEndpoint, $endpoint) === 0) {
                    $allowedEndpoint = true;
                    break;
                }
            }
        }

        return $allowedEndpoint;
    }
    
    // Retorna o erro de autenticação no formato JSON (padrão)
    protected function returnAuthenticationError()
    {
        //401 Unauthorized
        $this -> setError('Erro na autenticação do sistema, verifique se os dados de acesso estão corretos.');
        $this -> sendResponse();
    }
}
