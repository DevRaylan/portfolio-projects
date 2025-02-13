<?php

/**
 * Arquivo que contém a classe AdminController
 * @file AdminController.php
 */

/**
 * @class AdminController
 *
 * Esta classe possui os métodos e atributos relacionados a administração do framework
 *
 * @version 1.0
 * @author Jean Carlos Oliveira de Abreu
 <jean.abreu@udesc.br>
 */

namespace Geral\Controller;

use \Geral\Model\Factory\LogFactory;
use \Geral\Model\Config;

class AdminController extends AbstractPrivateController
{
    private $toolDoctrine;
    const DIR_ENTITY = 'Entity';

    public function __construct()
    {
        parent::__construct();
        $this->toolDoctrine = new \Geral\Model\ToolDoctrine($this->url->getNameModule(), $this->em);

        if (!$this->auth->isAllowedTransactions(['admin'])) {
            LogFactory::error('Usuário sem permissão para acessar este módulo.');
            die(LogFactory::getLastMsg());
        }
    }

    public function index()
    {
        $this->view();
    }

    public function viewLogs()
    {
        $this->view();
    }

    public function findLogsByType()
    {
        $type = $this->url->getSegment(4);
        $limit = $this->url->getSegment(5);
        if ($this->url->getSegment(6) && $this->url->getSegment(7) && $this->url->getSegment(8)) {
            $dataCriacao = $this->url->getSegment(8) .'-'. $this->url->getSegment(7) .'-'. $this->url->getSegment(6);
        } else {
            $dataCriacao = null;
        }

        $logs = LogFactory::findByTypeAndDate($type, $dataCriacao, $limit, true);
        $this->setSuccess('Registros localizados com sucesso.', ["logs" => $logs]);
        $this->sendResponse();
    }

    // Tela para listar os arquivos criados pelos módulos e o framework
    public function listDirectoryFiles()
    {
        $dirs = $this->getDirs();

        $this->addVarView('dirs', $dirs);
        $this->view();
    }

    // Função utilizada via AJAX para listar arquivos de um diretório
    public function getFileList()
    {
        $dirs = $this->getDirs();
        $dir = realpath(DIR_SYS . '/../' . $_GET['dir']);

        if (!$this->isAcceptedDir($dir)) {
            $return = [
                'result' => 'error',
                'msg' => 'O diretório "' . $_GET['dir'] . '" não pode ter seus arquivos listados.'
            ];

            $this->returnJson($return);
            exit;
        }

        // Pegar os arquivos
        $files = scandir($dir);

        // Remover "." e ".."
        array_shift($files);
        array_shift($files);

        // Inverter os valores para keys
        $files = array_flip($files);

        // Inserir um boolean com a verificação se o arquivo é diretório
        foreach ($files as $file => &$isDir) {
            $isDir = is_dir($dir . '/' . $file);
        }

        $result = [
            'result' => 'success',
            'files' => $files
        ];

        $this->returnJson($result);
    }

    /**
     * Permite realizar download de arquivos criados pelo sistema, dispensando
     * o acesso via FTP para baixar arquivos.
     */
    public function downloadFile()
    {
        $file = DIR_SYS . '/../' . $_GET['file'];
        $file = realpath($file);

        if (empty($file)) {
            $result = [
                'result' => 'error',
                'msg' => 'Arquivo inexistente.'
            ];

            $this->returnJson($result);
            exit;
        }

        $dir = pathinfo($file,  PATHINFO_DIRNAME);

        if (!$this->isAcceptedDir($dir)) {
            $result = [
                'result' => 'error',
                'msg' => 'Download do arquivo "' . $file . '" não permitido.'
            ];

            $this->returnJson($result);
            exit;
        }

        // Configuramos os headers que serão enviados para o browser
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="' . pathinfo($file, PATHINFO_BASENAME) . '"');
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');

        echo file_get_contents($file);
    }

