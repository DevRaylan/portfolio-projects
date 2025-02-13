<?php
namespace Geral\Model;

use ErrorException;

//TODO substituir esta classe por outra com funcionalidade similar no módulo Geral?
//TODO parametrizar strings de erro não parametrizadas
final class HttpRoute
{
    //TODO métodos PUT, PATCH e DELETE desativados, reativá-los após testes
    const VALID_HTTP_METHODS = ['GET','POST']; //, 'PUT', 'PATCH', 'DELETE'];
    
    private static function isJsonContent($contentType)
    {
        return strpos($contentType, 'application/json') !== false;
    }
    
    private static function isCsvContent($contentType)
    {
        return strpos($contentType, 'text/csv') !== false;
    }
    
    //TODO testar melhor para os métodos HTTP PUT, DELETE e PATCH
    /**
     * Redireciona uma requisição HTTP para um módulo/controller/action
     * específico do WS UDESC.
     * 
     * Métodos HTTP esperados: GET ou POST. 
     * 
     * Exemplo: Suponha que uma requisição HTTP GET deva ser redirecionada para 
     * o endereço URL:
     * 
     * <pre>https://ws.udesc.br/MeuModulo/MeuController/meuAction</pre>
     * 
     * Neste caso, este método deve ser chamado da seguinte forma:
     *
     * <pre>
     * \Geral\Model\HttpRoute::to('MeuModulo', 'MeuController', 'meuAction', 'GET', $_GET)
     * </pre>
     * 
     * <b>NOTA</b>: Este método recria a requisição original e a executa utilizando
     * as funções <a href="http://php.net/manual/en/ref.curl.php">cURL</a>
     * do PHP.
     * 
     * @param string $module<p>
     * Módulo do WS UDESC para onde será redirecionada a requisição.
     * </p>
     * @param string $controller<p>
     * Controller do WS UDESC para onde será redirecionada a requisição.
     * </p>
     * @param string $action<p>
     * Módulo do WS UDESC para onde será redirecionada a requisição.
     * </p>
     * @param string $method<p>
     * Módulo do WS UDESC para onde será redirecionada a requisição.
     * </p>
     * @param array $data
     * @throws ErrorException
     */
    public static function to($module, $controller, $action, $method, $data = [])
    {
        if (empty($module) || empty($controller) || empty($action)) {
            throw new ErrorException('Erro: URL de redirecionamento não definido.');
        }
        
        $validMethods = self::VALID_HTTP_METHODS;
        
        if (!in_array($method, $validMethods)) {
            throw new ErrorException('Erro: Método HTTP inválido (opções: '.implode($validMethods, ', ').')');
        }
        
        $redirMethod = $_SERVER['REQUEST_METHOD'];        
        
        //em https://stackoverflow.com/a/9031882
        if (empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $protocol = empty($_SERVER['HTTPS']) ? 'https' : 'http';
        } else {
            $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'];
        }
        
        $authToken = $_SERVER['PHP_AUTH_USER'];
        $authKey = $_SERVER['PHP_AUTH_PW'];
        
        $url = $protocol.'://'.$_SERVER['SERVER_NAME'].'/'.$module.'/'.$controller.'/'.$action;
        $data = http_build_query($data);
        
        $curlSession = curl_init();
        
        curl_setopt($curlSession, CURLOPT_URL, ($redirMethod === 'GET' ? $url.'?'.$data : $url));
        curl_setopt($curlSession, CURLOPT_HEADER, 0);
        curl_setopt($curlSession, CURLOPT_USERPWD, $authToken.":".$authKey);
        
        if ($redirMethod === 'POST') {
            curl_setopt($curlSession, CURLOPT_POST, true);
            curl_setopt($curlSession, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, $redirMethod);
            //curl_setopt($curlSession, CURLOPT_HTTPHEADER, ['Content-Length: ' . strlen($data)]);
            curl_setopt($curlSession, CURLOPT_POSTFIELDS, $data);
        }
        
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($curlSession);
        $contentType = curl_getinfo($curlSession , CURLINFO_CONTENT_TYPE);
        $httpCode = curl_getinfo($curlSession , CURLINFO_HTTP_CODE);
        
        curl_close ($curlSession);
        
        if(self::isJsonContent($contentType)) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($httpCode);
            echo $response;
        } else if(self::isCsvContent($contentType)) {
            header('Content-Type: text/csv; charset=utf-8');
            http_response_code($httpCode);
            echo $response;
        } else {
            throw new ErrorException('Erro: A resposta do servidor não está no formato correto (esperado: csv ou json).');
        }
    }
}
