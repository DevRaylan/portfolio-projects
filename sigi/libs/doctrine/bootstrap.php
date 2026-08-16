<?php
use \Doctrine\Common\Util\Debug;

function dump($valor, $tipo = 1, $exit = false) {
    echo '<pre>';
    if($tipo == 2) {
        var_dump($valor);
    }
    elseif($tipo == 3) {
        print_r($valor);
    }
    else {
        Debug::dump($valor);
    }
    
    echo '</pre>';
    
    if($exit) exit;
}

// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$modules = scandir(DIR_MODULES);

foreach($modules as $i => $module) {
    $dir = DIR_MODULES. '/' . $module . '/Entity';
    if(!is_dir($dir)) {
        unset($modules[$i]);
    } else {
        $modules[$i] = $dir;
    }    
}

//$config = Setup::createAnnotationMetadataConfiguration([DIR_MODULES."/Usuario/Entity"], $isDevMode);
$config = Setup::createAnnotationMetadataConfiguration($modules, $isDevMode);

// database configuration parameters
$conn = array(
    'driver'   => BD_DRIVER,
	'host'	   => BD_HOST,
    'user'     => BD_USER,
    'password' => BD_PASSWORD,
    'dbname'   => BD_NAME,
	'charset'  => BD_CHARSET,
	'path'     => BD_PATH
);

$GLOBALS['config'] = &$config;

// obtaining the entity manager
$em = EntityManager::create($conn, $config);
$platform = $em->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');

$GLOBALS['em'] = $em;

// Manter a configuração global para utilização na instanciação de um EntityManager em outras aplicações.
// Exemplo: ToolDoctrine para sincronizar entidades de schemas diferentes.
$GLOBALS['config'] = $config;
