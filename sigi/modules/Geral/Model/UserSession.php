<?php
/**
 * Esta classe gerencia todo o conteúdo do parâmetro 'usuario' da Sessão ($_SESSION['usuario']).
 * O nome do parâmetro está definido em Session::$RESERVED_KEYS.
 */

namespace Geral\Model;

abstract class UserSession extends AbstractSession
{
    // Comentado em AbstractSession
    final public static function &getParam($param)
    {
        return parent::getParam($param);
    }

    // Comentado em AbstractSession
    final public static function setParam(String $param, $value)
    {
        parent::setParam($param, $value);
    }

    // Comentado em AbstractSession
    final public static function removeParam($param)
    {
        parent::removeParam($param);
    }

    final public static function createFromData($data)
    {
        $data['id'] = $data['cpf'];
        $attr = ['id' => '', 'cpf' => '', 'nome' => '', 'vinculos' => '', 'vinculo' => '', 'defaultApp' => '', 'foto' => ''];
        $diff = array_diff_key($attr, $data);

        if(!empty($diff)) {
            throw new \Exception('Os campos "'.implode(',', $diff).'" ou mais dados exigidos não estão presentes para iniciar a sessão.');
        }

        $data = array_intersect_key($data, $attr);

        UserSession::setParam('id', $data['id']);
        UserSession::setParam('cpf', $data['cpf']);
        UserSession::setParam('nome', $data['nome']);
        UserSession::setParam('foto', $data['foto']);
        UserSession::setParam('vinculos', $data['vinculos']);
        UserSession::setParam('vinculo', $data['vinculo']);
        UserSession::setParam('multivinculos', (count($data['vinculos']) > 1));
        UserSession::setParam('logado', true);
        UserSession::setParam('defaultApp', $data['defaultApp']);
    }
}