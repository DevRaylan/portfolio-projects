<?php
namespace Geral\Model;

use \Geral\Model\Interfaces\IConn;

class LocalTempConn implements IConn
{
    private $logs = '== FTP Logs =='."\n";

    public function __construct( $host, $port, $user, $pass ) { }

    public function getLogs()
    {
        return $this -> logs;
    }

    public function __destruct() {}

    public function iniCon()
    {
        // Set up a connection
        $this -> logs .= 'Iniciando conexão com o servidor "'.$this -> host.'" ... ';
        $this -> logs .= ' OK'."\n";
        $this -> logs .= 'Realizando a autenticação ... ';
        $this -> logs .= ' OK'."\n";
    }

    public function getFile($fileOrig, $fileDst)
    {
        if (file_exists($fileOrig) && rename($fileOrig, $fileDst)) {
            return $fileDst;
        } else {
            return null;
        }
    }

    public function getFilesFromDir($dirOrig, $dirDst)
    {
        $dirOrig = DIR_FILES . '/' . $GLOBALS['url']->getNameModule() . '/' . DIR_NAME_TEMP.'/'.$dirOrig;
        $files = scandir($dirOrig);
        array_shift($files); // .
        array_shift($files); // ..
        $filesDownloaded = [];
        
        if(!is_dir($dirOrig)) {
            throw new \Exception('Diretório não encontrado.');
        }

        foreach($files as $fileOrig) {
            if(strpos($fileOrig, 'README.md') !== false) continue;
            
            $fileOrig = $dirOrig.'/'.$fileOrig;
            $posSlashes = strrpos($fileOrig, "/");
            $fileDst = $dirDst . '/' . ($posSlashes !== false ? substr($fileOrig, $posSlashes+1) : $fileOrig);

            $this -> getFile($fileOrig, $fileDst);

            $filesDownloaded[] = $fileDst;
        }

        return $filesDownloaded;
    }
}
