<?php
namespace Geral\Model\Exception;

use \Geral\Model\SystemMessages;
use \Geral\Model\Config;
use \Geral\Model\Factory\LogFactory;

/**
 * Exceções do framework UDESC
 *
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 */
abstract class FrameworkException extends \Exception
{
    /**
    * Atributo definido nas classes filhas.
    *
    * @see $ACCEPTED_TYPES
    */
    protected $prefix;
    
    /**
    * Camada de origem da exceção.
    *
    * @see $ACCEPTED_LAYERS
    */
    private $layer = 'G'; // Padrão global
    
    /**
    * Código identificar do erro no arquivo errors.csv da aplicação.
    * O código pode se repetir entre aplicações, mas nunca numa mesma aplicação.
    */
    protected $code;
    
    /**
    * Código HTTP para o cabeçalho de resposta.
    * Se o valor for 0 (zero), será utilizado o código padrão 200.
    */
    protected $httpCode;
    
    /**
    * Endereço de documentação para detalhar o erro ou possível solução.
    */
    protected $docLink; // Url com orientação de suporte ao usuário.
    
    /**
    * Maiores detalhes sobre o erro.
    */
    protected $detail; // Mensagem
    
    /**
    * Endereço da requisição que originou o erro.
    */
    protected $url;
    
    /**
    * Colunas que devem existir no arquivo "errors.csv":
    * - code: Inteiro => Código identificador do erro
    * - httpCode: Inteiro => Código http para ser enviado no cabeçalho de resposta (ex: 400 Not Found, etc)
    * - message: Texto => Conteúdo textual da mensagem
    * - detail: Texto => Conteúdo textual mais detalhado sobre o error
    * - docLink: Texto => Url com maiores detalhes sobre o erro na documentação da aplicação
    */
    static private $FILE_ERROR_COLUMNS = [
        'code',
        'httpCode',
        'message',
        'detail',
        'docLink'
    ];
    
    // Tipos de mensagem aceitos, as keys são o prefixo (prefix)
    static private $ACCEPTED_TYPES = [
        'A' => 'Alert',
        'W' => 'Warning',
        'E' => 'Error'
    ];
    
    // Mapeamento do local de origem do erro para a letra equivalente.
    static private $ACCEPTED_LAYERS = [
        DIRECTORY_SEPARATOR.'Model'.DIRECTORY_SEPARATOR => 'M',
        DIRECTORY_SEPARATOR.'Entity'.DIRECTORY_SEPARATOR => 'E',
        DIRECTORY_SEPARATOR.'Controller'.DIRECTORY_SEPARATOR => 'C',
        DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR => 'V'
        //'/*/' => 'G' // Padrão é ser 'G'lobal
    ];
    
    public function __construct($code, $app = null)
    {
        if(empty($app)) {
            $app = $GLOBALS['url'] -> getNameModule();
        }
        
        // Se for código, pegar erro da base CSV
        $error = self::getError($code, $app);
        $this->docLink = $error['docLink'];
        $this->httpCode = $error['httpCode'];
        $this->detail = $error['detail'];
        $this->url = $GLOBALS['url']->getFullUrl();
        
        // Caso a mensagem não tenha código, então utiliza a mensagem personalizada
        if(empty($error['code'])) {
            $error['message'] = $code;
            $code = 0;
        }
        
        parent::__construct($error['message'], $error['code']);

        if($this->prefix == 'E') {
            LogFactory::error($error['message']);
        }
        
        foreach(self::$ACCEPTED_LAYERS as $dir => $layer) {
            if(strpos($this->file, $dir) !== false) {
                $this->layer = $layer;
            }
        }
        
        $this -> code = $this->prefix.$this->layer.' - '.$code;
        SystemMessages::addException($this);
    }
    
    protected function getLayer()
    {
        return $this->layer;
    }

    public function getHttpCode()
    {
        return $this -> httpCode;
    }
    
    public function getDocLink()
    {
        return $this -> docLink;
    }
    
    public function getDetail()
    {
        return $this -> detail;
    }
    
    public function getUrl()
    {
        return $this -> url;
    }
    
    public function __toString()
    {
        return __CLASS__ . " ({$this->code}) {$this->message}\n";
    }
    
//==============================================================================
    
    /**
    * Gerencia os arquivos de erros por aplicação (config/errors.csv)
    *
    * @param $code Inteiro Código do erro no arquivo CSV
    * @param $app String Aplicação para busca do arquivo de erros
    */
    private static function getError($code, $app)
    {
        // Erro com mensagem customizada, do código
        if(!is_numeric($code)) {
            // Pegar o erro zero, com dados vazio.
            return self::getError(0, 'Geral');
        }
        
        $fileCsvErrors = DIR_MODULES.'/'.$app.'/'.Config::DIR_CONFIG.'/errors.csv';
        
        if(!file_exists($fileCsvErrors)) {
            // Arquivo de erro não encontrado. (Código 2 da aplicação Geral)
            return self::getError(2, 'Geral');
        }
        
        $fp = fopen($fileCsvErrors, 'r');
        $columns = fgetcsv($fp);
        
        while(($error = fgetcsv($fp)) != false) {
            // Ignorar linhas de comentário
            if(strpos($error[0],'#') === 0) {
                continue;
            }
            
            if($error[0] == $code) {
                $errorFile = array_combine($columns, $error);
                $error = [];
                
                foreach(self::$FILE_ERROR_COLUMNS as $column) {
                    if(!array_key_exists($column, $errorFile)) {
                        // Campo ausente no arquivo de erros. (Código 3 da aplicação Geral)
                        return self::getError(3, 'Geral');
                    }
                    
                    $error[$column] = $errorFile[$column];
                }
                
                return $error;
            }
        }
        
        // Código não encontrado, então pega o erro 1 do arquivo Geral
        return self::getError(1, 'Geral');
    }
}

