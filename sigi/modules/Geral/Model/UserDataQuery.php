<?php
namespace Geral\Model;

use Geral\Model\Exception\ErrorException;
use Geral\Model\Factory\UserDataFactory;
use Geral\Model\Interfaces\UserDataQueryInterface;
use Geral\Model\UserDataServiceOptions;

class UserDataQuery implements UserDataQueryInterface
{
    private $filter;
    private $options;

    public function __construct() {}

    public function byCpf($cpf)
    {
        self::convertToArray($cpf);
        $this->filter['cpf'] = $cpf;

        return $this;
    }

    public function byUnidade($unidade)
    {
        self::convertToArray($unidade);
        $this->filter['unidade'] = $unidade;

        return $this;
    }

    public function byVinculo($vinculo)
    {
        self::convertToArray($vinculo);
        $this->filter['vinculo'] = $vinculo;

        return $this;
    }

    public function loadFoto()
    {
        $this->options = new UserDataServiceOptions();
        $this->options->setCarregaFoto(true);

        return $this;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getResult()
    {
        return UserDataFactory::executeQuery();
    }

    public function getArrayResult()
    {
        return UserDataFactory::executeQuery(true);
    }

    public function getSingleResult()
    {
        $registros = UserDataFactory::executeQuery();

        if(count($registros) > 1) {
            throw new ErrorException('Mais de um registro foi encontrado.');
        }

        return !empty($registros) ? current($registros) : null;
    }

    public function getArraySingleResult()
    {
        $registros = UserDataFactory::executeQuery(true);

        if(count($registros) > 1) {
            throw new ErrorException('Mais de um registro foi encontrado.');
        }

        return !empty($registros) ? current($registros) : [];
    }

    private static function convertToArray(&$val)
    {
        if(!is_array($val)) {
            $val = [$val];
        }
    }
}
