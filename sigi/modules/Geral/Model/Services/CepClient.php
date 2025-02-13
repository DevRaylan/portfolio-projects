<?php

/**
 * @class CepClient
 *
 * Esta classe abstrai a chamada para o serviço de busca de CEP: 
 * https://viacep.com.br/
 * https://postmon.com.br/
 * 
 *
 * @version 1.0
 * @author Mayara Madeira Trevisol <mayara.trevisol@udesc.br>
 */

namespace Geral\Model\Services;

use ErrorException;

abstract class CepClient
{
    
    const URL_VIACEP = "https://viacep.com.br/ws/{CEP}/json/";
    const URL_POSTMON = "https://api.postmon.com.br/v1/cep/{CEP}";


    static public function getEnderecoPorCep($cep)
    {
        $url = str_replace("{CEP}", $cep, self::URL_VIACEP);

        return self::getCepJson($url);
    }

    // ---------------- API IBGE ----------------

    static private function getCepJson($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 0);

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if (self::contentIsJson($contentType)) {
            return json_decode($response, true);
        } else {
            throw new ErrorException("Resposta do Serviço REST nao é em formato JSON");
        }

    }

    static private function contentIsJson($contentType)
    {
        return strpos($contentType, 'application/json') !== false;
    }
}
