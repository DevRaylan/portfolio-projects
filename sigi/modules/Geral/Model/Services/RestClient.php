<?php

/**
 * @class RestClient
 *
 * Esta classe abstrai a chamada para um serviço externo utilizando REST API
 *
 * @version 1.0
 * @author Mayara Madeira Trevisol <mayara.trevisol@udesc.br>
 */

namespace Geral\Model\Services;

use ErrorException;
use SimpleXMLElement;

class RestClient
{
    // Ex: new SgbrClient(self::AUTH_SGBR['sgbrToken'], self::AUTH_SGBR['sgbrKey']);
    public function __construct()
    {
    }

    // ---------------- Method JSON ----------------

    // Data: array("param" => "value") ==> index.php?param=value
    public function sendJsonPost($token, $key, $url, $data = [])
    {
        $data = http_build_query($data);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERPWD, $token . ":" . $key);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        if (isset($response['result'])) {
            return $response;
        } else {
            throw new ErrorException("Resposta do Serviço REST nao é em formato JSON");
        }
    }

    public function sendJsonGet($token, $key, $url, $data = [])
    {
        $data = http_build_query($data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url . '?' . $data);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERPWD, $token . ":" . $key);
        curl_setopt($ch, CURLOPT_POST, 0);

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($this->contentIsJson($contentType)) {
            return json_decode($response, true);
        } else {
            throw new ErrorException("Resposta do Serviço REST nao é em formato JSON");
        }
    }

    private function contentIsJson($contentType)
    {
        return strpos($contentType, 'application/json') !== false;
    }

    // ---------------- END Method JSON ----------------

    // ---------------- Method XML ----------------
    public function sendXmlPost($url, $body, $header)
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

        $curl_response = curl_exec($curl);

        if ($curl_response === false) {
            $erro = curl_error($curl);
            curl_close($curl);
            throw new ErrorException("Serviço REST nao responde: " . $erro);
        }
        
        curl_close($curl);

        $xml = new SimpleXMLElement($curl_response);

        return $xml;
    }


    // ---------------- END Method XML ----------------


}
