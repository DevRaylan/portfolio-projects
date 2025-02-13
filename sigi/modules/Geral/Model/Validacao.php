<?php
namespace Geral\Model;

abstract class Validacao
{
    static $UFsToCEP = [
        'AC' => [[69900, 69999]], 
        'AL' => [[57000, 57999]],
        'AM' => [[69000, 69299], [69400, 69899]],
        'AP' => [[68900, 68999]],
        'BA' => [[40000, 48999]],
        'CE' => [[60000, 63999]],
        'DF' => [[70000, 72799], [73000, 73699]],
        'ES' => [[29000, 29999]],
        'GO' => [[72800, 72999], [73700, 76799]],
        'MA' => [[65000, 65999]],
        'MG' => [[30000, 39999]],
        'MS' => [[79000, 79999]],
        'MT' => [[78000, 78899]],
        'PA' => [[66000, 68899]],
        'PB' => [[58000, 58999]],
        'PE' => [[50000, 56999]],
        'PI' => [[64000, 64999]],
        'PR' => [[80000, 87999]],
        'RJ' => [[20000, 28999]],
        'RN' => [[59000, 59999]],
        'RO' => [[76800, 76999]],
        'RR' => [[69300, 69399]],
        'RS' => [[90000, 99999]],
        'SC' => [[88000, 89999]],
        'SE' => [[49000, 49999]],
        'SP' => [[1000, 19999]],
        'TO' => [[77000, 77999]]
    ];
    
    public function __construct( )
    {
    }

    static public function cpfIsValid( $cpf )
    {
        // Verificar se é somente números
        if (!ctype_digit($cpf)) {
            return false;
        }
        
        // Verificar tamanho
        if (strlen($cpf) != 11) {
            return false;
        }
        // Verificar digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        // Fazer o calculo de validação
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }
    
    static public function ufIsValid($uf) {
        return isset(self::$UFsToCEP[$uf]);
    }
    
    static public function cepIsValid($cep) {
        // Verificar se é somente números
        if (!ctype_digit($cep)) {
            return false;
        }
        
        // Verificar tamanho
        if (strlen($cep) != 8) {
            return false;
        }
        
        return true;
    }
    
    static public function cepIsValidForUf($cep, $uf) {
        $prefix = substr($cep, 0, -3);
        
        foreach(self::$UFsToCEP[$uf] as $faixas => &$faixa) {
            if($prefix >= $faixa[0] && $prefix <= $faixa[1]) {
                return true;
            }
        }
        
        unset($faixa);
        return false;
    }

    static public function isMobile(&$AGENT) {
        //return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
        return ASSINATURA_MOBILES && preg_match("/(" . ASSINATURA_MOBILES . ")/i", $AGENT);
    }
}
