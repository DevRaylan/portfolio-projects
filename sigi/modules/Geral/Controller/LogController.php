<?php
namespace Geral\Controller;
/*
 * LogController.php
 * Oferece webservice para:
 * - gravação de eventos no banco de dados de Auditoria;
 */

class LogController extends AbstractPrivateController
{
    private $logFactory;    // Factory para o Log.

    function __construct( )
    {
        parent::__construct( );

        $this -> logFactory = new \Geral\Model\Factory\LogFactory;
    }

    public function index()
    {
        $this -> view();
    }

    public function addLog( )
    {
/*      Grava um Log no banco
 *
 *      Recebe via POST:
 *      - usuario - Identificador do usuário que realizou a ação. Exemplo: 45678901234 (CPF).
 *      - sistema - Sistema onde a ação foi realizada. Exemplo: ID-UDESC.
 *      - modulo - Modulo do sistema onde a ação foi realizada. Exemplo: CONSULTA.
 *      - tipoObjeto - Tipo de objeto que sofreu a ação. Exemplo: USUARIO.
 *      - idObjeto - Identificador do objeto que sofreu a ação. Exemplo: 12345678901 (CPF).
 *      - url - URL onde a ação foi realizada. Exemplo: "http://sistemaX.dev.udesc.br/Usuario/listar".
 *      - ip - Endereço IP da estação de onde a ação que foi realizada. Exemplo: 200.20.1.5.
 *      - descricaoAcao - Descrição da ação que foi realizada. Exemplo: "Removido o usuário da base".
 *      - infoAdicional - Informações adicionais sobre o evento.
 *
 *      Retorno (exemplos):
 *      {"result":"success","message":"Log gravado com sucesso"}
 *      {"result":"error","message":"Erro na gravação do Log"}
 */
        $parms['usuario'] = $_POST['usuario'];
        $parms['sistema'] = $_POST['sistema'];
        $parms['modulo'] = $_POST['modulo'];
        $parms['tipoObjeto'] = $_POST['tipoObjeto'];
        $parms['idObjeto'] = $_POST['idObjeto'];
        $parms['descricaoAcao'] = $_POST['descricaoAcao'];
        $parms['infoAdicional'] = $_POST['infoAdicional'];
        $parms['ip'] = $_POST['ip'];
        $parms['url'] = $this -> url -> getUrlAction();

        $log = $this -> logFactory -> create ($parms);

        $resultado = [];

        if ($this -> logFactory -> add ($log)) {
            $resultado = [
                'result' => 'success',
                'message' => 'Log gravado com sucesso.'
            ];
        }
        else {
            $resultado = [
                'result' => 'error',
                'message' => 'Erro na gravação do Log.'
            ];
        }
        $this -> returnJson($resultado);
    }
}
