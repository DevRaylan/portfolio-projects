<?php
/**
* Configurações aplicáveis apenas no ambiente de Desenvolvimento (DEV) da aplicação.
* No apache deve estar setado a constant AMBIENTE para "DEV".
* Ex: <site>.conf deve ter uma linha com "SetEnv AMBIENTE DEV"
*/

// ############################################################
// Configurações do serviço de conexão com o sistema SGBR
// ############################################################
$config['authSGBR'] = [
    'sgbrToken' => 'ee2f95bc8ac315ab7d7a40403f089cc1',
    'sgbrKey' => '21d483cf3db80c0738ae6f25f48921741c4573a2',
    'endpointUrl' => 'https://sgbr.dev.udesc.br'
];

$config['menu']['Admin'][] = [
    'descricao' => 'Simular dados de usuários',
    'url' => $GLOBALS['url'] -> getUrlModule().'/UserDataSimulator/index',
    'glyphicon' => 'edit',
    'transactions' => ['admin']
];

$config["menu"]['LocalAuthorizer'] = 
$config["menu"]['UserDataSimulator'] = 
$config["menu"]['WebClient'] =
$config["menu"]['ConfigConn'] =
$config["menu"]['TestFileRepository'] = 
$config["menu"]['FileRepository'] = 
$config["menu"]['RegistroSimples'] = 
$config["menu"]['BasicData'] =
&$config["menu"]['Admin'];
