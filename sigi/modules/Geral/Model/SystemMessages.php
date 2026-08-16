<?php
/**
 * @class SystemMessages
 *
 * Esta classe trata das mensagens do sistema.
 *
 * @version 1.0
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 */

namespace Geral\Model;

//use ErrorException;
use \Geral\Model\Exception\FrameworkException;
use \Geral\Model\Exception\ErrorException;

abstract class SystemMessages
{
    const RESERVED_ATTRIBUTES = ['msg', 'messages', 'result'];
    const RESULT_ERROR_STATUS = 'error';
    const RESULT_SUCCESS_STATUS = 'success';
    const RESULT_STATUS_ACCEPTED = [self::RESULT_SUCCESS_STATUS, self::RESULT_ERROR_STATUS];

    private static $exceptions = [];
    private static $headers = [];
    private static $extraFields = [];
    private static $result;
    private static $msg;
    private static $httpCode = 200;
    private static $outputFormat = 'json';

    private static $formatToMime = [
        'json' => 'application/json;charset=utf-8',
        'csv' => 'text/csv;charset=utf-8'
    ];
    
    public function __construct() { }
    
// ### PRINCIPAL ###############################################################
    
    public static function addException(FrameworkException $e)
    {
        self::$exceptions[] = $e;
    }

    public static function addHeader($header, $value)
    {
        self::$headers[] = $header.': '.$value;
    }

    // Configurar tipo de mensagem de retorno (erro ou sucesso)
    public static function setResultStatus(String $status)
    {
        if(!in_array($status, self::RESULT_STATUS_ACCEPTED)) {
            new ErrorException('Parâmetro "$status" possui valor '.$status.' inválido.');
        }

        self::$result = $status;
    }

    // Configurar tipo de mensagem de retorno (erro ou sucesso)
    public static function setMsg(String $msg)
    {
        self::$msg = $msg;
    }

    // Configurar tipo de mensagem de retorno (erro ou sucesso)
    public static function setHttpCode(Int $httpCode)
    {
        self::$httpCode = $httpCode;
    }

    // Configurar tipo de mensagem de retorno (erro ou sucesso)
    public static function setOutputFormat(String $outputFormat)
    {
        self::$outputFormat = $outputFormat;
    }

    // Adiconar atributos extras na resposta
    public static function addExtraFields(Array $extraFields)
    {
        $hasReservedAttributes = array_intersect(self::RESERVED_ATTRIBUTES, array_keys($extraFields) );

        if(!empty($hasReservedAttributes)) {
            // Não utiliza ErrorException, pois o método que tratar esta exceção pode chamar o Controller::setError().
            throw new \Exception('Os atributos "'
                .implode('","', $hasReservedAttributes).'" são reservados e não podem ser escritos pelas
                funções "setSucces" ou "setError".');
        }

        self::$extraFields = $extraFields;
    }
    
    public static function dispatch()
    {
        self::addHeader('Content-type', self::$formatToMime[self::$outputFormat]);

        $output = self::$extraFields;

        // Se for responder em CSV, então apenas considera os atributos extras.
        if(self::$outputFormat == 'csv') {            
            try {
                $opc = null;
                $converter = new FormatConverter(self::$outputFormat);
                $output = $converter -> getResult($output, $opc);
            } catch (\Exception $e) {
                return null;
                //throw new \Exception($e -> getMessage(), 1);
            }
        } else {
            // Definir atributos padrões de resposta
            $output['result'] = self::$result;
            $output['msg'] = self::$msg;
            $output['code']   = self::$httpCode; // Por padrão, utilizar 200 (OK)
            $output['messages'] = [];

            if(self::$result == self::RESULT_ERROR_STATUS) {
                $output['msg'] = [];

                foreach(self::$exceptions as $e) {
                    // Se tiver código http específico do erro, substituir código padrão (200)
                    if(!empty($e -> getHttpCode())) {
                        $output['code'] = $e -> getHttpCode();
                    }
                    
                    $output['messages'][] = [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage(),
                        'detail' => $e->getDetail(),
                        'docLink' => $e->getDocLink(),
                        'url' => $e->getUrl()
                        //'file' => $e -> getFile(),
                        //'line' => $e -> getLine()
                    ];

                    // Juntar as mensagens para posterior concatenação
                    $output['msg'][] = $e->getMessage();
                }

                /**
                 * Concatenar mensagens com quebra de linha, assim o "msg" fica compatível com o "msg" 
                 * de mensagens de sucesso (result="success")
                 */
                $output['msg'] = implode("\n", $output['msg']);
            }
        }

        // Se for requisição de serviços web, então retornar o erro em formato JSON
        if($GLOBALS['url']->isWebService()) {
            self::dispatchToWebService($output);
        }
        // Respostas de sucesso sempre são feitas no formato JSON
        elseif(self::$result == self::RESULT_SUCCESS_STATUS || $GLOBALS['url']->isAjax()) {
            self::dispatchToAjax($output);
        }
        // redireciona para a tela de erro html
        else {
            self::dispatchToErrorPage($output);
        }
    }
    
    private static function dispatchToErrorPage(&$output)
    {
        if(strpos($output['messages'][0]['code'], 'V') === 1) {
            //echo 'http'.$GLOBALS['url']->getUrlModule('Geral', true).'/Message/error?'.http_build_query($output);
            echo '<script>document.location="/Geral/Message/error?'.http_build_query($output).'"</script>';
        } else {
            $GLOBALS['url']->redirect('Message', 'error', 'Geral', $output);
        }
    }
    
    private static function dispatchToWebService(&$output)
    {
        // Setar código HTTP de retorno
        http_response_code(self::$httpCode);

        foreach(self::$headers as $header) {
            header($header);
        }

        if(self::$outputFormat == 'csv') {
            echo $output;
        } else {
            // Imprimir JSON formatado
            echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }
    
    private static function dispatchToAjax(&$output)
    {
        // Setar código HTTP de retorno
        http_response_code(self::$httpCode);

        foreach(self::$headers as $header) {
            header($header);
        }
        
        // Imprimir JSON formatado
        echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
