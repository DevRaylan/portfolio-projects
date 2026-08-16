<?php
/**
 * @class FileRepository
 *
 * Esta classe faz o gerenciamento de todos os arquivos do framework.
 *
 * @version 2.0
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 */

namespace Geral\Model;

use \Geral\Model\File;
use \Geral\Model\Factory\DownloadFactory;
use \Geral\Model\UserSession;

final class FileRepository
{
    // Extensões aceitas
    const EXTENSIONS = ['pdf', 'docx', 'doc', 'xls', 'xlsx', 'odt', 'odf', 'ods', 'jpg', 'png', 'csv', 'txt', 'json'];

    // Aplicação à qual os arquivos deverão ser gerenciados.
    private $app;

    /**
     * O construct irá inicializar a classe com os valores correspondente à aplicação desejada. Também serão criados os
     * diretório correspondentes, caso ainda não existam.
     **/
    public function __construct($app) {
        if(empty($app)) {
            throw new \Exception("Aplicação não existente.");
        }

        // Diretório dos arquivos da aplicação
        $dirAppFiles = DIR_FILES . '/' . $app;

        if(!is_dir($dirAppFiles)) {
            mkdir($dirAppFiles);
        }

        // Diretórios esperados pelo repositório.
        $directories = [ DIR_NAME_TEMP, DIR_NAME_DOWNLOADS, DIR_NAME_DIVERSOS, DIR_NAME_LOG ];

        foreach($directories as $directory) {
            if(!is_dir($dirAppFiles . '/' . $directory)) {
                mkdir($dirAppFiles . '/' . $directory);
            }
        }

        $this->app = $app;
    }

    //== UPLOADS =======================================================================================================

    /**
     * Salva arquivos enviados para upload no diretório temp privado, o qual não pode ser acessado diretamente via WEB.
     * 
     * É retornado o nome destino do arquivo salvo temporariamente, que deverá ser usado em etapa posterior para mover o arquivo do
     * diretório temporário para o diretório definitivo (Ex: /files/<app>/downloads).
     */
    public function upload(Array $upload)
    {
        $file = new File($upload, true, $this->app);

        if ( !$file->hasExtension( SELF::EXTENSIONS ) ) {
            throw new \Exception("Tipo de arquivo não permitido.");
        } else {
            // Novo nome do arquivo para persistência definitiva.
            $fileDst = DIR_FILES.'/'.$this->app.'/'.DIR_NAME_TEMP . '/' . $file->getNewName();
            
            if ( move_uploaded_file( $file->getTempName(), $fileDst ) ) {
                return ['fullName' => $fileDst, 'name' => $file->getNewName()];
            } else {
                throw new \Exception("Não foi possível mover o arquivo para o diretório temporário privado.");
            }
        }
    }

    /**
     * Remove um arquivo temporário.
     * 
     * @return Void
     */
    public function removeUploadFile($fileName)
    {
        if(empty($fileName)) {
            throw new \InvalidArgumentException('Nome do arquivo vazio.');
        }

        // Pegar o caminho completo do arquivo temporário (/files/<app>/temp/arquivo).
        $file = $this->getUploadPath($fileName);
        @unlink( $file );
    }

    /**
     * Retorna o caminho absoluto do arquivo temporário.
     * 
     * @param $fileName Nome do arquivo temporário.
     * 
     * @return String Caminho absoluto do arquivo.
     */
    public function getUploadPath($fileName)
    {
        $file = DIR_FILES.'/'.$this->app.'/'.DIR_NAME_TEMP . '/' . $fileName; 

        if ( !file_exists( $file ) ) {
            throw new \Exception('Arquivo não encontrado nos diretórios de uploads (public e private)');
        }

        return $file;
    }

    //== DOWNLOADS =====================================================================================================

