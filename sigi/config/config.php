<?php

/**
 * Variáveis definidas para todos os ambientes da aplicação (DEV, HOM, PROD).
 * No apache deve estar setada a constante AMBIENTE para um dos seguintes valores:
 * - DEV: ambiente de desenvolvimento
 * - HOM: ambiente de homologação
 * - PROD: ambiente de produção
 *
 * Ex: No apache o arquivo site-available/<site>.conf deve ter uma linha com "SetEnv AMBIENTE DEV"
 * para o ambiente de desenvolvimento da aplicação.
 */

// Diretório raiz do sistema
define('DIR_ROOT', __DIR__ . '/..');
define('DIR_SYS', __DIR__ . '/../public');
define('DIR_MODULES', __DIR__ . '/../modules');

// Diretório das Bibliotecas
define('DIR_LIBS', '/libs'); // Bibliotecas server-side
define('DIR_PUBLIC_LIBS', '/libs'); // Bibliotecas client-side (visíveis a partir de /public)

// Diretório público de uploads, acessível pelo browser antes de salvar arquivos definitivamente
define('DIR_NAME_FILES', 'files');
define('DIR_NAME_LOG', 'log');
define('DIR_NAME_DIVERSOS', 'diversos');
define('DIR_NAME_TEMP', 'temp');
define('DIR_NAME_DOWNLOADS', 'downloads');
define('DIR_FILES', DIR_ROOT . '/' . DIR_NAME_FILES);

// Realizar login utilizando Ldap, Mysql, Local ou True
define('DIR_VIEWS', 'View');
define('DIR_VIEWS_DESKTOP', DIR_VIEWS . '/desktop');
define('DIR_VIEWS_MOBILE', DIR_VIEWS . '/mobile');

// Assinatura de dispositivos móveis
//define( 'ASSINATURA_MOBILES', 'android|blackberry|palm|tablet|webos');
define('ASSINATURA_MOBILES', '');

/**
 * Caso o usuário não tenha transações nas aplicações do framework, então é apresentada uma mensagem de usuário não
 * cadastrado. Caso esta constante tenha um valor, é mostrada também a frase "Solicitar acesso" com link para o
 * local de solicitação de acesso. Exemplo: link no OTRS.
 * 
 * Exemplo: Solicitação de acesso ao SGBR através de uma abertura de chamado no OTRS com uns campos pré-preenchidos.
 * define( 'URL_SOLICITAR_ACESSO', 'https://chamados.udesc.br/otrs/customer.pl?Action=CustomerTicketMessage;Dest=19||REITORIA::INFORMATICA::PORTAIS%20CORPORATIVOS;ServiceID=90;Subject=Solicita%C3%A7%C3%A3o+de+acesso');
 **/
define('URL_SOLICITAR_ACESSO', '');

// Token de acesso ao WebService de autorizações
define('AUTH_SERVER_TOKEN', 'mvc');

// Aplicação default que o usuário será redirecionado após login
define('DEFAULT_APP', 'Sigcinf');

// Para que o menu do topo não seja clicável e não mostre os ícones dos módulos
// setar para false (exemplo: quando há apenas um módulo)
define('EXPAND_TOP_MENU', true);

ini_set('memory_limit', '500M');

define('SMTP_HOST', 'relay.udesc.br');
define('SMTP_PORT', 25);
define('SMTP_USER', '');
define('SMTP_PASSWORD', '');
define('SMTP_FROM', 'cinf.ceart@udesc.br');
//define('SMTP_FROM', 'noreply@udesc.br');
define('SMTP_FROM_NAME', 'Sistema de Empréstimos - CEART - UDESC');
// Limite maximo default para consultas de log/auditoria
define('LIMIT_LOG_AUDIT', 500);
