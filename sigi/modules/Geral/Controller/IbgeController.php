<?php

namespace Geral\Controller;

use ErrorException;
use Geral\Model\Factory\LocalidadeIbgeFactory;
use Geral\Model\Factory\LogFactory;

class IbgeController extends AbstractServiceController
{

    function __construct()
    {
        parent::__construct();
        $this->ibgeClient = null;
    }

    public function index()
    {
        $this->view();
    }
    
    public function paises()
    {
        $this->view();
    }
    
    public function estados()
    {
        $this->view();
    }
    
    public function municipios()
    {
        $this->addVarView('estadoId', $this->url->getSegment(4));
        $this->view();
    }
    
    public function getAllPaises()
    {
        if (!$this->auth->isAllowedTransactions(['admin'])) {
            LogFactory::error('Usuário sem permissão para acessar este módulo.');
            die(LogFactory::getLastMsg());
        }

        try {
            $paises = LocalidadeIbgeFactory::getAllPaises(true);
            $this->setSuccess('Consulta realizada com sucesso.', ['paises' => $paises]);
        } catch (\Exception $e) {
            $this->setError('Erro na consulta.');
        }

        $this->sendResponse();
    }

    public function getAllEstados()
    {
        if (!$this->auth->isAllowedTransactions(['admin'])) {
            LogFactory::error('Usuário sem permissão para acessar este módulo.');
            die(LogFactory::getLastMsg());
        }

        try {
            $pais_br = LocalidadeIbgeFactory::getPaisPorNome(LocalidadeIbgeFactory::PAIS_IBGE);
            try {
                $estados = LocalidadeIbgeFactory::getAllEstados(true);
                $this->setSuccess('Consulta realizada com sucesso.', ['estados' => $estados]);
            } catch (\Exception $e) {
                $this->setError('Erro na consulta.');
            }
        } catch (\Exception $e) {
            $this->setError('País '.LocalidadeIbgeFactory::PAIS_IBGE.' não localizado.');
        }

        $this->sendResponse();
    }

    public function getEstadoPorId()
    {
        if (!$this->auth->isAllowedTransactions(['admin'])) {
            LogFactory::error('Usuário sem permissão para acessar este módulo.');
            die(LogFactory::getLastMsg());
        }

        $idEstado = $this->url->getSegment(4);

        try {
            $estado = LocalidadeIbgeFactory::getEstadoPorId($idEstado, true);

            if (!empty($estado)) {
                $this->setSuccess('Consulta realizada com sucesso.', ['estado' => $estado]);
            } else {
                $this->setError('Estado não localizado, verificar atualização de dados no IBGE.');
            }
        } catch (\Exception $e) {
            $this->setError('Erro na consulta.');
        }

        $this->sendResponse();
    }

    public function getEstadoPorSigla()
    {
        if (!$this->auth->isAllowedTransactions(['admin'])) {
            LogFactory::error('Usuário sem permissão para acessar este módulo.');
            die(LogFactory::getLastMsg());
        }

        $sigla = $this->url->getSegment(4);

        try {
            $estado = LocalidadeIbgeFactory::getEstadoPorSigla($sigla, true);

            if (!empty($estado)) {
                $this->setSuccess('Consulta realizada com sucesso.', ['estado' => $estado]);
            } else {
                $this->setError('Estado não localizado, verificar atualização de dados no IBGE.');
            }
        } catch (\Exception $e) {
            $this->setError('Erro na consulta.');
        }

        $this->sendResponse();
    }

    public function getMunicipiosPorEstado()
    {
        if (!$this->auth->isAllowedTransactions(['admin'])) {
            throw new ErrorException('Usuário sem permissão para acessar este módulo.');
        }

        $idEstado = $this->url->getSegment(4);

        try {
            $municipios = LocalidadeIbgeFactory::findMunicipiosPorEstado($idEstado, true);
            if (!empty($municipios)) {
                $this->setSuccess('Consulta realizada com sucesso.', ['municipios' => $municipios]);
            } else {
                $this->setError('Municipios não localizados, verificar atualização de dados no IBGE.');
            }
        } catch (\Exception $e) {
            $this->setError('Erro na consulta.');
        }

        $this->sendResponse();
    }

    public function getMunicipioPorId()
    {
        if (!$this->auth->isAllowedTransactions(['admin'])) {
            throw new ErrorException('Usuário sem permissão para acessar este módulo.');
        }

        $idMunicipio = $this->url->getSegment(4);

        try {
            $municipio = LocalidadeIbgeFactory::getMunicipioPorId($idMunicipio, true);
            if (!empty($municipio)) {
                $this->setSuccess('Consulta realizada com sucesso.', ['municipio' => $municipio]);
            } else {
                $this->setError('Municipio não localizado, verificar atualização de dados no IBGE.');
            }
        } catch (\Exception $e) {
            $this->setError('Erro na consulta.');
        }

        $this->sendResponse();
    }

    public function getEnderecoPorCep(){
        if (!$this->auth->isAllowedTransactions(['admin'])) {
            throw new ErrorException('Usuário sem permissão para acessar este módulo.');
        }

        $cep = $this->url->getSegment(4);

        try {
            $endereco = LocalidadeIbgeFactory::getEnderecoPorCep($cep, true);
            if (!empty($endereco)) {
                $this->setSuccess('Consulta realizada com sucesso.', ['endereco' => $endereco]);
            } else {
                $this->setError('CEP não localizado.');
            }
        } catch (\Exception $e) {
            $this->setError('Erro na consulta.');
        }

        $this->sendResponse();
    }

    /**
     * Arquivo deve seguir a estrutura JSON abaixo e estar localizado em: files/Geral/temp/localidades.json
     * 
     * {
     *   "id": "1",
     *   "st": "A",
     *   "sigla": "AF",
     *   "nome": "Afeganist\u00e3o",
     *   "er_prefixo_telefone": "",
     *   "er_telefone": "",
     *   "tp_registro": "pais"
     * }
     * 
     */
    public function importarPaises()
    {

        if (!$this->auth->isAllowedTransactions(['admin'])) {
            throw new ErrorException('Usuário sem permissão para acessar este módulo.');
        }

        $diretorioArquivo = DIR_ROOT . '/' . DIR_NAME_FILES . '/' .
            $this->url->getNameModule() . '/' . DIR_NAME_TEMP;
        //$nomeArquivo = $diretorioArquivo . '/localidades.json';
        $nomeArquivo = $diretorioArquivo . '/' . $_POST['nomeArquivo'];

        if (!file_exists($nomeArquivo)) {
            throw new ErrorException('Arquivo não localizado: ' . $nomeArquivo);
        }

        LocalidadeIbgeFactory::addPaisesArquivoJson($nomeArquivo);

        $this->setSuccess('Registros carregados com sucesso.');
        $this->sendResponse();
    }

    public function getPaisPorNome()
    {
        $nomePais = $this->url->getSegment(4);
        
        try {
            $pais = LocalidadeIbgeFactory::getPaisPorNome($nomePais, true);

            if (!empty($pais)) {
                $this->setSuccess('Consulta realizada com sucesso.', ['pais' => $pais]);
            } else {
                $this->setError('Pais não localizado: ' . $nomePais);
            }
        } catch (\Exception $e) {
            $this->setError('Erro na consulta.');
        }

        $this->sendResponse();
    }

}
