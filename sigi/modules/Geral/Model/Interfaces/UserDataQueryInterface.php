<?php
namespace Geral\Model\Interfaces;

interface UserDataQueryInterface
{
    public function byCpf($cpfs);
    public function byUnidade($unidades);
    public function byVinculo($vinculos);
    public function getFilter();
    public function getResult();
    public function getSingleResult();
    public function getArrayResult();
    public function getArraySingleResult();
}
