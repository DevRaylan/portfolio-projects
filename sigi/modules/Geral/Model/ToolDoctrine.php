<?php
/**
 * @class ToolDoctrine
 *
 * Esta classe possui os métodos e atributos para comunicação com doctrine
 *
 * @version 1.0
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 */

namespace Geral\Model;

use Doctrine\ORM\EntityManager;
use Geral\Model\Exception\ErrorException;

class ToolDoctrine
{
    private $tool;
    private $em;
    private $msg;

    function __construct( $module, &$em )
    {
        $this -> module = $module;
        $this -> em = $em;
        $this -> tool = new \Doctrine\ORM\Tools\SchemaTool($em);
    }

    public function getMsg()
    {
        return $this -> msg;
    }

    private function &getMetadataOfClasses(&$classes)
    {
        $metadataOfClasses = [];

        foreach($classes as &$classe) {
            $metadataOfClasses[] = $this -> em -> getClassMetadata($classe);
        }

        return $metadataOfClasses;
    }

    function create(Array &$classes)
    {
        $metadataOfClasses = $this -> getMetadataOfClasses($classes);

        if(empty($metadataOfClasses[0])) {
            die('Metadados da classe não existe.');
        }

        $em = $this->getEntityManagerFromEntity($metadataOfClasses[0]);
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        try {
            $tool->createSchema($metadataOfClasses);
            return true;
        } catch (\Exception $e) {
            $this -> msg = $e -> getMessage();
            return false;
        }
    }

    function update(Array &$entities = null)
    {
        $modules = Config::getAllEntities();
        $ems = [];
        $metadataOfClassesBySchema = [];

        foreach($modules as $module => $entities) {
            foreach($entities as $i => $entity) {
                try {
                    $entity = str_replace('/', '\\', $entity);
                    
                    // Pegar os metadados de todas as classes entidades da aplicação.
                    $metadataOfClasse = $this->em->getClassMetadata($entity);
                    
                    // Pegar o valor do atributo "schema" em @Table da classe para instanciar o respectivo EntityManager.
                    $schema = $metadataOfClasse->table['schema'] ? $metadataOfClasse->table['schema'] : BD_NAME;

                    if (!isset($ems[$schema])) {
                        $ems[$schema] = $this->getEntityManagerFromEntity($metadataOfClasse);
                    }

                    $metadataOfClassesBySchema[$schema][] = $metadataOfClasse;
                } catch(\Exception $e) {
                    new ErrorException('Erro ao recuperar dados de sincronização da entidades: '.$e->getMessage());
                }
            }
        }

        foreach($metadataOfClassesBySchema as $schema => $metadataOfClasses) {
            $tool = new \Doctrine\ORM\Tools\SchemaTool($ems[$schema]);

            try {
                $tool->updateSchema($metadataOfClasses, false); // true não deleta classes que não estejam no diretório Entity
            } catch (\Exception $e) {
                $this -> msg = $e -> getMessage();
                var_dump($this -> msg);exit;
                return false;
            }
        }

        return true;
    }

    function drop(Array &$classes)
    {
        $metadataOfClasses = $this -> getMetadataOfClasses($classes);

        if(empty($metadataOfClasses[0])) {
            die('Metadados da classe não existe.');
        }

        $em = $this->getEntityManagerFromEntity($metadataOfClasses[0]);
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        try {
            $tool->dropSchema($metadataOfClasses);
            return true;
        } catch (\Exception $e) {
            $this -> msg = $e -> getMessage();
            return false;
        }
    }

