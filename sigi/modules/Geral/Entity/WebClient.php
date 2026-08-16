<?php
namespace Geral\Entity;
/**
 * @class WebClient
 *
 *
 * @version 1.0
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 */

 /**
 * @Entity
 * @Table(name="webclient")
 **/

class WebClient
{
    const DATAHORA_FORMAT = 'd/m/Y H:i:s';
    const DATAHORASEMSEGUNDOS_FORMAT = 'd/m/Y H:i';
    const DATA_FORMAT = 'd/m/Y';

    /** @Id @Column(name="token", type="string", length=32, nullable=false) **/
    private $token;

    /** @Column(name="key_secret", type="string", length=40, nullable=false) **/
    private $keySecret;

    /** @Column(type="string", length=255, nullable=false) **/
    private $descricao;

    /** @Column(name="dt_cadastro", type="datetime", nullable=false) **/
    private $dtCadastro;

    /** @Column(name="dt_ultimo_acesso", type="datetime", nullable=true) **/
    private $dtUltimoAcesso;
    
    /** @Column(name="ips_permitidos", type="string", length=150, nullable=true) **/
    private $ipsPermitidos; // Até 10 ips
    
    /** @Column(name="emails", type="string", length=150, nullable=true) **/
    private $emails;
    
    

// ====================================================================================================================

    public function __construct( ) {
        $this -> dtCadastro = new \DateTime();
    }

    /**
     * @param  $token
     */
    public function setToken($token) {
        $this->token = $token;
        $this -> setKeySecret();
    }
    /**
     * @return
     */
    public function getToken() {
        return $this -> token;
    }
    /**
     * @param  $keySecret
     */
    private function setKeySecret() {
        $token2 = str_shuffle(str_shuffle($this -> token) . microtime());
        $keySecret = sha1($token2);
        $this -> keySecret = $keySecret;
    }

    public function getKeySecret() {
        return $this->keySecret;
    }

    /**
     * @param  $descricao
     */
    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }
    /**
     * @return
     */
    public function getDescricao() {
        return $this->descricao;
    }
    /**
     * @param  $dtCadastro
     */
    public function setDtCadastro($dtCadastro) {
        $this->dtCadastro = $dtCadastro;
    }
    /**
     * @return
     */
    public function getDtCadastro($string = false) {
        return $string ? self::convDatetimeToString($this->dtCadastro, self::DATAHORASEMSEGUNDOS_FORMAT) : $this->dtCadastro;
    }
    /**
     * @param  $dtUltimoAcesso
     */
    public function setDtUltimoAcesso($dtUltimoAcesso) {
        $this->dtUltimoAcesso = $dtUltimoAcesso;
    }
    /**
     * @return
     */
    public function getDtUltimoAcesso() {
        return $this->dtUltimoAcesso;
    }

    public function keySecretIsValid($keySecret)
    {
        return !empty($keySecret) && $this -> keySecret == $keySecret;
    }
    
    /**
    * Verificar se o ip do sistema está autorizado para utilizar o SGBR.
    * Caso o sistema não tenha ips cadastrados no SGBR, não será feito esta restrição.
    * Foi implementado assim para casos em que o ip do sistema possa mudar (Ex: redudância, ip dinâmico, etc).
    */
    public function isAllowedIp($ip)
    {
        return empty($this -> getIps()) || in_array($ip, $this -> getIps());
    }
    
    public function setIps(Array $ips) {
        $this -> ipsPermitidos = !empty($ips) ? implode(',',$ips) : null;
    }
    
    public function getIps($toString = false) {
        if(empty($this -> ipsPermitidos)) return null;
        
        return $toString ? $this -> ipsPermitidos : explode(',', $this -> ipsPermitidos);
    }
    
    public function setEmails(Array $emails) {
        $this -> emails = !empty($emails) ? implode(',', $emails) : null;
    }
    
    public function getEmails($toString = false) {
        return $toString ? $this -> emails : explode(',', $this -> emails);
    }

// CRUD ================================================================================================================

    public static function add($descricao, Array $ips, Array $emails)
    {
        $webClient = new WebClient;
        $webClient -> setDescricao($descricao);
        $webClient -> setIps($ips);
        $webClient -> setEmails($emails);
        $token = md5($descricao . microtime());
        $webClient -> setToken($token);

        try {
            $GLOBALS['em'] -> persist($webClient);
            $GLOBALS['em'] -> flush();
        } catch(Exception $e) {
            throw new Exception("Erro ao gravar dados no BD: ".$e -> getMessage());
        }

        return $webClient;
    }

    public static function getByToken($token, $toArray = false)
    {
        if(empty($token)) return null;

        $webClient = $GLOBALS['em'] -> find('\Geral\Entity\WebClient', $token);

        return $toArray && $webClient ? $webClient -> toArray() : $webClient;
    }

    public static function getAll($toArray = false)
    {
        $webClients = $GLOBALS['em'] -> getRepository('\Geral\Entity\WebClient') -> findAll();

        if($toArray) {
            $webClientsArray = [];

            foreach($webClients as $webClient) {
                $webClientsArray[] = $webClient -> toArray();
            }

            $webClients = &$webClientsArray;
        }

        return $webClients;
    }


// =============================================================================

    static function convDatetimeToString($data, $formato = self::DATAHORA_FORMAT)
    {
        return is_object($data) ? $data -> format($formato) : '';
    }

    public function toArray() {
        return [
            'token' => $this -> getToken(),
            'keySecret' => $this -> getKeySecret(),
            'descricao' => $this -> getDescricao(),
            'ips' => $this -> getIps(true),
            'emails' => $this -> getEmails(true),
            'dtCadastro' => $this -> getDtCadastro(true),
            'dtUltimoAcesso' => $this -> getDtUltimoAcesso(true)
        ];
    }

}
