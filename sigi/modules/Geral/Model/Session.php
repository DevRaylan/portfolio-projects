<?php
/**
 * Esta classe gerencia todas as variáveis de Sessão.
 * Ela pode ser extendida para o gerenciamento de dados específicos da Sessão.
 * Exemplo: a classe XSession extende Session para o gerenciamento do parâmtro x ($_SESSION['x']) da sessão.
 */
namespace Geral\Model;

abstract class Session extends \Geral\Model\AbstractSession
{
}
