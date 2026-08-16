<?php
/**
* Configurações aplicáveis apenas no ambiente de Produção (PROD) da aplicação.
* No apache deve estar setado a constant AMBIENTE para "PROD".
* Ex: <site>.conf deve ter uma linha com "SetEnv AMBIENTE PROD"
*/

// ############################################################
// Configurações do serviço de conexão com o sistema SGBR
// ############################################################
$config['authSGBR'] = [
    'sgbrToken' => '31b9d7b4c564cab007b9e90b6a4ec213',
    'sgbrKey' => '93822d35132d24be2d7f2a3d93457c35aaacc3c5',
    'endpointUrl' => 'https://sgbr.sistemas.udesc.br'
];
