<?php

namespace Geral\Controller;

class TestDbAdminController extends AbstractTestController
{
    function __construct()
    {
        parent::__construct();

        $this->setTestConfig('POST', [
            '/Geral/DbAdmin/executarComandosSQL' => [
                'data' => '{
                    "comandosSql": "INSERT into Pais (id, descricao) values (999, \'Chile\');"
                }',
                'dataAttrToUrl' => []
            ],
            '/Geral/DbAdmin/executarArquivoSQL' => [
                'data' => '{
                    "fileName": "sql_Geral_16298350779722.txt"
                }',
                'dataAttrToUrl' => []
            ]
        ]);

    }
}
