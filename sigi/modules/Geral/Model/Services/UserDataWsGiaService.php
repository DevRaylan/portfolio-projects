<?php
namespace Geral\Model\Services;

use \Geral\Model\Config;
use Geral\Model\ConnLdap;
use \Geral\Model\Exception\ErrorException;
use Geral\Model\Interfaces\UserDataInterface;
use \Geral\Model\Interfaces\UserDataServiceInterface;
use \Geral\Model\Services\RestClient;
use Geral\Model\UserDataServiceOptions;

class UserDataWsGiaService implements UserDataServiceInterface
{
    public const DADOS_USUARIO_MAP = [
        'uid'                 => 'id',
        'uid'                 => 'cpf',
        'nomePessoa'          => 'nome',
        'matricula'           => 'matricula',
        'tipoVinculo'         => UserDataInterface::NOME_CAMPO_VINCULO,
        'status'              => 'situacao',
        'unidade'             => 'unidade',
        'lotacao'             => 'setor',
        //'nomeLotacao'         => 'nomeSetor',
        //'siglaLotacao'        => 'siglaSetor',
        'cargo'               => 'cargo',
        'funcao'              => 'funcao',
        'mail'                => 'email',
        'mailAlias'           => 'emailAlias',
        'mailAlternativo'     => 'emailAlternativo',
        'telefone'            => 'telefoneComercial',
        'telefoneCelular'     => 'telefoneCelular',
        'telefoneCasa'        => 'telefoneResidencial',
        'isPrincipal'         => 'isPrincipal',
        'genero'              => 'sexo',
        'datanascimento'      => 'dataNascimento',
        'foto'                => 'foto'
    ];

    const DADOS_USUARIO_FORMAT = [
        'datanascimento' => 'dataToSlashesSeparator'
    ];

    private $endpointUrl;
    private $token;
    private $key;

    public function __construct()
    {
        $this->userDataQueryStrategy = new WsGiaQueryStrategy();

        $auth = Config::getValue('authWS', 'Geral');
        $this->endpointUrl = $auth['url'].$auth['endpointObtemVinculos'];
        $this->restClient = new RestClient();
        $this->token = $auth['tokenSystem'];
        $this->key = $auth['tokenSecret'];
    }

    // Implementações da interface =============================================

    public function getVinculosByCpf($cpf, UserDataServiceOptions $options=null)
    {
        $vinculos = $this->sendPost($this->endpointUrl, ['cpf' => $cpf]);
        $vinculos = $vinculos['valores'];

        return self::mapAttributes($vinculos, self::DADOS_USUARIO_MAP, $options);
    }

    public function executeQuery($filter, UserDataServiceOptions $options = null)
    {
        $filter = self::mapFilterAttributes($filter);
        $query = $this->userDataQueryStrategy->toQuery($filter);
        $registros = $this->sendPost($this->endpointUrl, $query);
        $registros = $registros['valores'];

        return self::mapAttributes($registros, self::DADOS_USUARIO_MAP, $options);
    }

    // Comunicação com Web Service =============================================

    private function getByVinculo(String $vinculo)
    {   
        $registros = $this->sendPost($this->endpointUrl, ['vinculo' => $vinculo]);
           
        return self::mapAttributes($registros);
    }

    // Funções auxiliares ======================================================

    private static function mapAttributes($registros, $dadosUsuarioMap = self::DADOS_USUARIO_MAP, UserDataServiceOptions $options = null)
    {
        if(empty($registros)) return [];

        if(empty($options) || !$options->getCarregaFoto()) {
            unset($dadosUsuarioMap['foto']);
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

            $registrosMapeados[] = $registroMapeado;
        }

        return $registrosMapeados;
    }

    static function mapFilterAttributes(&$filter)
    {
        return $filter;
    }

    // Converte o formato do GIA AAAAMMDD para DD/MM/YYYY.
    static public function dataToSlashesSeparator(String &$data = null)
    {
        // Caso esteja sendo utilizado '-', troca para '/'.
        if(strpos($data, '-') !== false) {
            $data = str_replace('-', '/', $data);
        } elseif(ctype_digit($data)) {
            $data = substr_replace($data, '/', 4, 0);
            $data = substr_replace($data, '/', 7, 0);
        }

        if(!empty($data)) {
            $data = implode('/', array_reverse(explode('/', $data)));
        }
    }

    private function sendPost($url, $query)
    {
        return $this->restClient->sendJsonPost($this->token, $this->key, $url, $query);
    }
}

class WsGiaQueryStrategy
{
    public function toQuery(Array $filters)
    {
        $filtro = [];

        foreach($filters as $atributo => $valores) {
            if(count($valores) > 2) {
                throw new ErrorException('A implementação do Ws Gia não permite
                múltiplos valores para um campo.');
            }

            if($atributo == 'uid') {
                $atributo = 'cpf';
            }

            $filtro[$atributo] = $valores;
        }

        return $filtro;
    }
}