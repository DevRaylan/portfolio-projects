<?php
namespace Geral\Model;

use \Geral\Model\Interfaces\IConn;

class FtpConn implements IConn
{
    private $host;
    private $port;
    private $timeout = 5;
    private $user;
    private $pass;
    private $logs = '== FTP Logs =='."\n";

    public function __construct( $host, $port, $user, $pass )
    {
        if($host == '170.66.50.11' && $_SERVER['AMBIENTE'] != 'PROD') {
            throw new \Exception("Servidor destino bloqueado para este ambiente. Somente válido no servidor de produção.");
        }

        $this -> host = $host;
        $this -> user = $user;
        $this -> pass = $pass;

        $this -> iniCon();
    }

    public function getLogs()
    {
        return $this -> logs;
    }

    public function __destruct()
    {
        if(is_resource($this -> conn)) {
            // close the connection
            ftp_close($this -> conn);
        }
    }

    public function iniCon()
    {
        // Set up a connection
        $this -> logs .= 'Iniciando conexão com o servidor "'.$this -> host.'" ... ';
        $this -> conn = ftp_connect($this -> host, $this -> port, $this -> timeout);

        if(!is_resource($this -> conn)) {
            throw new \Exception("Erro ao conectar com o servidor de FTP.");
        }

        $this -> logs .= ' OK'."\n";
        $this -> logs .= 'Realizando a autenticação ... ';

        // Autenticação
        if (!@ftp_login($this -> conn, $this -> user, $this -> pass)) {
            throw new \Exception('Erro de autenticação com o servidor de FTP.');
        }

        $this -> logs .= ' OK'."\n";

        // Necessário para listagens de arquivos no servidor
        // @link https://stackoverflow.com/questions/8219322/php-ftp-ftp-nlist-not-working-returning-boolean-false
        $this -> logs .= 'Configurando ftp modo passivo ...';
        ftp_pasv($this -> conn, true);
        $this -> logs .= ' OK'."\n";
    }

    public function getFile($fileOrig, $fileDst)
    {
        if (ftp_get($this -> conn, $fileDst, $fileOrig, FTP_BINARY)) {
            return $fileDst;
        } else {
            return null;
        }
    }

    public function getFilesFromDir($dirOrig, $dirDst)
    {
        $files = ftp_nlist($this -> conn, $dirOrig);
        $filesDownloaded = [];
        //$files = ftp_rawlist($this -> conn, $dirOrig, true);
        //var_dump($files);
        //exit;

        if($files === false) {
            throw new \Exception('Diretório não encontrado.');
        }

        foreach($files as $fileOrig) {
            $posSlashes = strrpos($fileOrig, "/");
            $fileDst = $dirDst . '/' . ($posSlashes !== false ? substr($fileOrig, $posSlashes+1) : $fileOrig);

            $this -> getFile($fileOrig, $fileDst);

            $filesDownloaded[] = $fileDst;
        }

        return $filesDownloaded;
    }
}
