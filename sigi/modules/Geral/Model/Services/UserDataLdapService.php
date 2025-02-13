<?php
namespace Geral\Model\Services;

use \Geral\Model\Exception\ErrorException;
use \Geral\Model\Interfaces\UserDataServiceInterface;
use \Geral\Model\ConnLdap;
use Geral\Model\Interfaces\UserDataInterface;
use Geral\Model\UserDataServiceOptions;

class UserDataLdapService implements UserDataServiceInterface
{
    private const NOME_VINCULO_PROFESSOR = 'Professor';
    private const NOME_VINCULO_TECNICO = 'Tecnico';
    private const NOME_VINCULO_ALUNO = 'Aluno';

    // Mapeia os atributos presentes no LDAP para os esperados pelas camadas superiores (Proxy, UserDataFactory, etc)
    private const DADOS_USUARIO_MAP = [
        'cpf'                   => 'cpf',
        'displayname'           => 'nome',
        'employeenumber'        => 'matricula',
        'employeetype'          => UserDataInterface::NOME_CAMPO_VINCULO,
        'accountstatus'         => 'situacao',
        'ou'                    => 'unidade',
        'departmentnumber'      => 'setor',
        //'nomeLotacao'         => 'nomeSetor',
        //'siglaLotacao'        => 'siglaSetor',
        'title'                 => 'cargo',
        'funcao'                => 'funcao',
        'mail'                  => 'email',
        'mailalternateaddress'  => 'emailAlias',
        'mailforwardingaddress' => 'emailAlternativo',
        'telephonenumber'       => 'telefoneComercial',
        'mobile'                => 'telefoneCelular',
        'homephone'             => 'telefoneResidencial',
        'isPrincipal'           => 'isPrincipal',
        'st'                    => 'sexo',
        'datanascimento'        => 'dataNascimento',
        'jpegphoto'             => 'foto'
    ];

    const DADOS_USUARIO_FORMAT = [
        'datanascimento' => 'dataToSlashesSeparator'
    ];

    private $connLdap;
    private $userDataQueryStrategy;

    public function __construct()
    {
        $this->connLdap              = new ConnLdap;
        $this->userDataQueryStrategy = new LdapQueryStrategy();
    }

    // Implementações da interface =============================================

    public function getVinculosByCpf($cpf, UserDataServiceOptions $options=null)
    {
        $usuarioLdap = $this->connLdap->getUserLdap($cpf);

        if (empty($usuarioLdap)) {
            throw new \Exception('Usuário com cpf "' . $cpf . '" não encontrado no Ldap.');
        }

        $usuarioLdap = self::mapAttributes([$usuarioLdap], self::DADOS_USUARIO_MAP, $options);

        return $usuarioLdap;
    }

    public function executeQuery($filter, UserDataServiceOptions $options = null)
    {
        if(empty($filter)) {
            throw new ErrorException('Consulta sem filtro não permitida.');
        }

        $filter = self::mapFilterAttributes($filter);
        $query = $this->userDataQueryStrategy->toQuery($filter);
        $registros = $this->connLdap->execQueryString($query);

        return self::mapAttributes($registros, self::DADOS_USUARIO_MAP, $options);
    }

    // Funções auxiliares ======================================================

    static function mapAttributes($registros, $dadosUsuarioMap = self::DADOS_USUARIO_MAP, UserDataServiceOptions $options = null)
    {
        if(empty($registros)) return [];

        if(empty($options) || !$options->getCarregaFoto()){
            unset($dadosUsuarioMap['jpegphoto']);
        }

        $registrosMapeados = [];
        
        foreach($registros as $i => &$registro) {
            $registroMapeado = [];

            foreach($dadosUsuarioMap as $from => $to) {
                if($to == UserDataInterface::NOME_CAMPO_VINCULO) {
                    UserDataProxyService::normalizarVinculo($registro[$from]);
                }
                
                if(isset(self::DADOS_USUARIO_FORMAT[$from])) {
                    $func = self::DADOS_USUARIO_FORMAT[$from];
                    self::$func($registro[$from]);
                }

                $registroMapeado[$to] = $registro[$from];
            }

            unset($registros[$i]);

            $registroMapeado['foto'] = base64_encode($registroMapeado['foto']);
            $registroMapeado['isPrincipal'] = true;
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

    // Converte o formato do LDAP DDMMYYYY para DD/MM/YYYY.
    static public function dataToSlashesSeparator(String &$data = null)
    {
        // Caso esteja sendo utilizado '-', troca para '/'.
        if(strpos($data, '-') !== false) {
            $data = str_replace('-', '/', $data);
        } elseif(ctype_digit($data)) {
            $data = substr_replace($data, '/', 2, 0);
            $data = substr_replace($data, '/', 5, 0);
        }
    }
}

class LdapQueryStrategy
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
