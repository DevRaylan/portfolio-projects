<?php
/**
 * @class AbstractController
 *
 * Esta classe é uma superclasse que é extendida por todos os controllers mais específicos do Framework.
 * Os controllers abstratos mais específicos são:
 * - AbstractPublicController: trata requisições públicas, ou seja, o acesso não precisa de autenticação.
 * - AbstractPrivateController: trata requisições privadas, ou seja, é necessário que exista uma sessão de usuário (login).
 * - AbstractWSController: trata requisições aos endpoints de serviços web do Framework e das aplicações desenvolvidas.
 *
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 */
namespace Geral\Controller;

use \Geral\Model\Factory\LogFactory;
use \Geral\Model\FileRepository;
use \Geral\Model\Exception\ErrorException;
use Geral\Model\Factory\AuditFactory;
use Geral\Model\SystemMessages;

abstract class AbstractController
{
    /**
     * Armazena uma referência à variável global $GLOBALS['em'] com a instância do EntityManager (EM) do doctrine.
     * Permite que os sub controllers possam acessar o EM com "$this->em";
     **/
    protected $em;

    /**
     * Armazena uma referência à variável global $GLOBALS['url'] com a instância de \Geral\Model\Url do Framework.
     * Permite que os sub controllers possam acessar esta global com "$this->url";
     **/
    protected $url;

    // Contém  variáveis que serão exportadas para a view invocada pelo controller da aplicação.
    protected $dados = array( );

    // Contém os arquivos CSS que serão incluídos automaticamente no header.php da view invocada pelo controller da aplicação.
    protected $cssFiles = array( );

    // Contém os arquivos JS que serão incluídos automaticamente no header.php da view invocada pelo controller da aplicação.
    protected $jsFiles = array( );

    // Contém os arquivos JS que serão incluídos automaticamente no footer.php da view invocada pelo controller da aplicação.
    protected $jsFilesFooter = array( );

    // Se ajax for usada, a função returnJson imprime JSON
    private $usarAjax = true;

    // Título do controller (módulo)
    public static $title;

    // Última mensagem do sistema
    private static $msg;

    // Tipo da última mensagem
    private static $tipoMsg;

    // Tipos de mensagens aceitas
    private static $tiposMsg = array( 'G' => 'Geral', 'C' => 'Confirmação', 'A' => 'Aviso', 'E' => 'Erro', 'O' => 'Ok' );

    // Serviço de armazenamento de arquivos do framework \Geral\Model\FileRepository.
    private $fileRepository;

    // Se for setada como false, não expando o menu do topo q mostra ícones dos módulos
    public static $expandeMenuTopo = true;

    // Se for setada como false, esconde o menu do topo
    public static $menuTopo = true;

    // Se for setada como false, esconde o menu da esquerda
    public static $menuLateral = true;

    // Por padrão, carrega todas as libs do framework. Caso seja false, carrega somente as libs mínimas para o funcionamento do framework
    public static $carregaLibs = true;

    //=== Parte utilizada para tratamento de respostas do backend. =====================================================

    // Armazena o método utilizado na requisição (POST, GET, etc.).
    private $method;

    // Armazena instância para acessar informações da requisição
    protected $request;

    // Possui o conteúdo a ser enviado como resposta.
    private $response;

    //=== FUNÇÔES =====================================================================================================

    abstract protected function index( ); // Implementação obrigatória para as suclasses.

    // Iniciar as variáveis do controller
    public function __construct( )
    {
        $this -> em = &$GLOBALS['em'];
        $this -> url = &$GLOBALS['url'];
        $this -> method = $_SERVER['REQUEST_METHOD'];
        $this -> request = new \Geral\Model\Request();
        
        // Inicializa o serviço de armazenamento de arquivos do Framework.
        try {
            $this->fileRepository = new FileRepository($this->url->getNameModule());
        } catch(\Exception $e) {
            LogFactory::error($e -> getMessage());
            die(LogFactory::getLastMsg());
        }

        AuditFactory::auditSys();
    }

    public function __destruct( )
    {
        // Recriar instância
        if(!$GLOBALS['em']->isOpen()) {
            $GLOBALS['em'] = $GLOBALS['em']->create(
                $GLOBALS['em']->getConnection(),
                $GLOBALS['em']->getConfiguration()
            );
        } else {
            // Limpar objetos alterados mas sem flushes.
            $GLOBALS['em']->clear();
        }
        
        LogFactory::save($this);
        AuditFactory::save();
    }

    // Inutilizada
    static public function setMsg( $msg, $tipo = NULL, $texto = NULL )
    {
        self::$msg = $msg;
        self::$tipoMsg = $tipo;
    }

    // Inutilizada
    static public function getMsg( )
    {
        return self::$msg;
    }

