<?php
namespace Geral\Model\Services;

use \Geral\Model\Exception\ErrorException;
use \Geral\Model\Interfaces\UserDataServiceInterface;
use \Geral\Model\Interfaces\UserDataInterface;
use \Geral\Model\Services\UserDataLdapService;
use \Geral\Model\Interfaces\UserDataQueryInterface;
use \Geral\Model\Session;
use \Geral\Model\Factory\UserDataCacheFactory;
use Geral\Model\UserDataServiceOptions;

class UserDataProxyService implements UserDataServiceInterface
{
    const VINCULOS_MAP = [
        'PROFESSOR'     => UserDataInterface::NOME_VINCULO_PROFESSOR,
        'Professor'     => UserDataInterface::NOME_VINCULO_PROFESSOR,
        'TECNICO'       => UserDataInterface::NOME_VINCULO_TECNICO,
        'Tecnico'       => UserDataInterface::NOME_VINCULO_TECNICO,
        'ALUNO'         => UserDataInterface::NOME_VINCULO_ALUNO,
        'Aluno'         => UserDataInterface::NOME_VINCULO_ALUNO,
        'Extensao'      => UserDataInterface::NOME_VINCULO_EXTENSAO,
        'EXTENSAO'      => UserDataInterface::NOME_VINCULO_EXTENSAO,
        'Estagiario'    => UserDataInterface::NOME_VINCULO_ESTAGIARIO,
        'ESTAGIARIO'    => UserDataInterface::NOME_VINCULO_ESTAGIARIO,
        'Terceirizado'  => UserDataInterface::NOME_VINCULO_TERCEIRIZADO,
        'TERCEIRIZADO'  => UserDataInterface::NOME_VINCULO_TERCEIRIZADO,
        'FUNCAO CHEFIA' => UserDataInterface::NOME_VINCULO_FC,
        'Afastado'      => UserDataInterface::NOME_VINCULO_AFASTADO,
        'AFASTADO'      => UserDataInterface::NOME_VINCULO_AFASTADO,
        'Aposentado'    => UserDataInterface::NOME_VINCULO_APOSENTADO,
        'APOSENTADO'    => UserDataInterface::NOME_VINCULO_APOSENTADO,
        'Exonerado'     => UserDataInterface::NOME_VINCULO_EXONERADO,
        'EXONERADO'     => UserDataInterface::NOME_VINCULO_EXONERADO
    ];

    private $isUserDataCacheable;
    private $userDataService;

    public function __construct()
    {
        /**
         * O recurso de simulação de dados de usuário somente pode ser utilizado no ambiente de desenvolvimento.
         */
        if(($_SERVER['AMBIENTE'] == 'DEV' && (Session::paramExists('simularDados') && Session::getParam('simularDados')))) {
            $this->isUserDataCacheable = false;
            $this->setUserDataService(new UserDataSimulatorService());
        } else {
            // Nega acesso aos dados caso a requisição pertença a um controller Público no ambiente de Desenvolvimento.
            if(Session::paramExists('simularDadosRequerido')) {
                Session::removeParam('simularDadosRequerido');
                
                throw new ErrorException('Acesso a dados de usuário negado. Este acesso não é permitido no ambiente de 
                desenvolvimento utilizando o TIPO_LOGIN="True" ou em controllers públicos.');
            }

            $this->isUserDataCacheable = true;
            $this->setUserDataService(new UserDataLdapService());
        }
    }

    private function setUserDataService(UserDataServiceInterface $userDataService)
    {
        $this->userDataService  = $userDataService;
    }

    // Implementações da interface =============================================

    public function getVinculosByCpf($cpf, UserDataServiceOptions $options=null)
    {
        return $this->userDataService->getVinculosByCpf($cpf, $options);
    }

    public function executeQuery(UserDataQueryInterface $userDataQuery)
    {
        if($this->isUserDataCacheable) {
            $filter = $userDataQuery->getFilter();
            $options = $userDataQuery->getOptions();

            // Pegar do cache somente se consultado apenas por cpf e única entrada.
            if(isset($filter['cpf']) && count($filter['cpf']) == 1 && count($filter) == 1) {
                $vinculos = UserDataCacheFactory::getByCpf(current($filter['cpf']), true);

                if(empty($vinculos)) {
                    $vinculos = $this->userDataService->executeQuery($userDataQuery->getFilter(), $userDataQuery->getOptions());

                    foreach($vinculos as &$vinculo) {
                        UserDataCacheFactory::add($vinculo);
                    }
                }

                return $vinculos;
            }
        }

        // Cache desativado então repassa consulta
        return $this->userDataService->executeQuery($userDataQuery->getFilter(), $userDataQuery->getOptions());
    }

    // Normaliza os valores do campo vinculo
    public static function normalizarVinculo(&$nomeVinculo)
    {
        if(!isset(self::VINCULOS_MAP[$nomeVinculo])) {
            return $nomeVinculo;

            //throw new ErrorException('Valor "'.$nomeVinculo.'" do vínculo não está entre os valores esperados "'
            //    .implode('", "', array_keys(self::VINCULOS_MAP)).'".');
        }

        $nomeVinculo = self::VINCULOS_MAP[$nomeVinculo];
    }
}