    public function adminEntities()
    {
        // Pegar as entidades e seus respectivos SQLs DROP
        $entitiesBySchema = $this->toolDoctrine->getClassesSync('remove');

        // Entidades por app.
        $entitiesByModule = [];

        // Separar as entidades por app.
        foreach ($entitiesBySchema as &$entities) {
            foreach ($entities as $entity => &$sqls) {
                $module = explode('\\', $entity, 3);

                // O nome do app está na posição 1.
                $entitiesByModule[$module[1]][$entity] = $sqls;
            }

            unset($sqls);
        }

        try {
            $this->toolDoctrine->checkConflictTableNames();
        } catch (\Exception $e) {
            $conflictedEntities = $e->getMessage();
        }

        $this->addVarView('conflictedEntities', $conflictedEntities);
        $this->addVarView('entitiesByModule', $entitiesByModule);
        $this->view();
    }

    /**
     * Listar registros da tabela de uma entidade.
     *
     * @param String POST entity Caminho da entidade no framework. Ex: /Geral/Entity/Download
     *
     * @return JSON
     */
    public function getEntityRegisters()
    {
        $entity = str_replace('/', '\\', $_GET['entity']);
        $registers = $this->toolDoctrine->getEntityRegisters($entity);

        if (!empty($registers)) {
            foreach ($registers as &$register) {
                foreach ($register as $campo => &$valor) {
                    // Arquivo BLOB
                    if (is_resource($valor)) {
                        // No front, substituir {mimetype} para o tipo correto.
                        $valor = 'data:application/octet-stream;base64,' . base64_encode(stream_get_contents($valor));
                    }
                }

                unset($valor);
            }
        }

        $this->setSuccess('', ['registers' => $registers]);
        $this->sendResponse();
    }

    /**
     * Remover tabelas das entidades selecionadas do Banco de dados.
     * Faz um DROP, ou seja, todos os dados serão perdidos.
     */
    public function removeTable()
    {
        $entities = is_array($_POST['entities']) ? $_POST['entities'] : [];

        foreach ($entities as &$entity) {
            $entity = str_replace('/', '\\', $entity);
        }

        try {
            if ($this->toolDoctrine->drop($entities)) {
                $result = ['result' => 'success', 'msg' => 'Tabela(s) recriada(s) com sucesso no BD.'];
            } else {
                echo $this->toolDoctrine->getMsg();
                $result = ['result' => 'success', 'msg' => 'Erro ao recriar tabela(s) das classes.'];
            }
        } catch (\Exception $e) {
            $result = ['result' => 'error', 'msg' => $e->getMessage()];
        }

        $this->returnJson($result);
    }

    public function systemLibs()
    {
        $this->view();
    }

    /**
     * Esta função retorna a lista de arquivos de um diretório.
     */
    private function getDirs()
    {
        $dirsPermissions = [];
        $dirsPermissions[] = '/' . DIR_NAME_FILES;

        return $dirsPermissions;
    }

    /**
     * Esta função verifica se o diretório é aceito para a listagem de arquivos.
     */
    private function isAcceptedDir($dir)
    {
        $dirs = $this->getDirs();
        $dir = realpath($dir);

        // Se o diretório não existe, a função realpath retorna "false"
        if (empty($dir)) {
            $return = [
                'result' => 'error',
                'msg' => 'Diretório "' . $_GET['dir'] . '" não encontrado.'
            ];

            $this->returnJson($return);
            exit;
        }

        // Variável Utilizada para verificar se o diretório passado está dentro
        // dos diretório de escrita pelo framework
        $isAcceptedDir = false;

        foreach ($dirs as &$dirBase) {
            $dirBase = realpath(DIR_SYS . '/../' . $dirBase);

            /**
             * Verificar se o caminho do diretório está contido nos diretórios
             * de escrita do sistema e se existe.
             */
            if (strpos($dir, $dirBase) === 0 && is_dir($dir)) {
                $isAcceptedDir = true;
                break;
            }
        }

        return $isAcceptedDir;
    }

    public function listMailMessages()
    {
        $mailMessagesByApp = Config::getAppNames();
        $mailMessagesByApp = array_flip($mailMessagesByApp);

        foreach ($mailMessagesByApp as $appName => &$messages) {
            $messages = \Geral\Model\Factory\MailMessageFactory::getByApp($appName);
        }

        $this->addVarView('mailMessages', $mailMessagesByApp);
        $this->view();
    }

    public function listErrors()
    {
        $this->addVarView('tipo', 'erros');
        $this->view();
    }
}