    function getClassesSync($type = 'update')
    {
        $modules = Config::getAllEntities();
        $ems = [];
        $metadataOfClassesBySchema = [];

        foreach($modules as $module => $entities) {
            foreach($entities as $i => $entity) {
                try {
                    $entity = str_replace('/', '\\', $entity);
                    
                    // Pegar os metadados de todas as classes entidades da aplicação.
                    $metadataOfClasse = $this->em->getClassMetadata($entity);
                    
                    // Pegar o valor do atributo "schema" em @Table da classe para instanciar o respectivo EntityManager.
                    $schema = $metadataOfClasse->table['schema'] ? $metadataOfClasse->table['schema'] : BD_NAME;

                    if (!isset($ems[$schema])) {
                        $ems[$schema] = $this->getEntityManagerFromEntity($metadataOfClasse);
                    }

                    // Se create, então o retorno vai ser por schema e entidade. Utilizado em /Geral/Admin/adminEntities.
                    if(in_array($type, ['create', 'remove'])) {
                        $metadataOfClassesBySchema[$schema][$entity][] = $metadataOfClasse;
                    } else {
                        // Retorna por schema, utilizado no Update do Framework.
                        $metadataOfClassesBySchema[$schema][] = $metadataOfClasse;
                    }
                } catch(\Exception $e) {
                    new ErrorException('Erro ao recuperar dados de sincronização da entidades: '.$e->getMessage());
                }
            }
        }

        $entitiesSync = [];

        foreach($metadataOfClassesBySchema as $schema => $metadataOfClasses) {
            $tool = new \Doctrine\ORM\Tools\SchemaTool($ems[$schema]);

            // Se create, então pega a SQL individual de cada entidade. Assim, uma única entidade pode ser recriada.
            // Aqui entra apenas CREATE, se não existe ou ALTER para as chaves estrangeiras.
            // Sempre é considerado que a entidade ainda não existe no banco de dados.
            if($type == "create") {
                foreach($metadataOfClasses as $entity => $metadataOfClasse) {
                    $sqls = $tool->getCreateSchemaSql($metadataOfClasse, false);

                    if(empty($sqls)) continue;
                    
                    $entitiesSync[$schema][$entity] = $sqls;
                }
            } elseif($type == "remove") {
                foreach($metadataOfClasses as $entity => $metadataOfClasse) {
                    // Ignorar classes que utilizam a tag @MappedSuperclass
                    if($metadataOfClasse[0]->isMappedSuperclass) {
                        $sqls = ['isNotEntity'];
                    } else {
                        $sqls = $tool->getDropSchemaSql($metadataOfClasse, false);
                    }
                    
                    $entitiesSync[$schema][$entity] = $sqls;
                }
            } else {
                // Pega todas as SQLs de atualização para sincronizar as entidades.
                // Aqui entra CREATE, ALTER e DROP
                // Sempre verifica se a entidade já existe no banco para gerar SQLS de atualização.
                $sqls = $tool->getUpdateSchemaSql($metadataOfClasses, true);

                if(empty($sqls)) continue;
                
                $entitiesSync[$schema] = $sqls;
            }
        }

        return $entitiesSync;
    }

    function getEntityRegisters($entity)
    {
        try {
            $metadataOfClasse = $this -> em -> getClassMetadata($entity);
            //$metadataOfClasse->setAssociationOverride('department', ['fetch' => 'EAGER']);
            
            $query = $this->em->getRepository($entity)->createQueryBuilder('u');
            $assocs = $metadataOfClasse->getAssociationMappings();

            foreach($assocs as $assoc => $dados) {
                $query->leftJoin('u.'.$assoc, $assoc)
                      ->addSelect($assoc);
            }

            $query = $query->getQuery();

            return $query->getArrayResult();
        } catch(\Exception $e) {
            return null;
        }
    }

    /**
     * Retornar um EntityManager baseado no valor do atributo "schema" nas anotações doctrine da classe.
     */
    private function getEntityManagerFromEntity(&$metadataOfClasse)
    {
        $conn = $GLOBALS['conn'];
        $dbName = $metadataOfClasse->getMetadataValue('table');

        // Verificar se existem conflitos entre entidades com relação ao nome da tabela e schema.
        $this->checkConflictTableNames();

        /**
         * Criar um novo EntityManager, mas conectado com o banco de dados de mesmo schema definido na classe. O restante
         * da configuração permanece igual.
         */
        if(!empty($dbName['schema'])) {
            $conn['dbname'] = $dbName['schema'];
            $em = EntityManager::create($conn, $GLOBALS['config']);
            $platform = $em->getConnection()->getDatabasePlatform();
            $platform->registerDoctrineTypeMapping('enum', 'string');

            return $em;
        }

        // EntityManager geral do framework.
        return $this->em;
    }

    /**
     * Verifica se existem conflitos entre entidades do framework e aplicações com relação
     * aos nomes de suas respectivas tabelas e schemas no banco de dados.
     */
    public function checkConflictTableNames()
    {
        $modules = Config::getAllEntities();
        $tableNamesBySchema = [];
        $hasConflict = false;

        // Pegar o nome da tabela e schema de cada entidade.
        foreach($modules as &$entities) {
            foreach($entities as &$entity) {
                $entity = str_replace('/', '\\', $entity);
        
                // Metadados da entidade.
                $entityMetadata = $this->em->getClassMetadata($entity);

                // Ignorar classes que utilizam a tag @MappedSuperclass
                if($entityMetadata->isMappedSuperclass) continue;
                
                // Schema da entidade do framework.
                $schema = $entityMetadata->table['schema'] ? $entityMetadata->table['schema'] : BD_NAME;
                $schema = strtolower($schema);
                $tableName = strtolower($entityMetadata->table['name']);

                // Ops, já tem entidade utilizando este nome de tabela.
                if(isset($tableNamesBySchema[$schema][$tableName])) {
                    $hasConflict = true;
                }

                $tableNamesBySchema[$schema][$tableName][] = $entity;
            }
        }

        if($hasConflict) {
            $output = '';

            // Montar mensagem sobre o erro.
            foreach($tableNamesBySchema as $schema => &$tableNames) {
                foreach($tableNames as $tableName => &$entities) {
                    if(count($entities) > 1) {
                        $output .= '- Schema: '.$schema."\n- Nome da tabela: ".$tableName."\n--- ".implode("\n--- ", $entities)."\n\n";
                    }
                }
            }

            throw new ErrorException('Há conflito de nome de tabela em um mesmo schema nas seguintes entidades: '."\n"
                .$output);
        }
    }
}