    /**
     * Remove arquivos do diretório de download
     */
    public function removeDownload($fileName)
    {
        if(empty($fileName)) {
            throw new \InvalidArgumentException('Parâmetros com valores ausentes.');
        }

        $download = DownloadFactory::getByName($fileName);

        if(empty($download)) {
            throw new \Exception("O arquivo $fileName não foi encontrado.");
        }
        
        if(!$download->isFromApp($this->app)) {
            throw new \Exception("O arquivo não pertence à aplicação solicitante.");
        }
        
        try {
            $file = $this->getDownloadPath($fileName);
        } catch(\Exception $e) {

        }

        if(!file_exists($file) || @unlink( $file )) {
            DownloadFactory::remove($fileName);
        }
    }

    /**
     * Retorna o caminho absoluto do arquivo definitivo (download). O arquivo deve estar cadastrado na base de downloads
     * do Framework.
     * 
     * @param $fileName Nome do arquivo de download.
     * 
     * @return String Caminho definitivo do arquivo.
     */
    public function getDownloadPath($fileName)
    {
        if(empty($fileName)) {
            throw new \Exception('O nome do arquivo não foi enviado.');
        }

        $file = DIR_FILES.'/'.$this->app.'/'.DIR_NAME_DOWNLOADS . '/' . $fileName; 

        if ( !file_exists( $file ) ) {
            throw new \Exception('Arquivo não encontrado nos diretórios de uploads (public e private)');
        }

        return $file;
    }

    /**
     * Procurar um arquivo e retornar o seu caminho no sistema de arquivos. Neste caso não é levada em consideração a
     * aplicação de onde esta função está sendo chamada.
     * Apenas pode ser invocada pela aplicação Geral.
     */
    public function searchDownloadPath($fileName)
    {
        // Função utilizável apenas dentro da aplicação Geral (A requisição será <url>/Geral/...).
        if($this->app != 'Geral') {
            throw new \Exception('Esta função só pode ser utilizada pela aplicação "/modules/Geral".');
        }

        if(empty($fileName)) {
            throw new \Exception('O nome do arquivo não foi enviado.');
        }

        // Procurar o arquivo na base de downloads.
        try {
            $file = DownloadFactory::getByName($fileName);
        } catch(\Exception $e) {
            throw $e;
        }

        $filePath = DIR_FILES . '/' . $file->getApplication() . '/' . DIR_NAME_DOWNLOADS . '/' .  $fileName; 

        if ( !file_exists( $filePath ) ) {
            throw new \Exception('Arquivo não encontrado nos diretórios de uploads (public e private)');
        }

        return $filePath;
    }

    /**
     * Retornar os downloads categorizados por aplicação.
     * Estrutura [ <appName> => [ downloads ] ]
     * 
     * @param $toArray Converter o resultado em Array. Por padrão o retorno é um Array de objetos Downloads.
     * @return Array Downloads
     * @see getDownloadsByApp()
     */

    public function getAllDownloads($toArray = false)
    {
        // Função utilizável apenas dentro da aplicação Geral (A requisição será <url>/Geral/...).
        if($this->app != 'Geral') {
            throw new \Exception('Esta função só pode ser utilizada pela aplicação "/modules/Geral".');
        }

        $downloads = [];

        foreach(Config::getAppNames() as $appName) {
            $downloads[$appName] = $this->getDownloadsByApp($appName, $toArray);
        }

        return $downloads;
    }

    /**
     * Retornar os downloads de uma aplicação específica
     * 
     * @param $appName Nome da aplicação para filtrar os downloads. Se este parâmetro for vazio, então será utilizado o 
     * nome da aplicação de onde esta função está sendo chamada (Aplicação do contexto).
     * @param $toArray Converter o resultado em Array. Por padrão o retorno é um Array de objetos Downloads.
     * @return Array Downloads
     */
    public function getDownloadsByApp($appName = null, $toArray = false)
    {
        // Função utilizável apenas dentro da aplicação Geral (A requisição será <url>/Geral/...).
        if($this->app != 'Geral') {
            throw new \Exception('Esta função só pode ser utilizada pela aplicação "/modules/Geral".');
        }
        
        if(empty($appName)) $appName = $this->app;

        return DownloadFactory::getByApp($appName, $toArray);
    }

