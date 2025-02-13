<?php

/**
 * @class IbgeClient
 *
 * Esta classe abstrai a chamada para o serviço de localidades do IBGE: 
 * https://servicodados.ibge.gov.br/api/docs/localidades
 *
 * @version 1.0
 * @author Mayara Madeira Trevisol <mayara.trevisol@udesc.br>
 */

namespace Geral\Model\Services;

use ErrorException;

abstract class IbgeClient
{
    
    const URL_ESTADO = "https://servicodados.ibge.gov.br/api/v1/localidades/estados";
    const URL_ESTADO_ID = "https://servicodados.ibge.gov.br/api/v1/localidades/estados/{UF}";
    const URL_MUNICIPIO_UF = "https://servicodados.ibge.gov.br/api/v1/localidades/estados/{UF}/municipios";
    const URL_MUNICIPIO_ID = "https://servicodados.ibge.gov.br/api/v1/localidades/municipios/{MUNICIPIO}";


    static public function getEstados()
    {
        $url = self::URL_ESTADO;

        return self::getIbgeJson($url);
    }

    static public function getEstadoPorId($id)
    {
        $url = str_replace("{UF}", $id, self::URL_ESTADO_ID);

        return self::getIbgeJson($url);
    }

    static public function getMunicipiosPorEstado($idUf)
    {
        $url = str_replace("{UF}", $idUf, self::URL_MUNICIPIO_UF);

        return self::getIbgeJson($url);
    }

    static public function getMunicipioPorId($id)
    {
        $url = str_replace("{MUNICIPIO}", $id, self::URL_MUNICIPIO_ID);

        return self::getIbgeJson($url);
    }

    // ---------------- API IBGE ----------------

    static private function getIbgeJson($url)
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
