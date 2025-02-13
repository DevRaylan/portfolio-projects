<?php
/**
 * @class File
 *
 * Esta classe possui os métodos e atributos comuns a manipulação de nomes de
 * arquivos
 *
 * @version 2.0
 * @author Jean Carlos Oliveira de Abreu <jean.abreu@udesc.br>
 */

namespace Geral\Model;
 
class File
{
    private $name;
    private $extension;
    private $fullName;
    
    // Armazena as informações do arquivo de upload
    private $infoUpload;

    static $letrasAcentuadas = 'ÁÍÓÚÉÄÏÖÜËÀÌÒÙÈÃÕÂÎÔÛÊáíóúéäïöüëàìòùèãõâîôûêÇç';

    function __construct( $fileName, $isUpload = false, $appSuffix = null )
    {
        /**
         * Se o arquivo for de upload, então estará sendo passado o conteúdo de $_FILES['inFile'], que possui os atributos
         * name, type, tmp_name, error e size. Neste caso, estas informações são salvas em $this->infoUpload e a variável
         * $fileName é reescrita com o valor name do upload.
         **/
        if($isUpload) {
            $this->infoUpload = $fileName;
            $fileName = $fileName['name'];
        }

        if(empty($fileName)) {
            throw new \Exception("Nome do arquivo não fornecido.");
        }

        // Pegar as informações sobre o nome do arquivo (diretório, extensão, etc...).
        $info = pathinfo( $fileName );
        $this->name = $info['filename']; // Nome do arquivo, sem extensão.
        $this->extension = strtolower($info['extension']); // Extensão, sem o ponto.
        $this->fullName = $info['basename']; // Nome do arquivo com extensão.
        $this->dir = $info['dirname']; // Diretório do arquivo
        $this->path = $info['dirname'].'/'.$this->name; // Diretório completo com o nome do arquivo.

        // Se for upload, é um arquivo ainda não cadastrado no framework. Logo cria um novo nome para persistência.
        if($isUpload) {
            $this->infoUpload['new_name'] = $this->createNewName($appSuffix);
        }
    }

    // GETS ====================================================================

    public function getName()
    {
        return $this->name;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function getNewName()
    {
        return isset($this->infoUpload['new_name']) ? $this->infoUpload['new_name'] : NULL;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * O nome temporário existe apenas de o arquivo for de upload.
     */
    public function getTempName()
    {
        return isset($this->infoUpload['tmp_name']) ? $this->infoUpload['tmp_name'] : NULL;
    }

    // LOGIC ===================================================================
    
    /**
     * Substitui caracteres não aceitos por underlines no nome do arquivo.
     * Caracteres aceitos: de aA até zZ e 0 a 9.
     * 
     * @return String Nome do arquivo pós filtro
     **/
    private static function filterName($fileName)
    {
        return preg_replace( "/[^a-z0-9_]/ui", '_', $fileName );
    }

    /**
     * Criar um novo nome para o arquivo utilizando o nome da aplicação como sufixo.
     * O novo nome é criado no formato: <nome original após filtro>_<appsufixo>_<microtime>.<extensão>
     * 
     * @return String Novo nome
     **/
    private function createNewName($appSuffix = '')
    {
        $fileName = self::filterName($this->getName());

        if(!empty($appSuffix)) {
            $fileName .= '_'.$appSuffix;
        }

        $fileName .= '_' . str_replace( array( '.', ' ' ), '', microtime( true ) );
        $fileName .= '.' . $this->getExtension();

        return $fileName;
    }

    /**
     * @fn hasExtension
     *
     * Verifica se a extensão do arquivo está contida nas extensões permitidas
     *
     * @param Array extensions Extensões permitidas para verificação
     * @return  Boolean
     **/
    function hasExtension( Array $extensions )
    {
        return in_array( self::getExtension(), $extensions );
    }
}
?>