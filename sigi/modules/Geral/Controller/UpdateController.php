<?php

namespace Geral\Controller;

use \Geral\Model\Factory\DbAdminFactory;
use \Geral\Model\UdevUpdate;
use \Geral\Model\Login;

class UpdateController extends AbstractPublicController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        // Anular exceção lançada pelo doctrine ao auditar esta ação.
    }

    public function index()
    {
        $this->url->redirect('Update', 'check');
    }

    // Mostra tela com os SQls para sincronização com o Banco de Dados.
    public function check()
    {
        if (!in_array($_SERVER['AMBIENTE'], ['DEV', 'HOM'])) {
            $this->setError('Este recurso só pode ser utilizado nos ambientes de Homologação e Desenvolvimento.');
            $this->sendResponse();
        }

        // Entidades que utilizam o mesmo nome de tabela no banco de dados.
        $conflictedEntities = '';

        // Array de SQLs separados por schema (Nome do banco de dados).
        $sqlsToSyncBySchema = [];

        $fup = new UdevUpdate;
        $sqlsToSyncBySchema = $fup->getSqlToSync();

        try {
            $fup->getConflictTableNames();
        } catch (\Exception $e) {
            // É lançado uma exceção quando há conflitos, então armazena a mensagem na variável.
            $conflictedEntities = $e->getMessage();
        }

        $this->addVarView('conflictedEntities', $conflictedEntities);
        $this->addVarView('sqlsToSyncBySchema', $sqlsToSyncBySchema);
        $this->addVarView('deployKey', DEPLOY_KEY);
        $this->view(null, ['SEM_TOPO']);
    }

    // Faz a sincronização das entidades com o banco de dados.
    public function execute()
    {
        $fimMsg = '### Fim ###' . PHP_EOL;
        $erroMsg = '* ERRO *.' . PHP_EOL;

        echo '#############################################' . PHP_EOL;
        echo '### Log UDev: sincronizador de entidades. ###' . PHP_EOL;
        echo '#############################################' . PHP_EOL;
        echo '- Validando credencial ... ';

        // Verificar o deploy key
        if (empty($this->request->post('key')) || $this->request->post('key') !== DEPLOY_KEY) {
            echo $erroMsg;
            echo '===> Chave de acesso ao deploy inválida.' . PHP_EOL;
            exit;
        }

        echo 'OK.' . PHP_EOL;

        echo '- Verificando se o UDev está instalado ... ';
        /*
        * Pega o dashboard do usuário.
        * Se for lançado um erro de tabala inexistente, então o framework não está instalado. 
        */
        try {
            $dashboard = \Geral\Entity\DashBoard::getDefaultFromUser(null);
            echo 'OK' . PHP_EOL;
        } catch (\Exception $e) {
            echo $erroMsg;
            echo '===> Erro: não instalado. Por favor, realize a instalação antes de proceder.' . PHP_EOL;
            echo $fimMsg;
            exit;
        }

        echo '- Verificando dados de conexão ... ';
        echo 'OK' . PHP_EOL;
        echo '-   Hostname: ' . BD_HOST . PHP_EOL;
        echo '-   Driver: ' . BD_DRIVER . PHP_EOL;

        $fup = new UdevUpdate();

        echo "- Verificando conflitos ... ";

        try {
            // Procurar por conflitos.
            $fup->getConflictTableNames();
            echo 'OK.' . PHP_EOL;
        } catch (\Exception $e) {
            echo $erroMsg;
            echo '===> ' . $e->getMessage() . PHP_EOL;
            echo $fimMsg;
            exit;
        }

        echo '- Gerando SQLs para execução ... ';

        try {
            $sqlsToSyncBySchema = $fup->getSqlToSync();
            echo 'OK.' . PHP_EOL;

            if (!empty($sqlsToSyncBySchema)) {
                foreach ($sqlsToSyncBySchema as $schema => &$sqls) {
                    echo '--  SQL gerada para o schema "' . $schema . '"' . PHP_EOL;
                    echo '    ' . implode(';' . PHP_EOL . '    ', $sqls) . ';' . PHP_EOL;
                }
            } else {
                echo '--> Não há SQLs para execução.' . PHP_EOL;
            }
        } catch (\Exception $e) {
            echo $erroMsg;
            echo '===> ' . $e->getMessage() . PHP_EOL;
            echo $fimMsg;
            exit;
        }

        echo '- Sincronizando entidades ... ';

        if (!empty($sqlsToSyncBySchema)) {
            try {
                // Sincronizar entidades com o banco de dados.
                $fup->syncEntitiesWithDb();

                echo 'OK.' . PHP_EOL;
            } catch (\Exception $e) {
                echo 'Erro.' . PHP_EOL;
                echo '===> ' . $e->getMessage() . PHP_EOL;
                echo $fimMsg;
                exit;
            }
        } else {
            echo 'OK.' . PHP_EOL;
            echo '--> Entidades do Udev e aplicações já sincronizadas.' . PHP_EOL;
        }

        echo $fimMsg;
    }

    /**
     * Retorna as tarefas agendadas para adicionar ao CRON
     * @return void
     */
    public function getScheduleTasks(){
        // Verificar o deploy key
        if (empty($this->request->post('key')) || $this->request->post('key') !== DEPLOY_KEY) {
            http_response_code(401);
            exit;
        }

        $tasks  = \Geral\Model\Factory\TarefaAgendadaFactory::getAllSchedule();
        $output = [];
        foreach($tasks as $task){
            $output[] = $task->toSchedule("https://".$_SERVER['SERVER_NAME']);
        }
        
        echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
