<?php

namespace Geral\Controller;

class TestLocalAuthorizerController extends AbstractTestController
{
    function __construct()
    {
        parent::__construct();

        $this->setTestConfig('POST', [
            '/Geral/LocalAuthorizer/saveTransactionsByCpf' => [
                'data' => '{
                    "cpf": [ "00510060900", "03343841927" ],
                    "transactionsByApp[Exemplo][]": [ "aprovacao", "admin" ],
                    "transactionsByApp[Geral][]": [ "admin", "desenvolvedor" ]
                }',
                'dataAttrToUrl' => []
            ]
        ]);
    }
}
