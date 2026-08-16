<?php
namespace Geral\Controller;

use \Geral\Model\Config;
use Geral\Model\Factory\AuditFactory;
use Geral\Model\UdevUpdate;
use \Geral\Model\Validacao;

class InstallController extends AbstractPublicController
{
    public function __construct( )
    {
        parent::__construct( );
    }

    public function index( )
    {
        $this -> url -> redirect( 'Install', 'formDados' );
    }

    public function formDados( )
    {
        try {
            $transactions = \Geral\Entity\LocalAuthorizer::getAll();
        } catch (\Exception $e) {
            
        }
        
        if(!empty($transactions)) {
            $msg = 'O framework já está instalado.';
        }
        
        $directories = [DIR_NAME_FILES => 0];

        foreach($directories as $directory => &$writeable) {
            $writeable = is_writeable(DIR_ROOT.'/'.$directory);
        }
        
        unset($writeable);
        
        // Verificar se a variável do apache "AMBIENTE" está definido.
        $varAmbienteIsSet = isset($_SERVER['AMBIENTE']) && in_array($_SERVER['AMBIENTE'], ['DEV', 'HOM', 'PROD']) ? $_SERVER['AMBIENTE'] : null;
        
        // https://www.doctrine-project.org/projects/doctrine-dbal/en/2.7/reference/configuration.html
        $extensions = [
            'pdo_sqlite' => 0,
            'pdo_mysql' => 0,
            'ldap' => 0
            //'pdo_pgsql' => 0,
            //'pdo_sqlsrv' => 0
        ];
        
        foreach($extensions as $extension => &$installed) {
            $installed = extension_loaded($extension);
        }
        
        unset($installed);
        
        $this -> addVarView('varAmbienteIsSet', $varAmbienteIsSet);
        $this -> addVarView('directories', $directories);
        $this -> addVarView('extensions', $extensions);
        $this -> addVarView('msg', $msg);
        $this -> view();
    }
    
    public function install( )
    {
        try {
            $transactions = \Geral\Entity\LocalAuthorizer::getAll();
        } catch (\Exception $e) {
            $fup = new UdevUpdate();
            $fup->syncEntitiesWithDb();
        }
        
        if(!empty($transactions)) {
            die('O framework já está instalado.');
        }

        if(Validacao::cpfIsValid($_POST['cpf'])) {
            if(is_writable(DIR_FILES.'/Geral/'.DIR_NAME_TEMP)) {
                try {
                    $transactions = \Geral\Entity\LocalAuthorizer::getAll();
                } catch (\Exception $e) {
                    
                }

                if (empty($transactions)) {
                    // Iniciar ferramenta doctrine para persistencia das classes no Banco
                    $toolDoctrine = new \Geral\Model\ToolDoctrine('Geral', $GLOBALS['em']);
                    
                    // Pegar as entidades encontradas no diretório \Geral\Entity
                    $entities = Config::getAllEntities(false);
                    //$entities = array_keys($entities);
                    $msgsOk = ['Tabela(s) sincronizadas com sucesso.'];
                    
                    $transactionsByApp = [
                        'Geral' => ['admin']
                    ];

                    $templateTransactions = Config::getValue('transactions', 'Template');

                    if(!empty($templateTransactions)) {
                        $templateTransactions = array_keys(Config::getValue('transactions', 'Template'));
                        
                        if(is_array($templateTransactions)) {
                            $transactionsByApp['Template'] = $templateTransactions;
                        }
                    }
                    
                    // Salvar transação padrão
                    \Geral\entity\LocalAuthorizer::saveTransactionsByCpf($_POST['cpf'], $transactionsByApp);
                    
                    $dashboards = [];

                    foreach(\Geral\Entity\DashBoard::$defaultDashboards as $i => $defaultDashboard) {
                        $dashboards[$i] = \Geral\Entity\DashBoard::create($defaultDashboard['descricao'], $defaultDashboard['template'], $defaultDashboard['conf'], 0 );
                        $dashboards[$i] -> setLock(true);
                        $GLOBALS['em'] -> persist($dashboards[$i]);
                    }

                    $GLOBALS['em'] -> flush();
                } else {
                    $msgsErro[] = 'O framework já está instalado.';
                }
            } else {
                $msgsErro[] = 'Diretório temp não pode ser escrito pelo www-data.';
            }
        } else {
            $msgsErro[] = 'CPF inválido. Somente números são aceitos.';
        }
        
        AuditFactory::auditInfo('Instalação finalizada.');

        $this -> addVarView('msgsOk', $msgsOk);
        $this -> addVarView('msgsErro', $msgsErro);
        $this -> addVarView('entities', $entities);
        $this -> view();
    }
}
