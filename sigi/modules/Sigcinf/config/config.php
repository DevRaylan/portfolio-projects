<?php
// Busca automaticamente o nome do módulo pelo diretório onde está o arquivo config.php
$myModule = \Geral\Model\Config::getModuleDirName(__FILE__);

// Aqui são definidas as variáveis para o módulo no formato Array<nome da variável> => <valor da variável>
$config["title"] = "$myModule"; // Título que irá aparecer na barra superior da plicação
$config["blockTitle"] = "SIGCINF"; // Título que irá aparecer na lista de aplicações

/**
 * Definição do ícone que será exibido na lista de aplicações do menu superior
 *  [Opção 1]: blockIcon - será utilizado um ícone de glyphicon.
 *    A lista de ícones suportados pode ser acessada em https://getbootstrap.com/docs/3.3/components/#glyphicons
 *    Exemplo de uso: $config['blockIcon'] = 'cutlery';
 *  [Opção 2]: blockIconFA - será utilizado um ícone de FontAwesome (apenas ícones 'Free')
 *    A lista de ícones suportados pode ser acessada em https://fontawesome.com/icons?d=gallery
 *    OBS: utilizar a string completa da classe
 *    Exemplo de uso: $config['blockIconFA'] = 'fas fa-ghost';
 */
$config['blockIcon'] = 'cloud';

// Links do menu de contexto da aplicação
$config["menu"] = [
    'Index' => [
        [
            'descricao' => 'Home',
            'url' => $GLOBALS['url'] -> getUrlModule().'/Index/index', // /Index/index é o padrão caso não seja declarado nada após /app/.
            'glyphicon' => 'home',
            // Como alternativa ao glyphicon, pode-se utilizar ícones fontawesome:
            // ex: 'fontawesome' => 'fas fa-home',
            'transactions' => []
        ],

        [
            'descricao' => 'Minhas solicitações',
            'url' => $GLOBALS['url'] -> getUrlModule().'/Solicitacao/index', // /Index/index é o padrão caso não seja declarado nada após /app/.
            'glyphicon' => 'star',
            'transactions' => []
        ],

        [
            'descricao' => 'Solicitações',
            'glyphicon' => 'th-list',
            'transactions' => ['dev','admin','gerente'],
            'links' => [
                [
                    'descricao' => 'Cadastrar',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/Solicitacao/incluir',
                    'glyphicon' => 'plus-sign',
                    'transactions' => ['dev','admin','gerente']
                ],
                [
                    'descricao' => 'Moderar',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/Solicitacao/moderar',
                    'glyphicon' => 'pencil',
                    'transactions' => ['dev','admin','gerente']
                ]            
            ]
        ],

       
                [
                    'descricao' => 'Relatório',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/Solicitacao/relatorio',
                    'glyphicon' => 'glyphicon glyphicon-print',
                    'transactions' => ['dev','admin','gerente']
                ],
                        

        [
            'descricao' => 'Bens',
            'glyphicon' => 'list-alt',
            'transactions' => ['dev','admin','gerente'],
            'links' => [
                [
                    'descricao' => 'Com patrimonio',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/BemComPat/index',
                    'fontawesome' => 'fa fa-barcode',
                    'transactions' => []
                ],
                [
                    'descricao' => 'Sem patrimonio',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/BemSemPat/index',
                    'fontawesome' => 'fa fa-paperclip',
                    'transactions' => []
                ]              
            ]
        ],           

        [
            'descricao' => 'Tabelas',
            'glyphicon' => 'list-alt',
            'transactions' => ['dev','admin'],
            'links' => [
                [
                    'descricao' => 'Unidades',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/Unidades/index',
                    'fontawesome' => 'fa fa-university',
                    'transactions' => []
                ],
                [
                    'descricao' => 'Setores',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/Setores/index',
                    'fontawesome' => 'fa fa-users',
                    'transactions' => []
                ],
                [
                    'descricao' => 'Usuários',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/Usuario/index',
                    'fontawesome' => 'fa fa-users',
                    'transactions' => []
                ],
                [
                    'descricao' => 'Categorias',
                    'url' => $GLOBALS['url'] -> getUrlModule().'/Categorias/index',
                    'fontawesome' => 'fa fa-users',
                    'transactions' => []
                ],
            ]
        ], 

        [
            'descricao' => 'Configurações',
            'fontawesome' => 'fa fa-cog',
            'transactions' => ['dev','admin'],
            'links' => [
                [
                    'descricao' => 'UDEV',
                    'url' => '/Geral/Admin/index',
                    'glyphicon' => 'list-alt',
                    'transactions' => []
                ]              
            ]
        ],

    ],    
];

