<?php

/**
 * @class GeradorBoleto
 *
 * Esta classe oferece funcionalidades para geração e consulta de boletos no sistema SGBR.
 * 
 * @version 1.0
 * @author Mayara Madeira Trevisol <mayara.trevisol@udesc.br>
 */

namespace Geral\Model\Services;

use ErrorException;
use \Geral\Model\Config;
use \Geral\Model\Services\RestClient;

class GeradorBoleto
{
    // Configurações do WS de Boletos - SGBR
    const ENDPOINT_PRIVATE = 'SGBR/WSv1';
    const ENDPOINT_PUBLIC = 'SGBR/Public';
    const URL_SERVICE = 'service';
    const URL_BOLETO = 'boleto';
    const URL_VIEWBOLETO = 'view1ViaBoleto';

    static public $FIELDS_SACADO_BOLETO = ['nome', 'cpf', 'endereco', 'cidade', 'uf', 'cep'];

    private $restClient;
    private $endpointUrl;
    private $token;
    private $key;

    static $relacaoUfCep = [
        'AC' => [[69900, 69999]],
        'AL' => [[57000, 57999]],
        'AM' => [[69000, 69299], [69400, 69899]],
        'AP' => [[68900, 68999]],
        'BA' => [[40000, 48999]],
        'CE' => [[60000, 63999]],
        'DF' => [[70000, 72799], [73000, 73699]],
        'ES' => [[29000, 29999]],
        'GO' => [[72800, 72999], [73700, 76799]],
        'MA' => [[65000, 65999]],
        'MG' => [[30000, 39999]],
        'MS' => [[79000, 79999]],
        'MT' => [[78000, 78899]],
        'PA' => [[66000, 68899]],
        'PB' => [[58000, 58999]],
        'PE' => [[50000, 56999]],
        'PI' => [[64000, 64999]],
        'PR' => [[80000, 87999]],
        'RJ' => [[20000, 28999]],
        'RN' => [[59000, 59999]],
        'RO' => [[76800, 76999]],
        'RR' => [[69300, 69399]],
        'RS' => [[90000, 99999]],
        'SC' => [[88000, 89999]],
        'SE' => [[49000, 49999]],
        'SP' => [[1000, 19999]],
        'TO' => [[77000, 77999]]
    ];


    public function __construct()
    {
        $authSgbr = Config::getValue('authSGBR', 'Geral');
        $this->token = $authSgbr['sgbrToken'];
        $this->key = $authSgbr['sgbrKey'];
        $this->endpointUrl = $authSgbr['endpointUrl'];
        $this->restClient = new RestClient();
    }

    /* 
    * --------------------------- Funções de negócio WS SGBR ------------------------------------
    */

    /**
     * Parametros:
     *      $idBoletoService - Integer - codigo do serviço disponivel no SGBR
     *      $data - Array - mapeamento chave valor dos campos necessários para geração do boleto: ['nome', 'cpf', 'endereco', 'cidade', 'uf', 'cep']
     */
    public function criarBoleto($idBoletoService, $data)
    {
        // Verificar se todos os campos estão presentes e possuem valor
        foreach (self::$FIELDS_SACADO_BOLETO as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        $data['idBoletoService'] = $idBoletoService;

        // Retorna o token de acesso ao boleto gerado.
        $result = $this->restClient->sendJsonPost($this->token, $this->key, $this->getUrlBoleto(), $data);

        if ($result['result'] == 'success') {
            return $result['tokenAcesso'];
        } else {
            throw new ErrorException("SGBR retornou erro: " . $result['msg']);
        }
    }


    public function getUrlBoletoPorTokenAcesso($tokenAcesso)
    {
        return $this->getUrlBoletoByTokenAcesso($tokenAcesso);
    }

    
    static public function isCepValidoPorUf($cep, $uf)
    {
        $prefix = substr($cep, 0, -3);

        if (is_array(self::$relacaoUfCep[$uf])) {
            foreach (self::$relacaoUfCep[$uf] as $faixas => &$faixa) {
                if ($prefix >= $faixa[0] && $prefix <= $faixa[1]) {
                    return true;
                }
            }
        }

        unset($faixa);
        return false;
    }

    /* 
    * --------------------------- Funções Privadas Auxiliares ------------------------------------
    */

    private function getUrlBoletoByTokenAcesso($tokenAcesso)
    {
        return $this->endpointUrl . '/' . self::ENDPOINT_PUBLIC . '/' . self::URL_VIEWBOLETO . '/' . $tokenAcesso;
    }
    
    private function getUrlBoleto()
    {
        return $this->endpointUrl . '/' . self::ENDPOINT_PRIVATE . '/' . self::URL_BOLETO;
    }
}
