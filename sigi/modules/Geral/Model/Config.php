<?php
/**
 * @class Config
 *
 * Faz o carregamento das variáveis do arquivo config.php de cada aplicação/módulo.
 * 
 * Obs: Módulo e aplicação são sinônimos neste framework.
 *
 * @version 1.0
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 */

namespace Geral\Model;

use \Geral\Model\Factory\AuthorizerFactory;

abstract class Config
{
    //Nome do diretório dos arquivos com as classes entidades.
    const DIR_ENTITY = 'Entity';

    //Nome do diretório dos arquivos de configuração da aplicação.
    const DIR_CONFIG = 'config';

    // Configurações contidas no diretório dos arquivos de configuração das aplicações.
    static private $configs;
    
    public function __construct() { }
    
    /**
     * Faz o carregamento das variáveis do config.php da aplicação. O arquivo deve conter a variável $config do tipo
     * array e seus elementos as variáveis.
     * Exemplo: $config[<nome da variavel>] = valor (mixed)
     * 
     * @param $campos Utilizado para retornar apenas os campos informados.
     * @param $module Aplicação a ser selecionada para ler o respectivo arquivo de configuração. Caso não seja passada,
     * é utilizada a aplicação de acordo com a requisição.
     * 
     * @return Array Variáves do config.
     */
    static private function &loadConfig($campos = [], $module = null)
    {
        // Se $module for nullo, então é utilizado o nome do módulo corrente( Referente a requisição em execução).
        if(empty($module)) $module = $GLOBALS['url'] -> getNameModule();

        // Se a configuração do módulo já foi carregada, então retorna seus valores.
        if(empty(self::$configs[$module])) {
            // Configuração para todos os ambientes (PROD, HOM e DEV)
            $fileConfig = DIR_MODULES . '/' . $module. '/'.self::DIR_CONFIG.'/config.php';
            $config = [];

            if(file_exists($fileConfig)) {
                require($fileConfig);
            }

            // Configuração específica para o ambiente em execução (PROD ou HOM ou DEV).
            $fileConfig = DIR_MODULES . '/' . $module. '/'.self::DIR_CONFIG.'/config'.$_SERVER['AMBIENTE'].'.php';
            
            if(file_exists($fileConfig)) {
                require($fileConfig);
            }

            // Salvar as configuração para futuras requisições
            self::$configs[$module] = $config;
        } else {
            $config =  self::$configs[$module];
        }

        // Filtrar os campos caso o parâmetros campos seja utilizado.
        if(!empty($campos)) {
            foreach($config as $campo => &$valor) {
                // Se não for o campo desejado, então o remove.
                if(!in_array($campo, $campos)) unset($config[$campo]);
            }
        }

        // Retorna as configurações do módulo
        return $config;
    }

    /**
     * Carregar as constantes da aplicação.
     * 
     * @param $name String Nome da constante para retornar o valor
     * @param $module String Aplicação a qual deseja-se carregar o config.php. Caso não seja passada, é utilizada a aplicação
     * de acordo com a requisição.
     * 
     * @return String Valor de uma constante
     * @see loadConfig()
     */
    static private function &loadConstants($module)
    {
        // Se a configuração do módulo já foi carregado, então retorna seus valores.
        if(empty(self::$configs[$module])) {
            // Carregar as configurações caso tenha sido chamado uma constante antes de uma variável de configuração
            self::loadConfig([], $module);
        }
    }

    /**
     * Retorna as configurações de todas as aplicações.
     * A chave do array é o nome da aplicação.
     * 
     * @param $campos Utilizado para retornar apenas os campos informados.
     * 
     * @return Array Variáveis do config.
     */
    static function getAllConfigs(Array $campos = null, $app = null)
    {
        $configs = [];
        if (isset($app)) {
            $configs[$app] = self::loadConfig($campos, $app);
        } else {
            $modules = scandir(DIR_MODULES);
            array_shift($modules);
            array_shift($modules);
    
            foreach($modules as $module) {
                $configs[$module] = self::loadConfig($campos, $module);
            }    
        }

        return $configs;
    }

    /**
     * Retorna a configuração de uma aplicação específica.
     * 
     * @see self::loadConfig()
     */
    static function getConfig(Array $campos = null, $module = false)
    {
        $configs = self::loadConfig($campos, $module);

        if($campos) {
            $config = [];

            foreach($campos as $campo) {
                $config[$campo] = $configs[$campo];
            }

            return $config;
        }
        else {
            return $configs;
        }
    }

    /**
     * Retorna uma constante da aplicação.
     *
     * @param String $campos Nome da constante.
     * @param String $module Nome da aplicação para a leitura do respectivo arquivo.
     *
     * @return Array
     **/
    static function getConstant($name = null, $module = false)
    {
        $newName = self::constantExist($name, $module);

        if($newName) {
            $val = constant($newName);
            return $val;
        } else die('Constante "'.$name.'" não existe no config.php da aplicação.');
    }

