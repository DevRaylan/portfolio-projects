<?php
namespace Geral\Model\Factory;

use \Geral\Entity\WebClient;
use \Geral\Entity\WebClientEndpoint;

/**
 * Criação, gravação e consulta de endpoints utilizados por clientes web.
 */

abstract class WebClientEndpointFactory
{
    static private function create (WebClient $webClient, $method, $endpoint)
    {
        try{
            $wcEndpoint = new WebClientEndpoint;
            $wcEndpoint->setWebClient($webClient);
            $wcEndpoint->setMethod($method);
            $wcEndpoint->setUrl($endpoint);
        } catch(\Exception $e) {
            echo $e->getMessage();
            exit;
            throw new \Exception("Erro ao gravar dados no banco de dados.");
        }
        
        return $wcEndpoint;
    }
    
    static public function saveWebClientEndpoints (WebClient $webClient, Array $endpointsByMethod)
    {
        self::removeWebClientEndpoints($webClient);

        foreach($endpointsByMethod as $method => $endpoints) {
            foreach($endpoints as &$endpoint) {
                $wcEndpoint = self::create($webClient, $method, $endpoint);
                $GLOBALS['em']->persist($wcEndpoint);
            }

            unset($endpoint);
        }

        $GLOBALS['em']->flush();

        return true;
    }

    static public function removeWebClientEndpoints (WebClient $webClient)
    {
        $epsFromWebClient = self::getByWebClient($webClient);

        if(!empty($epsFromWebClient)) {
            foreach ($epsFromWebClient as $epFromWebClient) {
                $GLOBALS['em']->remove($epFromWebClient);
            }
        }

        $GLOBALS['em']->flush();
    }
    
    static function getByWebClient(WebClient $webClient, $toArray = false)
    {
        $query = $GLOBALS['em']
            ->getRepository('\Geral\Entity\WebClientEndpoint')
            ->createQueryBuilder('ep')
            ->where('ep.webClient = ?1')
            ->setParameter(1, $webClient)
            ->getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    static function getByWebClientUrls(WebClient $webClient)
    {
        $endpoints = self::getByWebClient($webClient, true);
        $urlsByMethod = [];

        foreach($endpoints as &$endpoint) {
            $urlsByMethod[$endpoint['method']][] = $endpoint['url'];
        }

        return $urlsByMethod;
    }
}
