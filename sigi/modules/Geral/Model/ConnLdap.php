<?php
namespace Geral\Model;

use Geral\Model\Exception\ErrorException;

class ConnLdap
{

    private $cnx;

    // Registros vindo do LDAP
    public function __construct()
    {
        $this->iniCon();
    }

    public function iniCon()
    {
        // iniciar conexão com o LDAP
        $this->cnx = ldap_connect(LDAP_HOST) or die("Erro ao conectar ao servidor LDAP.");
        ldap_set_option($this->cnx, LDAP_OPT_PROTOCOL_VERSION, 3);
        $isCnxOk = @ldap_bind($this->cnx);

        if (! $isCnxOk) {
            throw new ErrorException('Não foi possível conectar com o servidor LDAP utilizando conta anônima.');
        }
    }

    public function getConnexao()
    {
        return $this->cnx;
    }

    public function getUserLdap($cpf)
    {
        $attributes = $this->_getAttributesUser();
        $userLdap = $this->_getUserById($cpf, $attributes);

        return $userLdap;
    }

    public function getUserNormalized($cpf)
    {
        $userLdap = $this->_getUserById($cpf);
        
        if (! isset($userLdap)) {
            throw new ErrorException("Usuário não encontrado.");
        }

        $lastPasswdChange = $userLdap["phpgwlastpasswdchange"];

        $userNormalized = [
            "primeiroNome" => $userLdap["givenname"],
            "nome" => $userLdap["displayname"],
            "cpf" => $userLdap["cpf"],
            "tipo" => $userLdap["employeetype"],
            "situacao" => $userLdap["accountstatus"],
            "lotacao" => $userLdap["ou"],
            "centro" => $userLdap["ou"],
            "setor" => $userLdap["departmentnumber"],
            "vinculo" => $userLdap["title"],
            "funcao" => $userLdap["funcao"],
            "escolaridade" => str_replace('escolaridade-', '', $userLdap["description"]),
            "email" => $userLdap["mail"],
            "emailAlias" => $userLdap["mailalternateaddress"],
            "emailRecuperaSenha" => $userLdap["mailforwardingaddress"],
            "telefoneComercial" => $userLdap["telephonenumber"],
            "celular" => $userLdap["mobile"],
            "telefoneResidencial" => $userLdap["homephone"],
            "senha" => $userLdap["sambantpassword"],
            "ultimaModificacao" => date('m/d/Y H:i:s', $lastPasswdChange),
            "foto" => base64_encode($userLdap["jpegphoto"])
        ];

        return $userNormalized;
    }

    /**
     * Método que retorna todos os usuários registrados no LDAP com tipo diferente de aluno
     * Filtro = employeetype=Professor E employeetype=Tecnico
     */
    public function getUsersNotStudent()
    {
        $usersLdap = $this->_getUsersNotStudent();

        if (empty($usersLdap)) {
            throw new ErrorException("Usuários diferente de aluno não localizado.");
        }

        return $usersLdap;
    }

    public function autenticar($id, $password)
    {
        try {
            $usuario = $this->getUserLdap($id);
            if ($usuario) {
                // Autenticar usuário e retorno true ou false
                return true;
//                return @ldap_bind($this->cnx, $usuario['dn'], $password);
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
    

    /**
     * Exemplos Where
     * - AND no LDAP: &(cpf=55566699900)(employeetype=Tecnico)
     * - OR no LDAP: |(cpf=55566699900)(employeetype=Tecnico)
     */
    private function _getUserById($cpf, $attributesFilter = [])
    {
        // Localiza registro
        $filter = '(&(cpf='. $cpf . ')(accountstatus=active)(objectclass=person))';
        $localized = ldap_search($this->cnx, LDAP_DOMINIO, $filter);

        // localize atributos no LDAP
        $userAttributes = ldap_get_entries($this->cnx, $localized);
        $userLdap = $userAttributes[0];

        if (! isset($userLdap)) {
            throw new ErrorException('Usuário não localizado: '.$cpf);
        }

        $userLdap = $this->_converterToMap([$userLdap], $attributesFilter);
        
        return $userLdap[0];
    }

    private function _getUsersNotStudent()
    {
        // Localiza registro '|(employeetype=Professor)(employeetype=Tecnico)'
        $localized = ldap_search($this->cnx, LDAP_DOMINIO, '(|(employeetype=Professor)(employeetype=Tecnico))');

        // localize atributos no LDAP
        $userLdap = ldap_get_entries($this->cnx, $localized);
        // $usersNotStudant = $userAttributes[0];

        if (empty($userLdap)) {
            throw new ErrorException('Usuários não localizados.');
        }

        $userLdap = $this->_converterToMap($userLdap);
        
        return $userLdap;
    }

    private function _converterToMap($usersLdap, $attributesFilter = [])
    {
        if (empty($attributesFilter)) {
            $chaves = array_keys($usersLdap[0]);
            $attributesFilter = array_filter($chaves, function ($k){ return is_string($k); }); 
        }
        
        foreach ($usersLdap as $user) {

            foreach ($attributesFilter as $attr) {
                $valores[$attr] = (is_array($user[$attr]) ? $user[$attr][0] : $user[$attr]);
            }

            if (! empty($valores['cpf'])) {
                $usuariosFiltered[] = $valores;
            }
        }

        return $usuariosFiltered;
        
    }

    
    private function _getAttributesUser()
    {
        $attributes = [
            'uidnumber',
            'dn',
            'cn',
            'displayname',
            'cpf',
            'employeetype',
            'employeenumber',
            'accountstatus',
            'ou',
            'departmentnumber',
            'title',
            'funcao',
            'description',
            'mail',
            'mailalternateaddress',
            'mailforwardingaddress',
            'telephonenumber',
            'mobile',
            'homephone',
            'sambantpassword',
            'phpgwlastpasswdchange',
            'jpegphoto',
            'st',
            'datanascimento'
        ];

        return $attributes;
    }

    public function execQueryString($queryString)
    {
        $queryString = '&(objectclass=person)('.$queryString.')(accountstatus=active)';
        // Localiza registro '|(employeetype=Professor)(employeetype=Tecnico)'
        $search = ldap_search($this->cnx, LDAP_DOMINIO, '('.$queryString.')');

        // localize atributos no LDAP
        $entries = ldap_get_entries($this->cnx, $search);

        if (empty($entries) || $entries['count'] === 0) {
            return [];
        }
        
        return $this->_converterToMap($entries, $this->_getAttributesUser());
    }
}