    /**
     * Retornar os downloads de uma aplicação específica. Esta função é chamada no contexto da aplicação.
     * 
     * @param $toArray Converter o resultado em Array. Por padrão o retorno é um Array de objetos Downloads.
     * @return Array Downloads
     */
    public function getDownloads($toArray = false)
    {
        return DownloadFactory::getByApp($this->app, $toArray);
    }

    // === DIVERSOS ====================================================================================================

    /**
     * Retorna o caminho absoluto do arquivo definitivo na pasta diversos. 
     * 
     * @param $fileName Nome do arquivo de download.
     * 
     * @return String Caminho definitivo do arquivo.
     */
    public function getDiversosPath($fileName)
    {
        if(empty($fileName)) {
            throw new \Exception('O nome do arquivo não foi enviado.');
        }

        $file = DIR_FILES.'/'.$this->app.'/'.DIR_NAME_DIVERSOS . '/' . $fileName; 

        if ( !file_exists( $file ) ) {
            throw new \Exception('Arquivo não encontrado.');
        }

        return $file;
    }

    /**
     * Move os arquivos salvos em diretórios temporário para o respectivo diretório de download da aplicação. O 
     * diretório de destinos pode ser privado ou público.
     * 
     * @param $uploads Array de nomes de arquivos para serem movidos.
     * @param $module Aplicação de destino para o arquivo. Se for nulo, então será utilizada a aplicação referente à URL
     * sendo acessada.
     * @param $access Tipo de acesso ao arquivo (Público ou Privado), se privado, o arquivo pode apenas ser lido se o
     * usuário estiver logado no sistema.
     * 
     * @return Array com os nomes dos arquivos movidos com sucesso.
     */

    public function moveUploadToDownload($fileName, $module = null, $access = 'public')
    {
        if(empty($module)) $module = $GLOBALS['url'] -> getNameModule();

        $fileOrig = $this->getUploadPath($fileName);

        if(!file_exists($fileOrig)) {
            throw new \Exception('O arquivo "'.$fileName.'" não existe no diretório temp (uploads).');
        }

        // Está sendo utilizado um diretório único de downloads não visível diretamente na web. Ex: acesso via URL direto.
        $dirDownloads = DIR_FILES.'/'.$module.'/'.DIR_NAME_DOWNLOADS;

        if(!is_dir($dirDownloads)) {
            mkdir($dirDownloads);
        }

        if(!rename($fileOrig, $dirDownloads . '/' .$fileName)) {
            throw new \Exception('Não foi possível mover o arquivo "'.$fileName.'" para o diretório de downloads.');
        }

        // Salvar em Banco de Dados
        // Verificar a opção de salvar e em seguida atualizar o status para 'salvo' caso o arquivo tenha sido movido
        // para o diretório de downloads com sucesso.
        try {
            DownloadFactory::add($fileName, $GLOBALS['url']->getNameModule(), $GLOBALS['url']->getUrlAction(), $access, UserSession::getParam('cpf'));
        } catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new \Exception('O arquivo com nome "'.$fileName.'" já existe no Banco de Dados.');
        } catch(\Exception $e) {
            throw new \Exception("Erro interno.") ;
        }
    }

    public function fileExists($fileName)
    {
        return DownloadFactory::fileExists($fileName);
    }

    public function getContent($file)
    {
        return readfile($file);
    }

    /**
     * Retorna o nome do controller que fez o upload e salvou o arquivo.
     * 
     * @param $fileName Nome do arquivo.
     * @return String Nome do controller.
     */
    public function getNameControllerFromFile($fileName)
    {
        if(empty($fileName)) {
            throw new \Exception('O nome do arquivo não foi fornecido.');
        }

        $download = DownloadFactory::getByName($fileName);

        if(empty($download)) {
            throw new \Exception('O arquivo "'.$fileName.'" não foi encontrado no banco de downloads.');
        }

        // 1 => App, 2 => controller, 3 => action
        $url = explode('/', $download->getUrl());

        return $url[2];
    }
}
