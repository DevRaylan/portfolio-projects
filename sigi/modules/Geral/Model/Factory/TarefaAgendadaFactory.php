<?php

namespace Geral\Model\Factory;

use \Geral\Entity\TarefaAgendada;

/**
 * Factory para gerenciamento de tarefas agendadas
 * 
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 */
abstract class TarefaAgendadaFactory
{

    /**
     * Retorna a tarefa agendada pelo seu Id
     */
    public static function getById($id, $toArray = false){
        $query = $GLOBALS['em']->getRepository('\Geral\Entity\TarefaAgendada')
            ->createQueryBuilder('ta')
            ->where('ta.id = ?1')
            ->setParameter(1, $id)
            ->getQuery();
        return $toArray ? $query->getArrayResult() : $query->getOneOrNullResult();
    }

    /**
     * Retorna todas as tarefas agendadas
     */
    public static function getAll($toArray = false){
        $query = $GLOBALS['em']->getRepository('\Geral\Entity\TarefaAgendada')
            ->createQueryBuilder('ta')
            ->getQuery();
        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    /**
     * Retorna todas as tarefas agendadas para agendamento no CRON
     */
    public static function getAllSchedule(){
        $query = $GLOBALS['em']->getRepository('\Geral\Entity\TarefaAgendada')
            ->createQueryBuilder('ta')
            ->where('ta.situacao = ?1')
            ->setParameter(1, true)
            ->getQuery();
        return $query->getResult();
    }

    /**
     * Retorna todas as tarefas agendadas de um App
     */
    public static function getAllByApp(String $app, $toArray = false){
        $query = $GLOBALS['em']->getRepository('\Geral\Entity\TarefaAgendada')
            ->createQueryBuilder('ta')
            ->where('ta.app = ?1')
            ->setParameter(1, $app)
            ->getQuery();
        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    /**
     * Retorna todas as tarefas agendadas de um App
     */
    public static function getByAcessData($app, $controller, $action, $token){
        $query = $GLOBALS['em']->getRepository('\Geral\Entity\TarefaAgendada')
            ->createQueryBuilder('ta')
            ->where('ta.app = ?1')->setParameter(1, $app)
            ->andWhere('ta.controller = ?2')->setParameter(2, $controller)
            ->andWhere('ta.action = ?3')->setParameter(3, $action)
            ->andWhere('ta.token = ?4')->setParameter(4, $token)
            ->andWhere('ta.situacao = ?5')->setParameter(5, true)
            ->getQuery();
        return $query->getOneOrNullResult();
    }

    /**
     * Realiza inclusão de um registro
     */
    public static function add($dados){
        //Cria uma nova entidade
        $tarefaAgendada = new TarefaAgendada();
        //Carrega os dados repassados
        static::loadDataToEntity($tarefaAgendada, $dados);
        //Gera o Token
        $tarefaAgendada->generateToken();
        //Salva
        $GLOBALS['em']->persist($tarefaAgendada);
        $GLOBALS['em']->flush();
    }

    /**
     * Realiza alteração de um registro
     */
    public static function update($id, $dados)
    {
        //Busca entidade pelo Id
        $tarefaAgendada = self::getById($id);
        if(is_null($tarefaAgendada)){
            return false;
        }
        //Carrega os dados repassados
        static::loadDataToEntity($tarefaAgendada, $dados);
        //Salva
        $GLOBALS['em']->persist($tarefaAgendada);
        $GLOBALS['em']->flush();
        return true;
    }

    /**
     * Altera o status da tarefa
     */
    public static function changeStatus($id){
        //Busca entidade pelo Id
        $tarefaAgendada = self::getById($id);
        if(is_null($tarefaAgendada)){
            return false;
        }
        //Carrega os dados repassados
        $tarefaAgendada->setSituacao(!$tarefaAgendada->getSituacao());
        //Salva
        $GLOBALS['em']->persist($tarefaAgendada);
        $GLOBALS['em']->flush();
        return $tarefaAgendada;

    }

    /**
     * Remove uma Tarefa Agendada pelo Id
     */
    public static function remove($id){
        //Busca entidade pelo Id
        $tarefaAgendada = self::getById($id);
        //Remove
        $GLOBALS['em']->remove($tarefaAgendada);
        $GLOBALS['em']->flush();
    }

    /**
     * Carrega os dados para a entidade
     * @param \Geral\Entity\TarefaAgendada $entity
     * @param array $data
     */
    protected static function loadDataToEntity(&$entity, $data){
        foreach ($data as $propName => $value) {
            $method       = 'set' . ucfirst(strtolower($propName));
            if (method_exists($entity, $method)) {
                call_user_func_array([$entity, $method], [$value]);
            }
        }
    }

    /**
     * Executa procedimento que atualiza as tarefas na agenda do Sistema Operacional
     * @return string
     */
    public static function refreshSchedule(){
        //Dados para atualização
        $url   = 'https://jenkins.dev.udesc.br/job/generateCron/buildWithParameters';
        $token = 'sysacc-jenkins-api:11b3b7ba147972e9be5bc1afeec0c84f7a';
        $data  = http_build_query([
            'ambiente' => strtolower($_SERVER['AMBIENTE']),
            'key'      => DEPLOY_KEY,
            'domain'   => $_SERVER['SERVER_NAME']
        ]);
        //Processa a requisição
        $curl  = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERPWD, $token);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //Executa
        curl_exec($curl);
        curl_close($curl);
    }
}