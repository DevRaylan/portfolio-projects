<?php
/**
* Configurações aplicáveis apenas no ambiente de Homologação (HOM) da aplicação.
* No apache deve estar setado a constant AMBIENTE para "HOM".
* Ex: <site>.conf deve ter uma linha com "SetEnv AMBIENTE HOM"
*/

// ############################################################
// Configurações do serviço de conexão com o sistema SGBR
// ############################################################
$config['authSGBR'] = [
    'sgbrToken' => '31b9d7b4c564cab007b9e90b6a4ec213',
    'sgbrKey' => '93822d35132d24be2d7f2a3d93457c35aaacc3c5',
    'endpointUrl' => 'https://sgbr.hom.udesc.br'
];