    // Retorna a instância de FileRepository.
    protected function getFileRepository( )
    {
        return $this->fileRepository;
    }

    // Registra variáveis para serem exportadas para a view.
    protected function addVarView( $key, $var )
    {
        $this -> dados[$key] = $var;
    }

    // Registra arquivos CSS para serem incluídos automaticamente no header
    protected function addCSS( $filename )
    {
        $this -> cssFiles[] = $filename;
    }

    // Registra arquivos JS para serem incluídos automaticamente no header
    protected function addJS( $filename, $loadInFooter = false )
    {
        if ($loadInFooter) {
            $this -> jsFilesFooter[] = $filename;
        } else {
            $this -> jsFiles[] = $filename;
        }
    }
    
    
    /**
     * @fn view
     *
     * Imprime a view correspondente a ação em execução ou outra desejada. Para todas as telas é carregada as views padrão "header.php" e
     * "footer.php".
     *
     * @param String arqView Nome do arquivo da view a ser carregada. Caso seja vazio, será utilizado o nome da ação em execução.
     * @param Boolean $isolado Caso true, é carregado apenas o conteúdo da view desejada, descartando o header e footer.
     * 
     * @return void
     */
    protected function view( $arqView = null, $args = false, $moduleName = '', $controllerName = '' )
    {
        // Se não for informada uma view, seleciona a view com o nome da ação.
        if ( empty( $arqView ) ) {
            $arqView = $this -> url -> getNameAction( );
        }

        /**
         * A view deverá estar no diretório correspondente ao controller da requisição.
         * Ex: /Geral/Admin/index => /modules/Geral/View/Admin/index.php
         **/
        if (!$controllerName) $controllerName = $this->url->getNameController();
        if (!$moduleName) $moduleName = $this->url->getNameModule();

        $arqView = $controllerName . '/' . $arqView . '.php';

        /**
         * Alterna entre as views específicas para desktop ou dispositivos móveis. Pode entrar em desuso futuramente
         * devido à utilização de frameworks CSS responsivos. Exemplo: Bootstrap.
         */
        $dirAgent = DIR_VIEWS_DESKTOP;

        if( \Geral\Model\Validacao::isMobile( $_SERVER["HTTP_USER_AGENT"]) ) {
            $dirBase = DIR_MODULES . '/' . $moduleName . '/' . DIR_VIEWS_MOBILE;

            if(file_exists($dirBase . '/' . $arqView)) {
                $dirAgent = DIR_VIEWS_MOBILE;
            }
        }

        $dirBase = DIR_MODULES . '/' . $moduleName . '/' . $dirAgent;

        $arqView = $dirBase . '/' . $arqView;

        // Extrai as variáveis do array $dados para acesso na view
        if ( !empty( $this -> dados ) ) {
            extract( $this -> dados );
        }

        /** 
         * Verifica as características enviadas em "args" para montar a view
         * Opções: 
         * "HEADER_APP" -> carrega header e footer só da aplicação
         * "ISOLADO"    -> não carrega nenhum header e footer
         * "MINIMO"     -> carrega APENAS as libs mínimas para o framework funcionar
         * "SEM_TOPO"   -> remove o menu do topo da aplicação
         * "SEM_MENU"   -> remove o menu da esquerda da aplicação
         * default      -> carrega header e footer Geral + header e footer da aplicação
         */
        if(is_array($args)){
            if(in_array('HEADER_APP',$args)){
                $views = [$dirBase . '/header.php',$arqView,$dirBase . '/footer.php'];
            }elseif(in_array('ISOLADO',$args)){
                $views = [$arqView];                
            }else{
                $views = [$dirBase . '/header.php',$arqView,$dirBase . '/footer.php'];

                if($moduleName != 'Geral') {
                    array_unshift($views, DIR_MODULES . '/Geral/View/desktop/header.php');
                    $views[] = DIR_MODULES . '/Geral/View/desktop/footer.php';
                }
            }
        }else{
            $views = [$dirBase . '/header.php',$arqView,$dirBase . '/footer.php'];

            if($moduleName != 'Geral') {
                array_unshift($views, DIR_MODULES . '/Geral/View/desktop/header.php');
                $views[] = DIR_MODULES . '/Geral/View/desktop/footer.php';
            }
        }

        // verifica opções: MENU e MINIMO
        $this->menuTopo = (is_array($args) && in_array('SEM_TOPO',$args)) ? false : true;
        $this->menuLateral = (is_array($args) && in_array('SEM_MENU',$args)) ? false : true;
        $this->carregaLibs = (is_array($args) && in_array('MINIMO',$args)) ? false : true;
        $this->expandeMenuTopo = EXPAND_TOP_MENU;

        // Carrega as views.
        if($views){
            foreach($views as $i => $view) {
                if(!file_exists($view)) {
                    continue;
                }

                require($view);
            }
        }
    }

