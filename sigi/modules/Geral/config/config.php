<?php
/**
* Variáveis definidas para todos os ambientes da aplicação (DEV, HOM, PROD).
* No apache deve estar setada a constante AMBIENTE para um dos seguintes valores:
* - DEV: ambiente de desenvolvimento
* - HOM: ambiente de homologação
* - PROD: ambiente de produção
*
* Ex: No apache o arquivo site-available/<site>.conf deve ter a linha com "SetEnv AMBIENTE DEV"
* para o ambiente de desenvolvimento da aplicação.
*/

// Busca automaticamente o nome do módulo pelo diretório onde está o arquivo config.php
$myModule = \Geral\Model\Config::getModuleDirName(__FILE__);

// Aqui são definidas as variáveis para o módulo no formato Array<nome da variável> => <valor da variável>
$config["title"] = "Home"; // Título que irá aparecer na barra superior da aplicação
$config["blockTitle"] = "Home"; // Título que irá aparecer na lista de aplicações
$config['blockIcon'] = 'home'; // glyphicon que irá aparecer na lista de aplicações
$config['menu'] = [
    //'Index' => [],
    'Admin' => [
        [
            'descricao' => 'Arquivos',
            'glyphicon' => 'download-alt',
            'transactions' => ['admin'],
            'links' => [
                [
                    'descricao' => 'Lista de arquivos',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/FileRepository',
                    'glyphicon' => 'file',
                    'transactions' => ['admin']
                ],
                [
                    'descricao' => 'Diretórios / Pastas',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/Admin/listDirectoryFiles',
                    'glyphicon' => 'level-up',
                    'transactions' => ['admin']
                ],
                [
                    'descricao' => 'Área de testes',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/TestFileRepository',
                    'glyphicon' => 'cog',
                    'transactions' => ['admin']
                ]
            ]
        ],
        [
            'descricao' => 'Autorizações de Usuários',
            'url' => $GLOBALS['url'] -> getUrlModule().'/LocalAuthorizer',
            'glyphicon' => 'user',
            'transactions' => ['admin']
        ],
        [
            'descricao' => 'Clientes & Servidores',
            'glyphicon' => 'globe',
            'transactions' => ['admin'],
            'links' => [
                [
                    'descricao' => 'Clientes Web',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/WebClient',
                    'glyphicon' => 'globe',
                    'transactions' => ['admin']
                ],
                [
                    'descricao' => 'Servidores de arquivos',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/ConfigConn',
                    'glyphicon' => 'hdd',
                    'transactions' => ['admin']
                ]
            ]
        ],
        [
            'descricao' => 'Dados básicos',
            'glyphicon' => 'inbox',
            'transactions' => ['admin'],
            'links' => [
                [
                    'descricao' => 'Setores',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/RegistroSimples/index/setor',
                    'glyphicon' => 'inbox',
                    'transactions' => ['admin']
                ],
                [
                    'descricao' => 'Países',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/Ibge/paises',
                    'glyphicon' => 'globe',
                    'transactions' => ['admin']
                ],
                [
                    'descricao' => 'Estados e Municípios',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/Ibge/estados',
                    'glyphicon' => 'road',
                    'transactions' => ['admin']
                ]
            ]
        ],
        [
            'descricao' => 'E-mails enviados',
            'url' => $GLOBALS['url'] -> getUrlModule().'/Admin/listMailMessages',
            'glyphicon' => 'envelope',
            'transactions' => ['admin']
        ],
        [
            'descricao' => 'Entidades restrito',
            'url' => $GLOBALS['url'] -> getUrlModule().'/Admin/adminEntities',
            'glyphicon' => 'cog',
            'transactions' => ['admin']
        ],
        [
            'descricao' => 'Logs do sistema',
            'url' => $GLOBALS['url'] -> getUrlModule().'/Admin/viewLogs',
            'glyphicon' => 'list-alt',
            'transactions' => ['admin']
        ],
        [
            'descricao' => 'Mensagens de erro',
            'url' => $GLOBALS['url'] -> getUrlModule().'/Admin/listErrors',
            'glyphicon' => 'warning-sign',
            'transactions' => ['admin']
        ],
        [
            'descricao' => 'Consultas SQL',
            'url' => $GLOBALS['url'] -> getUrlModule().'/DbAdmin',
            'glyphicon' => 'retweet',
            'transactions' => ['dbaAdmin']
        ],
        [
            'descricao' => 'Tarefas Agendadas',
            'url' => $GLOBALS['url'] -> getUrlModule().'/TarefaAgendada',
            'glyphicon' => 'time',
            'transactions' => ['admin']
        ]
    ],
    'DbAdmin' => [
        [
            'descricao' => 'Importar SQL',
            'url' => $GLOBALS['url'] -> getUrlModule().'/DbAdmin',
            'glyphicon' => 'retweet',
            'transactions' => ['dbaAdmin']
        ],
        [
            'descricao' => 'Editar SQL',
            'url' => $GLOBALS['url'] -> getUrlModule().'/DbAdmin/sqlQueryBuilder',
            'glyphicon' => 'pencil',
            'transactions' => ['dbaAdmin']
        ],
        [
            'descricao' => 'Área administrativa',
            'url' => $GLOBALS['url'] -> getUrlModule().'/Admin',
            'glyphicon' => 'arrow-left',
            'transactions' => ['dbaAdmin']
        ]
    ]
];

