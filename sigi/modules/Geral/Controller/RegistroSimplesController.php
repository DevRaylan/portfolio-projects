<?php

namespace Geral\Controller;

use \Geral\Model\Exception\ErrorException;
use \Geral\Model\Factory\RegistroSimplesFactory;
use \Geral\Model\RegistroSimplesBuilder;

class RegistroSimplesController extends AbstractPrivateController
{
    function __construct()
    {
        parent::__construct();

        // Acesso somente a usuários com a transação "admin"
        if (!$this->auth->isAllowedTransactions(['admin'])) {
            throw new ErrorException("Usuário sem permissão de acesso.");
        }
    }

    public function index()
    {
        $this->addVarView('tipo', $this->url->getSegment(4));
        $this->view();
    }

    // =================================================================================================================

    public function getAll()
    {
        $tipo = $this->url->getSegment(4);

        $nomeClasse = RegistroSimplesBuilder::getClassePorTipo($tipo);

        $registros = RegistroSimplesFactory::getAll($nomeClasse, true);

        $this->setSuccess('Registros localizados com sucesso.', ["registros" => $registros]);
        $this->sendResponse();
    }


    public function remove()
    {
        $tipo = $this->url->getSegment(4);
        $id = (int) $this->url->getSegment(5);

        $nomeClasse = get_class(RegistroSimplesBuilder::getEntidadePorTipo($tipo));

        try {
            RegistroSimplesFactory::remove($id, $nomeClasse);

            $this->setSuccess('Registro removido com sucesso.');
        } catch (\Exception $e) {
            throw new ErrorException("Erro ao remover o registro.");
        }

        $this->sendResponse();
    }

    public function update()
    {
        $this->save();
    }

    public function importarDadosDeArquivo()
    {
        $diretorioArquivo = DIR_ROOT . '/' . DIR_NAME_FILES . '/' . $this->url->getNameModule() . '/' . DIR_NAME_TEMP;
        $nomeArquivo = $diretorioArquivo . '/' . $_POST['nomeArquivo'];
        $tipo = $_POST['tipo'];

        if (!file_exists($nomeArquivo)) {
            $this->setError('Arquivo não encontrado.');
            throw new ErrorException("Arquivo não encontrado.");
        } else {

            $arquivo = fopen($nomeArquivo, 'r');
            while (($conteudoLinha = fgetcsv($arquivo)) !== False) {

                $novoRegistro = RegistroSimplesBuilder::getEntidadePorTipo($tipo);
                $novoRegistro->setAtributos($conteudoLinha);

                RegistroSimplesFactory::merge($novoRegistro);
            }
            fclose($arquivo);

            $this->setSuccess('Registros carregados com sucesso.');
        }
        $this->sendResponse();
    }

    public function save()
    {
        $id = $_POST['id'];
        $descricao = $_POST['descricao'];
        $sigla = $_POST['sigla'];
        $tipo = $_POST['tipo'];

        // Monta a lista de atributos para popular o objeto
        $atributos = [$descricao];

        if (!empty($sigla)) {
            $atributos = [$sigla, $descricao];
        }

        $registro = RegistroSimplesBuilder::getEntidadePorTipo($tipo);
        $registro->setId($id);
        $registro->setAtributos($atributos);

        if (!empty($id)) {
            $this->atualizar($registro);
        } else {
            $this->add($registro);
        }

        $this->sendResponse();
    }

    // PRIVATE FUNCTIONS ===================================================================================================

    private function atualizar($registro)
    {
        try {
            RegistroSimplesFactory::update($registro);

            $this->setSuccess('Registro atualizado com sucesso.');
        } catch (\Exception $e) {
            throw new ErrorException("Erro ao atualizar o registro.");
        }
    }

    private function add($registro)
    {
        try {
            RegistroSimplesFactory::add($registro);

            $this->setSuccess('Registro adicionado com sucesso.');
        } catch (\Exception $e) {
            throw new ErrorException("Erro ao atualizar o registro.");
        }
    }
}
