<?php
namespace Geral\Model;

use Geral\Model\Exception\ErrorException;

class UdevUpdate
{
    // Retorna as SQLs para sincronização das entidades com o banco de dados.
    public function getSqlToSync()
    {
        $toolDoctrine = new \Geral\Model\ToolDoctrine('Geral', $GLOBALS['em']);
        $sqlsToSyncBySchema = $toolDoctrine->getClassesSync();
        
        return $sqlsToSyncBySchema;
    }

    // Retorna as entidades que possuem conflito de nomes de tabelas no banco de dados.
    public function getConflictTableNames()
    {
        $toolDoctrine = new \Geral\Model\ToolDoctrine('Geral', $GLOBALS['em']);

        return $toolDoctrine->checkConflictTableNames();
    }

    // Faz a sincronização das entidades com o banco de dados.
    public function syncEntitiesWithDb()
    {
        $toolDoctrine = new \Geral\Model\ToolDoctrine('Geral', $GLOBALS['em']);
        
        if($toolDoctrine->update()) {
            //$sqls = $this->sqlsArrayToString($entitiesByModule);
            //$fup = FrameworkUpdateFactory::add($this->getVersionFrom(), $this->getVersionTo(), $sqls, UserSession::getParam('cpf'));
            //FrameworkUpdateFactory::setStatus($fup->getId(), EntityFrameworkUpdate::STATUS_OK);
        }
        else {
            throw new ErrorException('Erro ao atualizar base de dados.');
        }
    }

    public function isSynchronized()
    {
        return !$this->getSqlToSync() ? true : false;
    }
}