$config["menu"]['Solicitacao'] = $config["menu"]['Index'];
$config["menu"]['Unidades'] = $config["menu"]['Index'];
$config["menu"]['Setores'] = $config["menu"]['Index'];
$config["menu"]['BemComPat'] = $config["menu"]['Index'];
$config["menu"]['BemSemPat'] = $config["menu"]['Index'];
$config["menu"]['Usuario'] = $config["menu"]['Index'];
$config["menu"]['Categorias'] = $config["menu"]['Index'];

// #########################################################
// Configurações do serviço de autorização local de usuários
// #########################################################
//define($myModule.'_AUTHORIZER_TYPE', 'Config');

$config['baseAuthorizer'] = [
    'transactions' => [
        '04171018994' => ['view'], // CPF => Array de transações
        '00608031933' => ['view'] // CPF => Array de transações
    ]
];

// ###################################################################
// Configurações do serviço de autorização com dados de um WebService
// ###################################################################


//define($myModule.'_AUTHORIZER_TYPE', 'WebServer');


// ############################################################
// Configurações do Menu lateral (default: aberto)
// ############################################################

$config[$myModule.'MenuOpened'] =  false;

// ############################################################
// Configurações do serviço de auditoria automática do sistema
// ############################################################

define($myModule.'_AUDIT_LEVEL', 'ALL');
//define($str.'_AUDIT_LEVEL', 'PRIVATE');


$config['authWebServer'] = [
    'url' => 'https://gia.dev.udesc.br/GIA/WS/obtemAcessos',
    'tokenSystem' => 'GIA',
    'tokenSecret' => '',
    'transactionsField' => 'transacoes',
    'dataField' => 'acessoDados'
];

// Transações suportadas pela aplicação
define($myModule.'_AUTHORIZER_TYPE', 'Local');

$config['transactions'] = [
    'admin' => [
        'titulo' => 'Administrador da aplicação',
        'descricao' => 'Pode realizar todas as ações da aplicação.'
    ],
    'desenvolvedor' => [
        'titulo' => 'Desenvolvedor',
        'descricao' => 'Pode acessar áreas restritas para fins de testes e desenvolvimento.'
    ],
    'gerente' => [
        'titulo' => 'Gerente',
        'descricao' => 'Realiza as moderações.'
    ],
    'adminUsuarios' => [
        'titulo' => 'Gerenciar usuários',
        'descricao' => '...'
    ]
];

$config['endpoints'] = [
    'GET' => [
        '/'.$myModule.'/WSv1/user' => [
            'to' => 'getAll',
            'titulo' => 'Retornar todos os usuários.',
            'descricao' => 'Este Endpoint faz a consulta por usuários e retorna todos os registros encontrados.'
        ],
        '/'.$myModule.'/WSv1/user/{id}' => [
            'to' => 'getById',
            'titulo' => 'Retornar dados de usuário.',
            'descricao' => 'Este Endpoint faz a consulta por um usuário específico e retorna seus dados.'
        ],
        '/'.$myModule.'/WSv1/user/{id}/teste/{nome}' => [
            'to' => 'getById',
            'titulo' => 'Retornar dados de usuário.',
            'descricao' => 'Este Endpoint faz a consulta por um usuário específico e retorna seus dados.'
        ]
    ],
    'POST' => [
        '/'.$myModule.'/WSv1/user' => [
            'to' => 'add',
            'titulo' => 'Adicionar usuários.',
            'descricao' => 'Este Endpoint permite a adição de usuários.'
        ],
        '/'.$myModule.'/WSv1/user/{id}' => [
            'to' => 'save',
            'titulo' => 'Atualizar dados do usuário.',
            'descricao' => 'Este Endpoint permite a atualização dos dados do usuário.'
        ]
    ],
    'DELETE' => [
        '/'.$myModule.'/WSv1/user/{id}' => [
            'to' => 'remove',
            'titulo' => 'Remover usuário.',
            'descricao' => 'Este Endpoint remove um usuário do sistema.'
        ]
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
    ]
];