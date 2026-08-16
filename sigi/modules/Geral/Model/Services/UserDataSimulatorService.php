<?php
namespace Geral\Model\Services;

use \Geral\Model\Exception\ErrorException;
use Geral\Model\Factory\UserDataSimulatorFactory;
use \Geral\Model\Interfaces\UserDataInterface;
use \Geral\Model\Interfaces\UserDataServiceInterface;
use Geral\Model\UserDataServiceOptions;

class UserDataSimulatorService implements UserDataServiceInterface
{
    const QTDMAX_CONTAS_FAKES = 2;

    public const DADOS_USUARIO_MAP = [
        'cpf'                 => 'cpf',
        'nome'                => 'nome',
        'matricula'           => 'matricula',
        'vinculo'             => UserDataInterface::NOME_CAMPO_VINCULO,
        'situacao'            => 'situacao',
        'unidade'             => 'unidade',
        'setor'               => 'setor',
        //'nomeLotacao'         => 'nomeSetor',
        //'siglaLotacao'        => 'siglaSetor',
        'cargo'               => 'cargo',
        'funcao'              => 'funcao',
        'email'               => 'email',
        'emailAlias'          => 'emailAlias',
        'emailAlternativo'    => 'emailAlternativo',
        'telefoneComercial'   => 'telefoneComercial',
        'telefoneCelular'     => 'telefoneCelular',
        'telefoneResidencial' => 'telefoneResidencial',
        'isPrincipal'         => 'isPrincipal',
        'sexo'                => 'sexo',
        'dataNascimento'      => 'dataNascimento',
        'foto'                => 'foto'
    ];

    public function __construct()
    {
        if($_SERVER['AMBIENTE'] != 'DEV') {
            throw new ErrorException('O recurso de simulação de dados está disponível apenas no ambiente de
            desenvolvimento');
        }
    }

    // Implementações da interface =============================================

    public function getAll()
    {
        return self::mapAttributes(UserDataSimulatorFactory::getAll(true));
    }

    public function getVinculosByCpf($cpf, UserDataServiceOptions $options=null)
    {
        return self::mapAttributes(UserDataSimulatorFactory::getByCpf($cpf, true), self::DADOS_USUARIO_MAP, $options);
    }

    public function executeQuery($filter, UserDataServiceOptions $options = null)
    {
        $filter = self::mapFilterAttributes($filter);
        $registros = UserDataSimulatorFactory::getByFilter($filter, true);
        
        return self::mapAttributes($registros, self::DADOS_USUARIO_MAP, $options);
    }

    //=== Funções auxiliares ===================================================

    public static function addDadosSimulados($cpf)
    {
        $jaExiste = true;

        try {
            UserDataSimulatorFactory::getByCpf($cpf);
        } catch (\Exception $e) {
            $jaExiste = false;
        }

        if(UserDataSimulatorFactory::getQtdFakes() >= self::QTDMAX_CONTAS_FAKES) {
            //throw new ErrorException('Está sendo utilizado o recurso de simulação de dados e o limite de '.self::QTDMAX_CONTAS_FAKES.' contas simuladas foi atingido.');
        }

        if(!$jaExiste) {
            $dados = [
                'cpf'                 => $cpf,
                'nome'                => 'Usuário padrão',
                'matricula'           => '123456789',
                'sexo'                => 'M',
                'dataNascimento'      => '10/12/1980',
                'vinculo'             => 'TECNICO',
                'situacao'            => 'ativo',
                'unidade'             => 'REITORIA',
                'setor'               => 'SEPC',
                'cargo'               => 'TECNICO UNIVERSITARIO DE DESENVOLVIMENTO',
                'funcao'              => 'ANALISTA DE SISTEMAS',
                'email'               => 'emailfake@udesc.br',
                'emailAlias'          => 'emailfake@udesc.br',
                'emailAlternativo'    => 'emailfake@mail.br',
                'telefoneComercial'   => '(00) 0000-0000',
                'telefoneCelular'     => '(11) 11111-1111',
                'telefoneResidencial' => '(22) 22222-2222',
                'foto'                => '',
                'isPrincipal'         => true,
                'createdOnLogin'      => true
            ];

            UserDataSimulatorFactory::add($dados);
        }
    }

    private static function mapAttributes($registros, $dadosMap=self::DADOS_USUARIO_MAP, UserDataServiceOptions $options = null)
    {
        if(empty($registros)) return [];

        if($options && $options->getCarregaFoto()){
            $dadosMap['foto'] = 'foto';
        }

        $registrosMapeados = [];
        
        foreach($registros as $i => &$registro) {
            $registroMapeado = [];

            foreach($dadosMap as $from => $to) {
                if($to == UserDataInterface::NOME_CAMPO_VINCULO) {
                    UserDataProxyService::normalizarVinculo($registro[$from]);
                }

                $registroMapeado[$to] = $registro[$from];
            }

            unset($registros[$i]);
            $registrosMapeados[] = $registroMapeado;
        }

        return $registrosMapeados;
    }

    static function mapFilterAttributes(&$filter)
    {
        $newFilter = [];

        foreach($filter as $atributo => &$dados) {
            $from = array_search($atributo, self::DADOS_USUARIO_MAP);
            $newFilter[$from] = $dados;
        }

        return $newFilter;
    }
}

class UserDataSimulateQueryStrategy
{
    public function toQuery(Array $filters)
    {
        $filtro = [];
        $range = '';

        foreach ($filters as $atributo => &$valores) {
            foreach($valores as &$valor) {
                $valor = '(' . $atributo . '=' . $range . $valor . $range . ')';
            }

            $filtro[] = '(|'.implode('', $valores).')';
            unset($valor);
        }

        $filtro = '&' . implode('', $filtro) . '';

        return $filtro;
    }
}