    // Inutilizada
    protected function viewAddOne( $arqView = null ) {
        $this -> view($arqView, true);
    }

    /**
     * Retorna o Array em formato JSON ou Objeto, de acordo com a variável usarAjax.
     * Se $usarAjax for true, então a variável informada é retornada sem qualquer conversão.
     * 
     * @see usarAjaxa
     **/
    protected function returnJson( Array $result )
    {
        if ( $this -> usarAjax ) {
            echo json_encode( $result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
        } else {
            return $result;
        }
    }

    // Ativa/desativa ajax para alterar o comportamento da função returnJson.
    protected function ativarAjax( $opc = true )
    {
        $this -> usarAjax = $opc;
    }

    // Inutilizada
    protected function createDB()
    {
        $tool = \Geral\Model\ToolDoctrine($this -> url -> getNameModule(), $this -> em);

        $tool -> create();

        $classes = array(
          $em->getClassMetadata('Entities\User'),
          $em->getClassMetadata('Entities\Profile')
        );
        $tool->createSchema($classes);
    }

    //=== FUNÇÕES RELACIONADAS À FORMATAÇÃO DE RESPOSTAS DO BACKEND ====================================================

    protected function getMethod()
    {
        return $this -> method;
    }

    // Monta uma mensagem de retorno com sucesso
    protected function setSuccess($msg, $data = [])
    {
        try {
            SystemMessages::addExtraFields($data);
        } catch (\Exception $e) {
            // Atributos reservados, então redirecionar para comunicar um erro
            $this->setError($e->getMessage());
            $this->sendResponse();
        }

        SystemMessages::setResultStatus(SystemMessages::RESULT_SUCCESS_STATUS);
        SystemMessages::setMsg($msg);
    }

    // Monta uma mensagem de retorno com erro
    protected function setError($msg, $data = [])
    {
        try {
            SystemMessages::addExtraFields($data);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
        }

        SystemMessages::setResultStatus(SystemMessages::RESULT_ERROR_STATUS);
        
        new ErrorException($msg);
    }

    /**
     * Envia a mensagem HTTP de resposta para o cliente.
     * 
     * @param String $outputType formato de saída (opções: "json" ou "csv")
     * @param String $opc opções específicas de conversão para o formato de saída
     * <p>
     * <ul>
     * <li>Formato "json" => sem opções específicas</li>
     * <li>Formato "csv" => vide método <code>FormatConverter::toCsv</code></li>
     * </ul>
     * </p>
     * 
     * @throws \Exception se ocorrer algum erro durante a conversão para um formato de saída
     * @see \Geral\Model\FormatConverter::toCsv
     */
    protected function sendResponse($outputFormat = 'json', $opc = null)
    {
        SystemMessages::setOutputFormat($outputFormat);
        SystemMessages::dispatch();
        exit;
    }

    //=== FUNÇÕES RELACIONADAS AO REPOSITÓRIO DE ARQUIVOS DAS APLICAÇÕES ===============================================

     /**
     * Salva os arquivos enviados para upload no diretório temporário (/files/<app_dir>/temp), o qual não pode ser 
     * acessado diretamente via WEB.
     * 
     * É retornado o nome destino do arquivo salvo, que deverá ser usado em etapa posterior para mover o arquivo do
     * diretório temporário para o diretório definitivo (Ex: /files/<app_dir>/downloads).
     * 
     * Esta funcionalidade deverá ser utilizada com requisições Ajax e não com o comportamento padrão de submits de 
     * formulários. O plugin "jquery_file_upload" fornece este tipo de utilização.
     * 
     * @param $_FILES['inFile'] Array com os dados do arquivo para upload. O nome 'inFile' é o valor do atributo "name"
     * do input. Ex: '<input type="file" name="inFile" />'.
     */
    final public function upload()
    {
        try {
            $upload = $this->fileRepository->upload(array_shift($_FILES));
            $this->setSuccess('Upload realizado com sucesso', ['file' => $upload]);
        } catch(\Exception $e) {
            $this->setError('Não foi possível realizar o upload do arquivo', ['detail' => $e->getMessage()]);
        }

        $this->sendResponse();
    }

    /**
     * Esta função possibilita a remoção de arquivos temporários (uploads realizados e não salvos em downloads) da
     * aplicação. O acesso é realizado com a URL /App/Controller/removeUpload/<nome do arquivo>.
     * 
     * Ex: Para excluir o arquivo "file1_123456789.pdf", enviado pelo controller /Template/User, basta utilizar a URL
     * /Template/User/removeUpload/file1_123456789.pdf
     */
    final public function removeUpload( )
    {
        $file = $this->url->getSegment(4);
        
        try {
            $this->getFileRepository()->removeUploadFile($file);
            $this->setSuccess('Arquivo excluído com sucesso.');
            
        } catch(\Exception $e) {
            $this->setError('Não foi possível excluir o arquivo.', ['detail' => $e->getMessage()]);
        }

        $this->sendResponse();
    }

    /**
     * Esta função possibilita que um upload (arquivos do diretório temp) possa ser recuperado. O acesso é realizado com a URL
     * /App/Controller/getUpload/<nome do arquivo temporário>.
     * 
     * Ex: Para baixar o upload "file1_123456789.pdf", enviado pelo controller /Template/User, basta utilizar a URL
     * /Template/User/getUpload/file1_123456789.pdf
     */
    final public function getUpload()
    {
        $fileName = $this->url->getSegment(4);

        try {
            $file = $this->getFileRepository()->getUploadPath($fileName);
        } catch(\Exception $e) {
            http_response_code(404);
            echo 'O arquivo '.$fileName.' não foi encontrado.';
            exit;
        }

        $this->sendFile($file);
    }

    /**
     * Esta função possibilita que um arquivo do diretório diversos possa ser recuperado. 
     * O acesso é realizado com a URL:
     * /App/Controller/getFile/<nome do arquivo>
     * 
     * Ex: Para baixar o upload "file1_123456789.pdf", enviado pelo controller /Template/User, basta utilizar a URL
     * /Template/User/getUpload/file1_123456789.pdf
     */
    final public function getFile()
    {
        $fileName = $this->url->getSegment(4);
        $param2 = $this->url->getSegment(5);
        if ($param2) $fileName .= '/' . $param2;

        try {
            $file = $this->getFileRepository()->getDiversosPath($fileName);
        } catch(\Exception $e) {
            http_response_code(404);
            echo 'O arquivo '.$fileName.' não foi encontrado.';
            exit;
        }

        $this->sendFile($file);
    }

    /**
     * Esta função possibilita a remoção de arquivos salvos definitivamente (downloads) da aplicação. O acesso é
     * realizado com a URL /App/Controller/removeDownload/<nome do arquivo>.
     * 
     * Ex: Para excluir o arquivo "file1_123456789.pdf", enviado pelo controller /Template/User, basta utilizar a URL
     * /Template/User/removeDownload/file1_123456789.pdf
     */
    final public function removeDownload( )
    {
        $file = $this->url->getSegment(4);
        
        try {
            $this->getFileRepository()->removeDownload($file);
            $this->setSuccess('Arquivo excluído com sucesso.');
        } catch(\Exception $e) {
            $this->setError('Não foi possível excluir o arquivo.', ['detail' => $e->getMessage()]);
        }

        $this->sendResponse();
    }

    /**
     * Esta função possibilita o download de um arquivo da aplicação. O acesso é realizado com a URL
     * /App/Controller/download/<nome do arquivo>.
     * 
     * Ex: Para baixar o arquivo "file1_123456789.pdf", enviado pelo controller /Template/User, basta utilizar a URL
     * /Template/User/download/file1_123456789.pdf
     * 
     * Esta função pode ser sobrescrita pela aplicação para acrescentar algum outro tipo de restrição de acesso ao
     * arquivo. Por exemplo, verificar se uma conta de usuário pode ter acesso ao arquivo antes de chamar este função
     * para download.
     */
    final public function download()
    {
        $fileName = $this->url->getSegment(4);
        
        // Pegar o nome do controller que fez o upload do arquivo.
        $controller = $this->getFileRepository()->getNameControllerFromFile($fileName);

        // Verificar se o controller que está requisitando o arquivo foi o mesmo que fez upload.
        if ($controller != $this->url->getNameController()) {
            throw new ErrorException('Arquivo "'.$fileName.'" não encontrado.');
        }

        try {
            $this->isAllowedDownload($fileName);
        } catch(\Exception $e) {
            throw new ErrorException($e->getMessage());
        }
        
        try {
            $file = $this->getFileRepository()->getDownloadPath($fileName);
        } catch(\Exception $e) {
            http_response_code(404);
            echo 'O arquivo '.$fileName.' não encontrado.';
            exit;
        }

        $this->sendFile($file);
    }

    final private function sendFile($file)
    {
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');
        
        $this->getFileRepository()->getContent($file);
    }

    protected function isAllowedDownload(String $fileName)
    {
        // Na implementação da aplicação deve ser utilizado o lançamento de exceção para a verificação da autorização.
        // Exemplo: throw new \Exception('Você não está autorizado a baixar este arquivo.');

        return true;
    }
}