$config["menu"]['LocalAuthorizer'] =
$config["menu"]['UserDataSimulator'] =
$config["menu"]['WebClient'] =
$config["menu"]['ConfigConn'] =
$config["menu"]['TestFileRepository'] =
$config["menu"]['FileRepository'] =
$config["menu"]['RegistroSimples'] =
$config["menu"]['Ibge'] =
$config["menu"]['TarefaAgendada'] =
&$config["menu"]['Admin'];

// #########################################################
// Configurações do serviço de autorização local de usuários
// #########################################################

//define($myModule.'_AUTHORIZER_TYPE', 'Config');

$config['baseAuthorizer'] = [
    'transactions' => [
        '04171018994' => ['admin', 'UPDATE_ENTITY'] // CPF => Array de transações
    ],
    'data' => [
        // CPF => dados
    ]
];

// ############################################################
// Configurações do Menu lateral (default: aberto)
// ############################################################

$config[$myModule.'MenuOpened'] =  true;

// ############################################################
// Configurações do serviço de auditoria automática do sistema
// ############################################################

define($myModule.'_AUDIT_LEVEL', 'ALL');
//define($myModule.'_AUDIT_LEVEL', 'PRIVATE');

// ###################################################################
// Configurações do serviço de autorização com dados de um WebService
// ###################################################################


//define($myModule.'_AUTHORIZER_TYPE', 'WebServer');

$config['authWebServer'] = [
    //'url' => 'https://gia.dev.udesc.br/GIA/WS/obtemTransacoes',
    'url' => 'https://gia.dev.udesc.br/GIA/WS/obtemAcessos',
    'tokenSystem' => 'ID-UDESC',
    'tokenSecret' => '',
    'transactionsField' => 'transacoes',
    'dataField' => 'acessoDados'
];

$config['authWS'] = [
    'url' => 'https://ws.sistemas.udesc.br/GIA/WS',
    'tokenSystem' => 'd9217591445855083497a791f12296f8',
    'tokenSecret' => 'b0f2b86e7699db913098a19f5f8628f390629251',
    'endpointObtemVinculos' => '/obtemVinculos'
];

define($myModule.'_AUTHORIZER_TYPE', 'Local');

define($myModule.'_SMTP_REPLYTO', '');
define($myModule.'_SMTP_REPLYTO_NAME', '');

$config['transactions'] = [
    'admin' => [
        'titulo' => 'Administrador',
        'descricao' => 'Pode realizar TODAS as ações do framework.'
    ],
    'view' => [
        'titulo' => 'Acesso básico a aplicação '.$myModule.'.',
        'descricao' => 'Pode acessar o HOME global da aplicação.'
    ],
    'desenvolvedor' => [
        'titulo' => 'Desenvolvedor',
        'descricao' => 'Pode acessar áreas para fins de testes e desenvolvimento.'
    ],
    'adminUsers' => [
        'titulo' => 'Gerenciar Usuários',
        'descricao' => 'Pode acessar incluir novos usuários e conceder permissões.'
    ],
    'dbaAdmin' => [
        'titulo' => 'Administrador do BD',
        'descricao' => 'Pode alterar o BD.'
    ]
];

/**
 * Parâmetros do usuários, cada parâmetro deve conter:
 * - descricao: string, descrição do parâmetro
 * - multiplosValores: boolean, informa se a preferência pode conter mais de um valor por usuário.
 * 
 * Se "multiplosValores" for true, então o valor do parâmetro sempre será um vetor.
 * - Exemplo de valores de um parâmetro não vazio: ["valor 1", valor 2"].
 * - Exemplo de valor de um parâmetro vazio: []
 * 
 * Se "multiplosValores" for false, então o valor será escalar.
 * - Exemplo de valor de um parâmetro não vazio: 'tema1'
 * - Exemplo de valor de um parâmetro vazio: null
 */
$config['userPreferences'] = [
    'defaultApp' => [
        'descricao' => 'Aplicação padrão do usuário.',
        'multiplosValores' => false
    ],
    'linksFavoritos' => [
        'descricao' => 'Links favoritos.',
        'multiplosValores' => true
    ],
    'fixaMenu' => [
        'descricao' => 'Fixa o menu lateral.',
        'multiplosValores' => false
    ],
    'tema' => [
        'descricao' => 'Tema da aplicação.',
        'multiplosValores' => false
    ],
    'sqlTabs' => [
        'descricao' => 'Armazena SQLs editadas pelo usuário.',
        'multiplosValores' => true
    ]
];
