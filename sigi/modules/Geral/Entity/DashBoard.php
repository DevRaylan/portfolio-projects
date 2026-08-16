<?php
namespace Geral\Entity;
/**
 * @Entity
 * @Table(name="dashboard")
 **/
class DashBoard
{
    /**
     * @Id @Column(name="id_dashboard", type="integer")
     * @GeneratedValue
     **/
    private $id;

    /** @Column(type="string", length=255, nullable=false) **/
    private $descricao;

    /** @Column(type="string", length=1000, nullable=false) **/
    private $conf;

    /** @Column(type="string", length=50, nullable=false) **/
    private $template;

    /** @Column(name="id_usuario", type="string", length=11, nullable=false) **/
    private $idUser;

    /** @Column(name="padrao", type="boolean", nullable=false) **/
    private $padrao = false;

    /** @Column(name="`lock`", type="boolean", nullable=true) **/
    private $lock = false;

    static public $defaultDashboards = [
            1 => [
                'descricao' => 'Dashboard padrão genérico',
                'template' => 'dashboard_1',
                'conf' => [
                    1 => [
                        'url' => '/Modulo_1/Contratos/grafico_1',
                        'dados' => [
                            'tabela' => 1,
                            'grafico' => 0,
                            'isolado' => 1
                        ]
                    ],
                    2 => [
                        'url' => '/Modulo_1/Contratos/grafico_1',
                        'dados' => [
                            'tabela' => 0,
                            'grafico' => 1,
                            'isolado' => 1,
                            'tipo' => "line"
                        ]
                    ]
                ]
            ],
            2 => [
                'descricao' => 'Dashboard padrão para técnico',
                'template' => 'dashboard_1',
                'conf' => [
                    1 => [
                        'url' => '/Modulo_1/Contratos/grafico_1',
                        'dados' => [
                            'tabela' => 1,
                            'grafico' => 0,
                            'isolado' => 1
                        ]
                    ],
                    2 => [
                        'url' => '/Modulo_1/Contratos/grafico_1',
                        'dados' => [
                            'tabela' => 0,
                            'grafico' => 1,
                            'isolado' => 1,
                            'tipo' => "line"
                        ]
                    ]
                ]
            ],
            3 => [
                'descricao' => 'Dashboard padrão para professor',
                'template' => 'dashboard_1',
                'conf' => [
                    1 => [
                        'url' => '/Geral/Usuario/addOn1',
                        'dados' => [
                            'tabela' => 1,
                            'grafico' => 0,
                            'isolado' => 1
                        ]
                    ],
                    2 => [
                        'url' => '/Modulo_1/Contratos/grafico_1',
                        'dados' => [
                            'tabela' => 0,
                            'grafico' => 1,
                            'isolado' => 1,
                            'tipo' => "line"
                        ]
                    ]
                ]
            ]
        ];

    public function __construct() { }

    public function getId( )
    {
        return $this -> id;
    }

    public function getConf($string = false)
    {
        return $string ? $this -> conf : json_decode($this -> conf, true);
    }

    public function setConf( $conf )
    {
        $this -> conf = json_encode($conf, JSON_UNESCAPED_UNICODE);
    }

    public function getTemplate( )
    {
        return $this -> template;
    }

    public function setTemplate( $template )
    {
        $this -> template = $template;
    }

    /**
     * @param  $idUser
     */
    public function setIdUser($idUser) {
        $this->idUser = $idUser;
    }
    /**
     * @return
     */
    public function getIdUser() {
        return $this->idUser;
    }

    /**
     * @param  $padrao
     */
    public function setDefault($padrao) {
        $this -> padrao = !empty($padrao);
    }
    /**
     * @return
     */
    public function isDefault() {
        return $this->padrao;
    }
    /**
     * @param  $descricao
     */
    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }
    /**
     * @return
     */
    public function getDescricao() {
        return $this->descricao;
    }
    public function setLock($lock) {
        $this -> lock = !empty($lock);
    }
    /**
     * @return
     */
    public function isLock() {
        return $this -> lock;
    }

// ==================================================================================================================//

    static function getByIdUser($idUser)
    {
        return $GLOBALS['em'] -> getRepository('\Geral\Entity\DashBoard') -> findBy(['idUser' => $idUser]);
    }

    static function getDefaultFromUser($idUser)
    {
        return $GLOBALS['em'] -> getRepository('\Geral\Entity\DashBoard') -> findOneBy(['idUser' => $idUser, 'padrao' => true]);
    }

    static function getAll()
    {
        return $GLOBALS['em'] -> getRepository('\Geral\Entity\DashBoard') -> findAll();
    }

    static function getById($id)
    {
        return $GLOBALS['em'] -> find('\Geral\Entity\DashBoard', $id);
    }

    static function create($descricao, $template, Array $conf, $idUser)
    {
        $dashboard = new \Geral\Entity\DashBoard;
        $dashboard -> setDescricao($descricao);
        $dashboard -> setTemplate($template);
        $dashboard -> setConf($conf);
        $dashboard -> setIdUser($idUser);

        return $dashboard;
    }

    static function createDefaultDashBoardToUser($idUser, $perfil) {
        switch ($perfil) {
            case 'tecnico':
                $idDash = 2;
                break;
            case 'professor':
                $idDash = 3;
                break;
            default:
                $idDash = 1;
                break;
        }

        if(!isset(self::$defaultDashboards[$idDash])) {
            die('Dashboard padrão não existe.');
        }

        if(empty(self::getById($idDash))) {
            $GLOBALS['url']->redirect('Index', 'populate');
            exit;
        }

        $dashboard = \Geral\Entity\DashBoard::create(self::$defaultDashboards[$idDash]['descricao'], self::$defaultDashboards[$idDash]['template'], self::$defaultDashboards[$idDash]['conf'], $idUser );
        $dashboard -> setDefault(true);
        $dashboard -> setLock(true);

        return $dashboard;
    }

    private static function populateBase()
    {
        $dashboards = [];

        foreach(self::$defaultDashboards as $i => $defaultDashboard) {
            $dashboards[$i] = \Geral\Entity\DashBoard::create($defaultDashboard['descricao'], $defaultDashboard['template'], $defaultDashboard['conf'], 0 );
            $GLOBALS['em'] -> persist($dashboards[$i]);
        }

        $GLOBALS['em'] -> flush();
        die('o');
    }
}
