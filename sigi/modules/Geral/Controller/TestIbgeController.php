<?php

namespace Geral\Controller;

class TestIbgeController extends AbstractTestController
{
    function __construct()
    {
        parent::__construct();

        $this->setTestConfig('POST', [
            '/Geral/Ibge/getAllEstados/' => [
                'data' => '{ }',
                'dataAttrToUrl' => ['']
            ],
            '/Geral/Ibge/getEstadoPorId' => [
                'data' => '{ 
                    "id": "42"}',
                'dataAttrToUrl' => ['id']
            ],
            '/Geral/Ibge/getEstadoPorSigla' => [
                'data' => '{ 
                    "sigla": "SC"}',
                'dataAttrToUrl' => ['sigla']
            ],
            '/Geral/Ibge/getMunicipiosPorEstado' => [
                'data' => '{ 
                    "id": "42"}',
                'dataAttrToUrl' => ['id']
            ],
            '/Geral/Ibge/getMunicipioPorId' => [
                'data' => '{ 
                    "id": "4211900"}', //Palhoça
                'dataAttrToUrl' => ['id']
            ],
            '/Geral/Ibge/getEnderecoPorCep' => [
                'data' => '{ 
                    "cep": "88035901"}', 
                'dataAttrToUrl' => ['cep']
            ],
            '/Geral/Ibge/getPaisPorNome' => [
                'data' => '{ 
                    "nome": "Brasil"}', 
                'dataAttrToUrl' => ['nome']
            ]
        ]);
    }
}
