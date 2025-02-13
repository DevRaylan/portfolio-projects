<?php
namespace Sigcinf\Controller;

use \Geral\Model\Factory\UserDataFactory;
use \Geral\Model\UserSession;
use Sigcinf\Model\Factory\UnidadesFactory;
use \Sigcinf\Model\Factory\UsuarioFactory;

class IndexController extends \Geral\Controller\AbstractPrivateController
{

    // AbstractPPrivateController

    function __construct( )
    {
        parent::__construct( );
    }

    // =================================================================================================================

    public function index( )
    {

//        $this->AddCss('style.css');
        $this->view();

        try {
            $cpf = UserSession::getParam('cpf');
            $oRecordSet = UsuarioFactory::getByCpf3($cpf);
            $cpfusuarioSistema = 0;
            foreach($oRecordSet as $usuarioSistema){
                $cpfusuarioSistema = $usuarioSistema->getCpf();
            }
            if  ($cpfusuarioSistema == 0) {
                $oUsuarioSession = UserDataFactory::getByCpf($cpf);
                $oCentroUdesc = UnidadesFactory::getByAbrev3($oUsuarioSession->getUnidade());
                $idCentro = 0;
                foreach($oCentroUdesc as $centroUdesc){
                    $idCentro = $centroUdesc->getId();
                }
                if  ($idCentro == 0) {
                    UnidadesFactory::adicionar($oUsuarioSession->getUnidade(), $oUsuarioSession->getUnidade());
                    $oCentroUdesc = UnidadesFactory::getByAbrev3($oUsuarioSession->getUnidade());
                    foreach($oCentroUdesc as $centroUdesc){
                        $idCentro = $centroUdesc->getId();
                    }
                };
                $dados['centro'] = $idCentro;
                $dados['nome'] = $oUsuarioSession->getNome();
                $dados['cpf'] = $cpf;
                $dados['email'] = $oUsuarioSession->getEmail();
                UsuarioFactory::adicionar($dados);
            };
        } catch (\Exception $e) {
            $this->setError('Erro ao adicionar usuário.');
            $this->sendResponse();
        };

    }

}