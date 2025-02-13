<?php
namespace Geral\Controller;

use \Geral\Entity\WebClient;
use \Geral\Model\Factory\WebClientEndpointFactory;
use \Geral\Model\Config;
use \Geral\Model\Exception\ErrorException;

class WebClientController extends \Geral\Controller\AbstractPrivateController
{
    private $webClient;
    
    public function __construct()
    {
        parent::__construct();
        
        if(!$this -> auth -> isAllowedTransactions(['admin'])) {
            throw new ErrorException('Usuário sem permissão para acessar este módulo.');
        }
    }

    /***************************************************************************************/

    public function index()
    {
        $endpointsByApp = $this->getEndpointsByApp();

        $this -> addVarView('webClients', json_encode(WebClient::getAll(true), true));
        $this -> addVarView('endpointsByApp', $endpointsByApp);
        $this->view();
    }

    public function getWebClients()
    {
        $webClients = [];
        
        $result = [
            'result' => 'success',
            'webClients' => WebClient::getAll(true)
        ];

        $this->returnJson($result);
    }

    public function getWebClient()
    {
        $token = $this -> url -> getSegment(4);

        $result = [
            'result' => 'success',
            'webClient' => WebClient::getByToken($token, true)
        ];

        $this->returnJson($result);
    }

    /*=== CRUD  ===============================================================*/
    public function saveWebClient()
    {
        $token = $_POST['token'];
        $descricao = trim($_POST['descricao']);
        $_POST['ips'] = trim($_POST['ips']);
        $ips = !empty($_POST['ips']) ? explode(',', $_POST['ips']) : [];
        $_POST['emails'] = trim($_POST['emails']);
        $emails = !empty($_POST['emails']) ? explode(',', $_POST['emails']) : [];
        
        if(empty($descricao)) {
            $result = [
                'result' => 'error',
                'msg' => 'Campo descrição não pode ser vazio.'
            ];
        } elseif(!empty($token)) {
            $result = $this -> updateWebClient($token, $descricao, $ips, $emails);
        } else {
            $webClientDescription = $this -> em -> getRepository('\Geral\Entity\WebClient')->findOneBy(['descricao' => $descricao]);
            
            if(!empty($webClientDescription)) {
                // Já existe cliente utilizando a descrição
                $result = [
                    'result' => 'error',
                    'msg' => 'Cliente com a descrição "'.$descricao.'" já existe no sistema.'
                ];
            } else {
                try {
                    $webClient = WebClient::add($descricao, $ips, $emails);
                    $result = [
                        'result' => 'success',
                        'msg' => 'Cliente inserido com sucesso.',
                        'webClient' => $webClient -> toArray()
                    ];
                } catch (\Exception $e) {
                    throw new ErrorException('Não foi possível inserir o cliente web.');
                }
            }
        }

        $this -> returnJson($result);
    }
    
    private function updateWebClient($token, $descricao, Array $ips, Array $emails)
    {
        $webClientDescription = $this -> em -> getRepository('\Geral\Entity\WebClient')->findOneBy(['descricao' => $descricao]);
        $webClient = $this -> em -> find('\Geral\Entity\WebClient', $token);
        
        if (empty($webClient)) {
            // Cliente não encontrado
            $result = [
                'result' => 'error',
                'msg' => 'Cliente web com token "'.$token.'" não encontrado.'
            ];
        } elseif (!empty($webClientDescription) && $webClientDescription -> getToken() != $webClient -> getToken()) {
            // Já existe cliente utilizando a descrição
            $result = [
                'result' => 'error',
                'msg' => 'Cliente com a descrição "'.$descricao.'" já existe no sistema.'
            ];
        } else {
            // === Atualizar =======================================================
            $webClient -> setDescricao($descricao);
            $webClient -> setIps($ips);
            $webClient -> setEmails($emails);
            $this -> em -> flush();

            $result = [
                'result' => 'success',
                'msg' => 'Cliente web atualizado com sucesso.',
                'webClient' => $webClient -> toArray()
            ];
        }
        
        return $result;
    }

    public function removeWebClient()
    {
        $token = $this -> url -> getSegment(4);

        $webClient = WebClient::getByToken($token);

        if(empty($webClient)) {
            $result = [ 'result' => 'error', 'msg' => 'Não existe cliente cadastrado com o token "'.$token.'".' ];
        } else {
            try {
                $this->em->remove($webClient);
                $this->em->flush();
                $result = ['result' => 'success', 'msg' => 'Cliente web excluído com sucesso.'];
            } catch (Exception $e) {
                $result = ['result' => 'error', 'msg' => 'Erro ao excluir client web.'];
            }
        }

        $this->returnJson($result);
    }

// == CRUD DE ENDPOINTS ================================================================================================

    /**
    * Retornar os endpoints que o cliente web pode acessar.
    *
    * @param String UrlSegment Token do cliente web.
    *
    * @return JSON Endpoints
    */
    public function getWebClientEndpoints()
    {
        $token = $this->url->getSegment(4);
        $webClient = WebClient::getByToken($token);

        if(empty($webClient)) {
            $this->setError('Cliente web não encontrado.');
        } else {
            $endpoints = WebClientEndpointFactory::getByWebClient($webClient, true);
            $endpointsByMethod = [];

            foreach($endpoints as &$endpoint) {
                $endpointsByMethod[$endpoint['method']][] = $endpoint['url'];
            }

            $this->setSuccess('Consulta realizada com sucesso.', ['endpoints' => $endpointsByMethod]);
        }

        $this->sendResponse();
    }

    /**
    * Salvar os endpoints que o cliente web pode acessar.
    *
    * @param String POST token Token do cliente web.
    * @param Array  POST endpoints Urls que o cliente web deve ter acesso.
    *
    * @return JSON Endpoints
    */
    public function saveWebClientEndpoints()
    {
        $token = $this->url->getSegment(4);
        $endpointsByMethod = is_array($_POST['endpoints']) ? $_POST['endpoints'] : [];

        $webClient = WebClient::getByToken($token);
        
        if(empty($webClient)) {
            $this->setError('Cliente web não encontrado.');
            $this->sendResponse();
        }

        foreach($endpointsByMethod as $method => &$endpoints) {
            foreach($endpoints as &$endpoint) {
                if(!$this->endpointExists($method, $endpoint)) {
                    $this->setError('Não existe o endpoint "'.$endpoint.'" utilizando o método "'.$method.'".');
                    $this->sendResponse();
                }
            }

            unset($endpoints);
        }
        
        try {
            WebClientEndpointFactory::saveWebClientEndpoints($webClient, $endpointsByMethod);
            $this->setSuccess('Endpoints do cliente salvo com sucesso');
        } catch (\Exception $e) {
            $this->setError('Erro ao salvar endpoints do cliente: '.$e->getMessage());

        }

        $this->sendResponse();
    }

    // Pegar todos os endpoints configurados no arquivo config das aplicações.
    private function getEndpointsByApp()
    {
        $endpointsByApp = Config::getAllConfigs(['endpoints']);
        
        foreach($endpointsByApp as &$endpoint) {
            if(!empty($endpoint)) {
                $endpoint = current($endpoint);
            }
        }

        return $endpointsByApp;
    }

    // Verificar se o endpoint está presente na lista de endpoints das aplicações.
    private function endpointExists($method, $endpoint)
    {
        $endpointsByApp = $this->getEndpointsByApp();
        
        foreach($endpointsByApp as &$endpointsByMethod) {
            if(!isset($endpointsByMethod[$method])) continue;
            
            if(isset($endpointsByMethod[$method][$endpoint])) {
                return true;
            }
        }
        
        return false;
    }
}
?>
