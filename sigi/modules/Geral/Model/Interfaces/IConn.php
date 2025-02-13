<?php
namespace Geral\Model\Interfaces;

interface IConn
{
    public function getLogs();
    public function iniCon();
    public function getFile($fileOrig, $fileDst);
    public function getFilesFromDir($dirOrig, $dirDst);
}
