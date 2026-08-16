<?php

namespace Geral\Model\Factory;

use ErrorException;

/**
 * Factory responsavel por verificar restrição de comandos SQL autorizados e executar os mesmo na base
 */
abstract class DbAdminFactory
{

    public function __construct()
    {
    }

    static public function executarArquivoSQL($fileName)
    {
        $executadoSucesso = false;

        if (empty($fileName) == false) {
            // processa os comandos a partir do arquivo:
            $comandosSql = self::lerConteudoArquivo($fileName);
            $executadoSucesso =  self::executarComandosSQL($comandosSql);
        } else {
            throw new ErrorException('Nome do arquivo invalido.');
        }
        return $executadoSucesso;
    }

    static public function executarTextoSQL($comandosSql)
    {
        $executadoSucesso = false;
        if (empty($comandosSql) == false) {
            $executadoSucesso = self::executarComandosSQL($comandosSql);
        } else {
            throw new ErrorException('String de comandos vazios.');
        }
        return $executadoSucesso;
    }

    // -------

    static private function executarComandosSQL($comandosSql)
    {
        $comandosAprovados = self::verificarRestricoes($comandosSql);
        
        // Valida se os comandos passaram no filtro
        if ($comandosAprovados == false) {
            throw new ErrorException('Apenas o comando INSERT é permitido, verifique o SQL.');
        }

        $comandos = self::separarComandos($comandosSql);
        
        return self::executarSql($comandos);
    }

    static private function lerConteudoArquivo($fileName)
    {
        return file_get_contents($fileName);
    }

    static private function separarComandos($conteudo)
    {
        return explode(";", $conteudo);
    }

    static private function verificarRestricoes($comandos)
    {
        $comandosValidos = true;

        $match = preg_match("/(DELETE )|(UPDATE )|(CREATE )|(ALTER )|(DROP )|(GRANT )|(REVOKE )|(DENY )/i", $comandos);

        if ($match) {
            $comandosValidos = false;
        }

        return $comandosValidos;
    }

    static private function executarSql($comandos)
    {
        $db = $GLOBALS['em']->getConnection()->getWrappedConnection();

        try {
            $registros = [];

            foreach ($comandos as $comando) {
                $querySet = $db->prepare($comando);
                $querySet->execute();
                
                if(strpos($comando, 'SELECT ') === 0) {
                    $registros = array_merge($registros, $querySet->fetchAll(\PDO::FETCH_ASSOC));
                } else {
                    $registros = [];
                }
                
                return $registros;
            }
        } catch (\Exception $e) {
            throw new ErrorException('Erro ao executar os comandos no BD: '.$e->getMessage());
        }
    }
}
