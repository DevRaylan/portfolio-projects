<?php

namespace Geral\Controller;

class TestAuditController extends AbstractTestController
{
    function __construct()
    {
        parent::__construct();

        ///Template/Index/findAuditByParams
        
        $this->setTestConfig('POST', [
            '/Geral/Audit/findByParams' => [
                'data' => '{
                    "cpf": "",
                    "data": "",
                    "tipo": "E",
                    "palavrasChave": "",
                    "modulo": ""
                }',
                'dataAttrToUrl' => []
            ]
        ]);
    }
}
