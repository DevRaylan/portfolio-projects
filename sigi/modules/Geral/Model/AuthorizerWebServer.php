<?php
namespace Geral\Model;
use \Geral\Model\Interfaces\IAuthorizer;
use \Geral\Model\Config;
use \Geral\Model\UserSession;

class AuthorizerWebServer extends AbstractAuthorizer {
    private $config;

    public function __construct($authScheme = 'userId')
    {
        if (! ( $authScheme != null && is_string($authScheme) && in_array($authScheme, parent::validAuthSchemes) ) ) {
            die('AuthorizerWebServer: Esquema de autenticação inválido.');
        }

        $configAuthWebServer = Config::getValue('authWebServer');

        if( empty($configAuthWebServer['tokenSystem']) ) {
            die('Token do sistema não existente.');
        }

        $data = [
            'sistema' => $configAuthWebServer['tokenSystem'],
            'usuario' => $authScheme == 'userId' ? UserSession::getParam('id') : $_SERVER['PHP_AUTH_USER']
        ];

        $response = $this -> getResponseJsonFromServer($configAuthWebServer['url'], $data, $configAuthWebServer['tokenSecret'], 'acessos');
        $baseAuthorizer = [
            'userId' => $data['usuario'],
            'transactions' => [$data['usuario'] => $response[$configAuthWebServer['transactionsField']]],
            'data' => [$data['usuario'] => $response[$configAuthWebServer['dataField']]],
            'profile' => [$data['usuario'] => $response[$configAuthWebServer['profilesField']]],
            'username' => [$data['usuario'] => $response[$configAuthWebServer['usernameField']]]
        ];

        parent::__construct($baseAuthorizer, $authScheme);
    }

    private function getResponseJsonFromServer($url, Array $data, $token, $fieldTag, $method = 'POST')
    {
        $data = http_build_query($data);

        if (is_null($token))
            $authBasic = "";
        else
            $authBasic = "Authorization: Basic " . base64_encode($token) . "\r\n";

        $opts = array('http' =>
            array(
                'method'  => $method,
                'header'=> "Content-type: application/x-www-form-urlencoded\r\n"
                         . "Content-Length: " . strlen($data) . "\r\n"
                         . $authBasic,
                'content' => $data
            )
        );

        $context  = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);
        $response_j = json_decode($response, true);

        if($response_j['result'] == 'success') {
            return ($fieldTag ? $response_j[$fieldTag] : $response_j);  // 'acessos'

        }
        else {
            die("AuthorizerWebServer::Resposta do servidor inválida. \n".$response."\n");
        }
    }
}
