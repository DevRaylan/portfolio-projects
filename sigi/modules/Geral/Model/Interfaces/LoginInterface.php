<?php
namespace Geral\Model\Interfaces;

interface LoginInterface
{
    public function autenticar( $id, $password );
}
