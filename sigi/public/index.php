<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors',1);
date_default_timezone_set('America/Sao_Paulo');

require('../config/config.php'); // Constantes

// Configuração específica para o ambiente em execução (PROD ou HOM ou DEV).
$fileConfig = '../config/config'.$_SERVER['AMBIENTE'].'.php';

if(file_exists($fileConfig)) {
    require($fileConfig);
}

require('../config/autoload.php'); // Lazy loading de classes
require DIR_SYS.'/../libs/doctrine/bootstrap.php'; // Inicia o Doctrine

$GLOBALS['url'] = new Geral\Model\Url($_SERVER['REDIRECT_URL']); // Instanciar URL, $GLOBALS é acessível em qualquer lugar do sistema

// Cria sessão apenas em chamadas não relacionadas a endpoint de WebService.
if(!$GLOBALS['url']->isWebService()) {
    session_start();
}

// Inserir sufixo 'Controller' no nome do controller. Ex: login ==> loginController
$controller = $GLOBALS['url'] -> getNameModule().'\Controller\\'.$GLOBALS['url']->getNameController().'Controller';

// Verificar se existe a classe controller correspondente
if(!class_exists($controller)) {
    $GLOBALS['url'] -> redirect('Index', 'pageNotFound', 'Geral', ['url' => $GLOBALS['url']->getUrlAction()]);
}

// Instanciar controller
$controller = new $controller();

// Pegar nome da ação
$action = $GLOBALS['url'] -> getNameAction();

// Verificar se o controller possui o método correspondente a ação
if(!method_exists($controller, $action)) {
    $GLOBALS['url'] -> redirect('Index', 'pageNotFound', 'Geral', ['url' => $GLOBALS['url']->getUrlAction()]);
}

$controller->$action();
