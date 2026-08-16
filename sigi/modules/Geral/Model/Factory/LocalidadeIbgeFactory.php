<?php

namespace Geral\Model\Factory;

use ErrorException;
use Geral\Entity\Estado;
use Geral\Entity\Municipio;
use Geral\Entity\Pais;
use Geral\Model\Services\CepClient;
use Geral\Model\Services\IbgeClient;
use phpDocumentor\Reflection\Types\Array_;

/**
 * Factory responsavel por consultar e atualizar o cache de Municipio e Estado do IBGE
 */
abstract class LocalidadeIbgeFactory
{

    const PAIS_IBGE = 'Brasil';

    public function __construct()
    {
    }

    static public function getAllPaises($toArray = false)
    {
        $qb = $GLOBALS['em']->createQueryBuilder();

        $qb->select('p')
            ->from('\Geral\Entity\Pais', 'p');
        $qb->orderBy('p.descricao');
        $query = $qb->getQuery();

        $localidades = $toArray ? $query->getArrayResult() : $query->getResult();

        return $localidades;
    }

    static public function getAllEstados($toArray = false)
    {
        $qb = $GLOBALS['em']->createQueryBuilder();

        $qb->select('e')
            ->from('\Geral\Entity\Estado', 'e')
            ->orderBy('e.nome', 'ASC');

        $query = $qb->getQuery();


        $localidades = $toArray ? $query->getArrayResult() : $query->getResult();

        if (empty($localidades)) {
            self::importaEstados();
            $localidades = $toArray ? $query->getArrayResult() : $query->getResult();
        }

        return $localidades;
    }

    static public function findMunicipiosPorEstado($idEstado, $toArray = false)
    {
        $qb = $GLOBALS['em']->createQueryBuilder();

        $qb->select('m')
            ->from('\Geral\Entity\Municipio', 'm')
            ->leftJoin('m.estado', 'e')
            ->addSelect('e')
            ->where('e.id = ?1')
            ->setParameter(1, $idEstado)
            ->orderBy('m.nome', 'ASC');
        $query = $qb->getQuery();

        $localidades = $toArray ? $query->getArrayResult() : $query->getResult();

        if (empty($localidades)) {
            self::atualizaMunicipiosPorEstado($idEstado);
            $localidades = $toArray ? $query->getArrayResult() : $query->getResult();
        }

        return $localidades;
    }

    static public function getEstadoPorId($id, $toArray = false)
    {

        $query = $GLOBALS['em']
            ->getRepository('\Geral\Entity\Estado')
            ->createQueryBuilder('u')
            ->where("u.id = ?1")
            ->setParameter(1, $id)
            ->getQuery();

        $localidade = $toArray ? $query->getArrayResult() : $query->getSingleResult();

        return $localidade;
    }

    static public function getEstadoPorSigla($sigla, $toArray = false)
    {

        $query = $GLOBALS['em']
            ->getRepository('\Geral\Entity\Estado')
            ->createQueryBuilder('u')
            ->where("u.sigla = ?1")
            ->setParameter(1, $sigla)
            ->getQuery();

        $localidade = $toArray ? $query->getArrayResult() : $query->getSingleResult();

        return $localidade;
    }

    static public function getMunicipioPorId($id, $toArray = false)
    {
        $query = $GLOBALS['em']
            ->getRepository('\Geral\Entity\Municipio')
            ->createQueryBuilder('u')
            ->leftJoin('u.estado', 'e')
            ->addSelect('e')
            ->where("u.id = ?1")
            ->setParameter(1, $id)
            ->getQuery();

        $localidade = $toArray ? $query->getArrayResult() : $query->getSingleResult();

        return $localidade;
    }

    static public function getEnderecoPorCep($cep)
    {
        // Consulta API Busca CEP
        $cepJson = CepClient::getEnderecoPorCep($cep);

        if (empty($cepJson)) {
            throw new ErrorException("CEP não localizado na API de consulta");
        }

        // ViaCep
        $idMunicipio = $cepJson["ibge"];
        if (empty($idMunicipio)) {
            //Postmon
            $idMunicipio = $cepJson["cidade_info"]['codigo_ibge'];
        }

        $endereco['cep'] = $cepJson["cep"];
        $endereco['logradouro'] = $cepJson["logradouro"];
        $endereco['bairro'] = $cepJson["bairro"];
        $endereco['municipio'] = self::getMunicipioPorId($idMunicipio, true);

        return $endereco;
    }

    static public function addPaisesArquivoJson($nomeArquivo)
    {
        $jsonFile = file_get_contents($nomeArquivo);
        $jsonStr = json_decode($jsonFile, true);

        foreach ($jsonStr as $jsonPais) {

            if ($jsonPais['tp_registro'] != "pais") {
                continue;
            }

            $pais = new Pais();
            $pais->setAtributos($jsonPais);

            // salva na tabela
            $GLOBALS['em']->persist($pais);
            $GLOBALS['em']->flush();
        }
    }

    static public function getPaisPorNome($nomePais, $toArray  = false)
    {

        $query = $GLOBALS['em']
            ->getRepository('\Geral\Entity\Pais')
            ->createQueryBuilder('u')
            ->where("u.descricao = ?1")
            ->setParameter(1, $nomePais)
            ->getQuery();

        $localidade = $toArray ? $query->getArrayResult() : $query->getSingleResult();

        return $localidade;
    }

    static private function importaEstados()
    {
        // Consulta API IBGE
        $estadosJson = IbgeClient::getEstados();

        foreach ($estadosJson as $estJson) {
            // Seta pais default
            $estJson['pais'] = self::getPaisPorNome(self::PAIS_IBGE);

            // Monta objeto estado
            $estado = new Estado();
            $estado->setAtributos($estJson);

            // salva na tabela local
            $GLOBALS['em']->persist($estado);
            $GLOBALS['em']->flush();
        }
    }

    static private function atualizaMunicipiosPorEstado($idEstado)
    {
        // Consulta API IBGE
        $municipiosJson = IbgeClient::getMunicipiosPorEstado($idEstado);

        // Consulta todos os municipios do estado em questão
        $municipiosBD = $GLOBALS['em']->getRepository('\Geral\Entity\Municipio')
            ->createQueryBuilder('m')
            ->leftJoin('m.estado', 'e')
            ->where('e.id = ?1')
            ->setParameter(1, $idEstado)
            ->select('m')
            ->getQuery()->getResult();


        // Desativa todos os municipios deste estado
        $GLOBALS['em']->createQueryBuilder()
            ->update('\Geral\Entity\Municipio', 'm')
            ->set('m.status', '?1')
            ->where('m in (?2)')
            ->setParameter(1, 'I')
            ->setParameter(2, $municipiosBD)
            ->getQuery()
            ->execute();
        $GLOBALS['em']->flush();

        // Atualiza os municipios existentes e adiciona os novos
        foreach ($municipiosJson as $municJson) {

            $municipioExistente = null;

            foreach ($municipiosBD as $municBD) {
                if ($municJson['id'] == $municBD->getId()) {
                    $municipioExistente = $municBD;
                    break;
                }
            }

            // Atualiza dados
            if (!empty($municipioExistente)) {
                $municipioExistente->setAtributos($municJson);
            } else {
                // Adiciona novo
                $municipio = new Municipio();
                $municipio->setAtributos($municJson);

                // salva na tabela cache
                $GLOBALS['em']->persist($municipio);
            }

            $GLOBALS['em']->flush();
        }
    }
}
