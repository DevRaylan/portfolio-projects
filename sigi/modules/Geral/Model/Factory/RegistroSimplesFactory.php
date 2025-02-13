<?php

namespace Geral\Model\Factory;

abstract class RegistroSimplesFactory
{
    public function __construct()
    {
    }

    static public function getAll($nomeClasse, $toArray = false)
    {

        $query = $GLOBALS['em']
            ->getRepository($nomeClasse)
            ->createQueryBuilder('u')
            ->getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    static public function findByKeyword($nomeClasse, $keywords = null, $colOrder = null, $toArray = false)
    {

        $qb = $GLOBALS['em']->createQueryBuilder();

        $qb->select('u')
            ->from($nomeClasse, 'u');

        // Inclui filtro por palavra chave na descricao
        if (!empty($keywords)) {
            // separa as palavras para buscar no texto todo independendo da posição.
            $paramWords = "";
            foreach (explode(" ", $keywords) as $word) {
                $paramWords = $paramWords . "%" . $word;
            }
            $paramWords = $paramWords . "%";

            // Inclui parametro na consulta
            $qb->andwhere($qb->expr()->andX(
                $qb->expr()->like('u.descricao', "?1")
            ))
                ->setParameter(1, "%" . $paramWords . "%");
        }


        if (!empty($colOrder)) {
            $qb->orderBy('u.' . $colOrder, 'ASC');
        }
        $query = $qb->getQuery();

        return $toArray ? $query->getArrayResult() : $query->getResult();
    }

    static public function getById($id, $nomeClasse, $toArray = false)
    {

        $query = $GLOBALS['em']
            ->getRepository($nomeClasse)
            ->createQueryBuilder('u')
            ->where('u.id = ?1')
            ->setParameter(1, $id)
            ->getQuery();

        try {
            $objResult = $toArray ? $query->getArrayResult() : $query->getSingleResult();
        } catch (\Exception $e) {
            $objResult = null;
        }

        return $objResult;
    }

    static public function getByDescricao($desc, $nomeClasse, $toArray = false)
    {

        $query = $GLOBALS['em']
            ->getRepository($nomeClasse)
            ->createQueryBuilder('u')
            ->where('u.descricao = ?1')
            ->setParameter(1, $desc)
            ->getQuery();

        try {
            $objResult = $toArray ? $query->getArrayResult() : $query->getSingleResult();
        } catch (\Exception $e) {
            $objResult = null;
        }

        return $objResult;
    }

    static public function add($registro)
    {
        $GLOBALS['em']->persist($registro);
        $GLOBALS['em']->flush();

        return true;
    }

    static public function update($registro)
    {
        $id = $registro->getId();
        $nomeClasse = get_class($registro);

        $objResult = self::getById($id, $nomeClasse);

        if (empty($objResult)) {
            throw new \Exception('Registro não encontrado.');
        }

        try {
            $objResult->setAtributos($registro->getAtributos());

            $GLOBALS['em']->flush();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Função utilizada no upload de arquivo para atualizar os registros
     * existentes a partir da DESCRICAO e incluir os novos.
     */
    static public function merge($registro)
    {
        $desc = $registro->getDescricao();
        $nomeClasse = get_class($registro);

        $objResult = self::getByDescricao($desc, $nomeClasse);

        try {
            if (empty($objResult)) {
                // Adiciona novo registro
                self::add($registro);
            } else {
                // Atualiza registro existente
                $objResult->setAtributos($registro->getAtributos());
                self::update($objResult);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    static public function remove($id, $nomeClasse)
    {
        $registro = self::getById($id, $nomeClasse);

        try {
            $GLOBALS['em']->remove($registro);
            $GLOBALS['em']->flush();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
