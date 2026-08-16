<?php

namespace Geral\Controller;

use ErrorException;
use Geral\Model\Factory\DbAdminFactory;
use Geral\Model\Factory\LogFactory;
use Geral\Model\UserPreference;
use Geral\Model\UserSession;


class DbAdminController extends AbstractServiceController
{

    function __construct()
    {
        parent::__construct();

        if (!$this->auth->isAllowedTransactions(['dbaAdmin'])) {
            LogFactory::error('Usuário sem permissão para acessar este módulo.');
            die(LogFactory::getLastMsg());
        }
    }

    public function index()
    {
        $this->view();
    }

    // Salva o conteúdo das abas nas preferências do usuário.
    public function saveTabs()
    {
        $tabs = is_array($_POST['tabs']) ? array_slice($_POST['tabs'], 0, 5, true) : [];

        $userPreference = new UserPreference(UserSession::getParam('cpf'));
        $userPreference->setPreference('sqlTabs', $tabs);

        $this->setSuccess('Tabs salva com sucesso', ['tabs' => $userPreference->getPreference('sqlTabs')]);
        $this->sendResponse();
    }

    public function executarComandosSQL()
    {
        $comandosSql = $_POST['comandosSql'];

        try {
            $comandosExecutados = DbAdminFactory::executarTextoSQL($comandosSql);
            $this->setSuccess('Comandos executados com sucesso.', ['registros' => $comandosExecutados]);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
        }

        $this->sendResponse();
    }

    public function executarArquivoSQL()
    {
        $diretorioArquivo = DIR_ROOT . '/' . DIR_NAME_FILES . '/' .
            $this->url->getNameModule() . '/' . DIR_NAME_TEMP;
        $nomeArquivo = $diretorioArquivo . '/' . $_POST['fileName'];

        if (!file_exists($nomeArquivo)) {
            throw new ErrorException('Arquivo não localizado: ' . $nomeArquivo);
        }

        try {
            $comandosExecutados = DbAdminFactory::executarArquivoSQL($nomeArquivo);

            if ($comandosExecutados == true) {
                $this->setSuccess('Comandos executados com sucesso.');
            } else {
                $this->setError('Falha ao executar os comandos SQL.');
            }
        } catch (\Exception $e) {
            $this->setError('SQL não aprovado: ' . $e);
        }

        $this->sendResponse();
    }

    // Geração de SQL nativa com base nas tabelas do Banco de Dados criadas de acordo com as entidades.
    public function sqlQueryBuilder()
    {
        $modules = \Geral\Model\Config::getAllEntities();
        $tables = [];
        // Faz o mapeamento Entity => Nome Tabela. Utilizado para mapear as associações (FK) com o nome da mesma no BD.
        $entityToTableName = [];
        // Armazena as aplicações existentes no sistema.
        $applications = [];
        
        // Aplicações (/modules).
        foreach($modules as $module => $entities) {
            // Entidades (<modulo>/Entity);

            // Salva o nome da aplicação (/module/<app>)
            $applications[$module] = '';

            foreach($entities as $i => $entity) {
                try {
                    $entity = str_replace('/', '\\', $entity);
                    
                    // Pegar os metadados de todas as classes entidades da aplicação.
                    $metadataOfClasse = $this->em->getClassMetadata($entity);
                    
                    // Namespace da entity para o nome da tabela.
                    $entityToTableName[$metadataOfClasse->name] = $metadataOfClasse->table['name'];
                    // Armazena as relações da tabela com outras tabelas externas.
                    $associations = [];

                    // Somente se a entidade tiver relacionamento externo.
                    if(!empty($metadataOfClasse->associationMappings)) {
                        // Relações entre tabelas
                        foreach($metadataOfClasse->associationMappings as $tableName => $association) {
                            // Relação ManyToMany não tem uma entidade própria de classe associativa, por isso tem que ser extraída de uma das tabelas envolvidas.
                            if(!empty($association['joinTable'])) {
                                // Dados sobre a tabela da entidade no BD.
                                $tables[$association['joinTable']['name']] = [
                                    // Nome da tabela no BD.
                                    'name' => $association['joinTable']['name'],
                                    // Schema da tabela
                                    'schema' => $metadataOfClasse->table['schema'] ? $metadataOfClasse->table['schema'] : BD_NAME,
                                    // Aplicação da entidade (/modules/<app>).
                                    'application' => $module,
                                    // Dados sobre as colunas da tabela.
                                    'columns' => [],
                                    // Colunas que possuem relação externa à tabela.
                                    'associations' => [
                                        $association['joinTable']['joinColumns'][0]['name'] => [
                                            // Namespace
                                            'table' => $association['sourceEntity'],
                                            // Coluna que tem a chave externa
                                            'from' => $association['joinTable']['joinColumns'][0]['name'],
                                            // Coluna da tabela externa relacionada
                                            'to' => $association['joinTable']['joinColumns'][0]['referencedColumnName']
                                        ],
                                        $association['joinTable']['inverseJoinColumns'][0]['name'] => [
                                            // Namespace
                                            'table' => $association['targetEntity'],
                                            // Coluna que tem a chave externa
                                            'from' => $association['joinTable']['inverseJoinColumns'][0]['name'],
                                            // Coluna da tabela externa relacionada
                                            'to' => $association['joinTable']['inverseJoinColumns'][0]['referencedColumnName']
                                        ]
                                    ]
                                ];
                            }

                            // Se não tiver atributos de relação externa OneToMany ou ManyToOne, ignora.
                            if(empty($association['joinColumns'])) continue;

                            // Atributos
                            foreach($association['joinColumns'] as $column) {
                                $associations[$tableName] = [
                                    // Namespace
                                    'table' => $association['targetEntity'],
                                    // Coluna que tem a chave externa
                                    'from' => $column['name'],
                                    // Coluna da tabela externa relacionada
                                    'to' => $column['referencedColumnName']
                                ];

                                // Assumindo apenas uma coluna de relação para uma mesma tabela externa.
                                break;
                            }
                        }
                    }

                    // Dados sobre a tabela da entidade no BD.
                    $tables[$metadataOfClasse->table['name']] = [
                        // Nome da tabela no BD.
                        'name' => $metadataOfClasse->table['name'],
                        // Schema da tabela
                        'schema' => $metadataOfClasse->table['schema'] ? $metadataOfClasse->table['schema'] : BD_NAME,
                        // Aplicação da entidade (/modules/<app>).
                        'application' => $module,
                        // Dados sobre as colunas da tabela.
                        'columns' => $metadataOfClasse->fieldMappings,
                        // Colunas que possuem relação externa à tabela.
                        'associations' => $associations
                    ];
                } catch(\Exception $e) {
                    new ErrorException('Erro ao recuperar dados de sincronização da entidades: '.$e->getMessage());
                }
            }
        }

        // Ordena as tabelas por nome.
        \ksort($tables, SORT_FLAG_CASE | SORT_STRING);

        // Conultas as abas salvas pelo usuário.
        $userPreference = new UserPreference(UserSession::getParam('cpf'));
        
        $this->addVarView('sqlTabs', $userPreference->getPreference('sqlTabs'));
        $this->addVarView('applications', $applications);
        $this->addVarView('entityToTableName', $entityToTableName);
        $this->addVarView('tables', $tables);
        $this->view();
    }
}
