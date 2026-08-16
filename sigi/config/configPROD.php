<?php
/**
* Configurações aplicáveis apenas no ambiente de produção (PROD) da aplicação.
* No apache deve estar setada a constante AMBIENTE para "PROD".
* Ex: <site>.conf deve ter uma linha com "SetEnv AMBIENTE PROD"
*/

define( 'TIPO_LOGIN', 'Ldap' );

// Dados de conexão com Bando de Dados
define( 'BD_DRIVER', 'pdo_sqlite' );
define( 'BD_HOST', '' );
define( 'BD_USER', '' );
define( 'BD_PASSWORD', '' );
define( 'BD_NAME', 'mvc' );
define( 'BD_PATH', DIR_FILES . '/Geral/' . DIR_NAME_DIVERSOS . '/' . BD_NAME . '.sqlite' );

// Utilizado apenas se o driver for pdo_sqlite
define( 'BD_CHARSET', 'utf8' );
define( 'USE_DOCTRINE', 1 ); // 0 => Não | 1 => Sim

// Dados de conexão com LDAP, utilizado caso TIPO_LOGIN tenha valor "Ldap"
define( 'LDAP_HOST', 'ldap.example.local' );
define( 'LDAP_DOMINIO', 'dc=example,dc=local' );

// Chave para deploy no ambiente de Produção via Jenkins, pode ser solicitado ao Setor de Datacenter/SETIC.
define('DEPLOY_KEY', '');

// Não mostrar errors de Exceção na tela, apenas em logs do servidor
ini_set('display_errors', 0);

// Configurações do Servidor Websocket
define('WEBSOCKET_HOST', 'websocket.example.local');
define('WEBSOCKET_PORT', '8080');

//Configurações de autenticação do Websocket
define('APP_WEBSOCKET_ID',     '');
define('APP_WEBSOCKET_KEY',    '');
define('APP_WEBSOCKET_SECRET', '');