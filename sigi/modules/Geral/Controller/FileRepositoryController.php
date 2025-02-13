<?php
namespace Geral\Controller;

use \Geral\Model\FileRepository;

final class FileRepositoryController extends AbstractPrivateController
{
    // Armazena o nome da aplicação para gerenciamento do repositório de arquivos.
    private $application;

    public function __construct( )
    {
        parent::__construct( );
    }

    // Mostrar todos os downloads realizados por todas as aplicações.
    public function index( )
    {
        $this->addVarView('downloadsByApp', $this->getFileRepository()->getAllDownloads(true));
        $this->view();
    }

    /**
     * Esta função fornece o download de arquivos (sobrescreve a função download Geral), independente da aplicação.
     * Útil para poder ter acesso a arquivos de aplicações que possam ter sido renomeados e a URL de download na
     * aplicação passa a ser inválida.
     */
    public function downloadAll()
    {
        $fileName = $this->url->getSegment(4);
        
        try {
            $file = $this->getFileRepository()->searchDownloadPath($fileName);
        } catch(\Exception $e) {
            http_response_code(404);
            exit;
        }
        
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
}
