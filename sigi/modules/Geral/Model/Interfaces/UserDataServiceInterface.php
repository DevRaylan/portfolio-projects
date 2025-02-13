<?php
namespace Geral\Model\Interfaces;

use Geral\Model\Interfaces\UserDataQueryInterface;
use Geral\Model\UserDataServiceOptions;

interface UserDataServiceInterface
{
    // Consultas
    public function getVinculosByCpf(String $cpf, UserDataServiceOptions $options=null);
    public function executeQuery(UserDataQueryInterface $userDataQuery);
}