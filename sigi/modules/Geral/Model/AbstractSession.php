<?php
/**
 * Esta classe gerencia todas as variáveis de Sessão.
 * Ela pode ser extendida para o gerenciamento de dados específicos da Sessão.
 * Exemplo: a classe XSession extende Session para o gerenciamento do parâmtro x ($_SESSION['x']) da sessão.
 */
namespace Geral\Model;

use Geral\Model\Exception\ErrorException;

abstract class AbstractSession
{
    /**
     * Atributo estático com os parâmetros que são gerenciados por uma Classe mais específica de Session.
     * Formato: [ < namespace da subclasse > => < parâmetro a ser gerenciado pela subclasse > ]
     * Exemplo: [ 'Geral\Model\UserSession' => 'usuario' ]
     * - A classe abstrata UserSession gerencia todo o conteúdo do parâmetro $_SESSION['usuario'].
     */
    private static $RESERVED_KEYS = [
        'Geral\Model\Session' => 'global',
        'Geral\Model\UserSession' => 'usuario'
    ];

    // Retorna o endereço da variável global $_SESSION.
    final public static function &getSession()
    {
        if(!isset($_SESSION[self::getSessionKey()])) {
            $_SESSION[self::getSessionKey()] = [];
        }
        
        return $_SESSION[self::getSessionKey()];
    }

    /**
     * Retorna o endereço de um parâmetro.
     * Este método retorna o endereço do parâmetro na memória, em vez de realizar uma nova cópia de seu conteúdo.
     * 
     * @param $param Strint Parâmetro a ser definido.
     * @return Mixed valor do parâmetro.
     */
    public static function &getParam($param)
    {
        if(!self::paramExists($param)) {
            throw new ErrorException('O parâmetro "'.$param.'" não existe na sessão.');
        }

        return self::getSession()[$param];
    }

    /**
     * Define um parâmetro com valor, desde que não seja um parâmetro reservado.
     * 
     * @param $param Strint Parâmetro a ser definido.
     * @param $value Mixed Conteúdo do parâmetro.
     */
    public static function setParam(String $param, $value)
    {
        $_SESSION[self::getSessionKey()][$param] = $value;
    }

    /**
     * Remove um parâmetro da sessão.
     * 
     * @param $param Strint Parâmetro a ser removido.
     */
    public static function removeParam($param)
    {
        unset($_SESSION[self::getSessionKey()][$param]);
    }

    final public static function paramExists($param)
    {
        return isset(self::getSession()[$param]);
    }

    // Retorna true se a sessão tiver dados ou false caso contrário.
    final public static function isEmpty()
    {
        return empty(self::getSession());
    }

    //=== Funções auxiliares ===================================================
    
    /**
     * Retorna o parâmetro gerenciado pela classe ou subclasse.
     */
    final protected static function getSessionKey()
    {
        /**
         * Se não existir uma chave reservada para a aplicação, então cria a chave com base no nome do respectivo
         * diretório em /modules.
         * Armazenar no atributo "self::$RESERVED_KEYS" evita a retribuição toda vez que invocar a session.
         */
        if(!isset(self::$RESERVED_KEYS[get_called_class()])) {
            self::$RESERVED_KEYS[get_called_class()] = $GLOBALS['url']->getNameModule();
        }

        return self::$RESERVED_KEYS[get_called_class()];
    }
}
