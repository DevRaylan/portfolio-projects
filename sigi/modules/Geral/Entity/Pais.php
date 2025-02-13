<?php

namespace Geral\Entity;

use Geral\Entity\RegistroSimples;

/** @Entity */
class Pais extends RegistroSimples
{

    public function setAtributos($atributos = [])
    {
        if (empty($atributos)) {
            return;
        }

        $this->setDescricao(trim($atributos[0]));
    }

}