    static function constantExist($name = null, $module = false)
    {
        if(empty($module)) $module = $GLOBALS['url'] -> getNameModule();

        self::loadConstants($module);

        $name = $module.'_'.$name;

        return defined($name) ? $name : null;
    }
    

    /**
     * Retorna o valor de um campo das configurações de uma aplicação.
     * Exemplo: se no config.php da aplicação tenha $config['nome'], se utilizado
     * 
     * @param $campos Utilizado para retornar apenas os campos informados.
     * @param $module Aplicação a qual deseja-se carregar o config.php. Caso não seja passada, é utilizada a aplicação
     * de acordo com a requisição.
     * 
     * @return Mixed Array ou null caso a variável não seja encontrada.
     */
    static function getValue($campo, $module = null)
    {
        $config = self::loadConfig([$campo], $module);

        return isset($config[$campo]) ? $config[$campo] : null;
    }

    /**
     * Retorna todas as classes presentes no diretório de classes entidades do framework.
     * Obs: mover este método para o model ToolDoctrine.
     * 
     * @return Array Nome das classes com namespace.
     */
    static public function getAllEntities($byModule = true)
    {
        $namespace = str_replace('/', '\\', $dirModules).'\\';
        $modules = self::getAppNames();
        $entities = [];

        foreach($modules as &$module) {
            $dirEntities = '/' . $module . '/' . self::DIR_ENTITY;

            if(!is_dir(DIR_MODULES . $dirEntities)) {
                $entities[$module] = [];
                continue;
            }

            $classFiles = scandir(DIR_MODULES . $dirEntities);
            
            // Retira . e .. da lista de arquivos.
            array_shift($classFiles);
            array_shift($classFiles);

            foreach($classFiles as &$classFile) {
                // Retira a extensão .php do arquivo
                if($byModule) {
                    $entities[$module][] = $dirEntities . '/' . substr($classFile, 0, strpos($classFile, '.'));
                } else {
                    $entities[] = $dirEntities . '/' . substr($classFile, 0, strpos($classFile, '.'));
                }
            }

            unset($classFile);
        }

        return $entities;
    }

    /**
     * Retorna o menu da aplicação corrente, ignorando itens restritos ao usuário logado.
     * O formato do array de retorno possui a estrutura [<controller>][<indice>][<item_menu>].
     * <item_menu> pode possuir a variável "links", que conterá outros item_menu.
     * 
     * Obs: mover este método para uma classe global de gerenciamento das aplicações. Sugestão: criar classe "Framework".
     * 
     * @param String $module Nome da aplicação
     * 
     * @return Array
     */
    static function getMenuApplication($module = null)
    {
        if(!UserSession::isEmpty()) {
            $auth = AuthorizerFactory::create();
        } else {
            $auth = null;
        }

        $menu = self::getValue('menu');

        foreach($menu as $controller => $links) {
            foreach($links as $i => $link) {
                // Se o link do menu tiver restrições de acesso então faz a devida checagem.
                if(!empty($link['transactions']) && (!$auth || !$auth -> isAllowedTransactions($link['transactions']))) {
                    unset($menu[$controller][$i]);
                    continue;
                }

                if(!empty($link['links'])) {
                    foreach($link['links'] as $j => $subLink) {
                        // Se o link do submenu tiver restrições de acesso então faz a devida checagem.
                        if(!empty($subLink['transactions']) && (!$auth || !$auth -> isAllowedTransactions($subLink['transactions']))) {
                            unset($menu[$controller][$i]['links'][$j]);
                            continue;
                        }
                    }
                }
            }
        }

        return $menu;
    }

    /**
     * Retorna o nome do diretório de todas as apĺicações dentro de /modules.
     * Obs: mover este método para uma classe global de gerenciamento das aplicações. Sugestão: criar classe "Framework".
     * 
     * @return Array
     */
    static public function getAppNames()
    {
        $appNames = scandir(DIR_MODULES);

        // Retira . e .. da lista de arquivos.
        array_shift($appNames);
        array_shift($appNames);

        return !empty($appNames) ? $appNames : [];
    }

    /**
     * Retorna o nome do diretório da aplicação onde o arquivo config.php está localizado.
     * 
     * @return String
     */
    static public function getModuleDirName($filepath)
    {
        $dir = self::getModuleDir($filepath);
        $pos = strrpos($dir, DIRECTORY_SEPARATOR)+1;

        return substr($dir, $pos);
    }

    /**
     * Retorna o caminho do diretório da aplicação /var/www/modules/<app> (Ex: /var/www/modules/Geral, 
     * /var/www/modules/Template) onde o arquivo config.php está localizado.
     * 
     * @return String
     */
    static public function getModuleDir($filepath)
    {
        $pos = strpos($filepath, DIRECTORY_SEPARATOR, strlen(\realpath(DIR_MODULES))+1);
        $filepath = substr($filepath, 0, $pos);

        return $filepath;
    }
}
