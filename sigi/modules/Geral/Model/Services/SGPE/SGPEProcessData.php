<?php

namespace Geral\Model\Services\SGPE;

use DateTime;
use Geral\Model\Date;
use JsonSerializable;

/**
 * Classe com dados do processo
 * @author Glauco David Laicht <glauco.laicht@udesc.br>
 */
class SGPEProcessData implements JsonSerializable{

    private $numeroFormatado;
    private $siglaOrgao;
    private $nomeOrgao;
    private $numero;
    private $ano;
    private $numeroProcessoExterno;
    private $numeroVolume;
    private $dataAutuacao;
    private $nomeMunicipio;
    private $siglaUf;
    private $nomeAutuador;
    private $codigoClassificacao;
    private $codigoAssunto;
    private $descricaoAssunto;
    private $descricaoComplemento;
    private $nomeSetorAbertura;
    private $nomeSetorAtual;
    private $siglaSetorAtual;
    private $siglaOrgaoAtual;
    private $nomeOrgaoAtual;
    
    /**
     * @var array
     */
    private $interessado;

    /**
     * Retorna o número do processo formatado
     * @return string
     */ 
    public function getNumeroFormatado(){
        return $this->numeroFormatado;
    }

    /**
     * Define o número do processo formatado
     * @param string $numeroFormatado
     */ 
    public function setNumeroFormatado($numeroFormatado){
        $this->numeroFormatado = $numeroFormatado;
    }

    /**
     * Retorna a sigla do órgão
     * @return string
     */ 
    public function getSiglaOrgao(){
        return $this->siglaOrgao;
    }

    /**
     * Define a sigla órgão
     * @param string $siglaOrgao
     */ 
    public function setSiglaOrgao($siglaOrgao){
        $this->siglaOrgao = $siglaOrgao;
    }

    /**
     * Retorna o nome do órgão
     * @return string
     */ 
    public function getNomeOrgao(){
        return $this->nomeOrgao;
    }

    /**
     * Define o nome do órgão
     * @param string $nomeOrgao
     */ 
    public function setNomeOrgao($nomeOrgao){
        $this->nomeOrgao = $nomeOrgao;
    }

    /**
     * Retorna o número do processo
     * @return int
     */ 
    public function getNumero(){
        return $this->numero;
    }

    /**
     * Define o número do processo
     * @param int $numero
     */ 
    public function setNumero($numero){
        $this->numero = $numero;
    }

    /**
     * Retorna o ano do processo
     * @return int
     */ 
    public function getAno(){
        return $this->ano;
    }

    /**
     * Define o ano do processo
     * @param int $ano
     */ 
    public function setAno($ano){
        $this->ano = $ano;
    }

    /**
     * Retorna o número externo do processo 
     * @return string
     */ 
    public function getNumeroProcessoExterno(){
        return $this->numeroProcessoExterno;
    }

    /**
     * Define o número externo do processo
     * @param string $numeroProcessoExterno
     */ 
    public function setNumeroProcessoExterno($numeroProcessoExterno){
        $this->numeroProcessoExterno = $numeroProcessoExterno;
    }

    /**
     * Retorna o número do volume
     * @return int
     */ 
    public function getNumeroVolume(){
        return $this->numeroVolume;
    }

    /**
     * Define o número do volume
     * @param int $numeroVolume
     */ 
    public function setNumeroVolume($numeroVolume){
        $this->numeroVolume = $numeroVolume;
    }

    /**
     * Define a data de autuação
     * @return string
     */ 
    public function getDataAutuacao($format=false){
        return $format ? Date::convDatetimeToString($this->dataAutuacao, Date::DATAHORA_FORMAT) : $this->dataAutuacao;
    }

    /**
     * Define a data de autuação
     * @param string $dataAutuacao
     */ 
    public function setDataAutuacao($dataAutuacao){
        if(is_string($dataAutuacao)){
            $this->dataAutuacao = DateTime::createFromFormat(DateTime::W3C, $dataAutuacao);
        }
        else{
            $this->dataAutuacao = $dataAutuacao;
        }
    }

    /**
     * Retorna o nome do município
     * @return string
     */ 
    public function getNomeMunicipio(){
        return $this->nomeMunicipio;
    }

    /**
     * Define o nome do município
     * @param string $nomeMunicipio
     */ 
    public function setNomeMunicipio($nomeMunicipio){
        $this->nomeMunicipio = $nomeMunicipio;
    }    

    /**
     * Retorna a sigla da UF
     * @return string
     */ 
    public function getSiglaUf(){
        return $this->siglaUf;
    }

    /**
     * Define a sigla da UF
     * @param string $siglaUf
     */ 
    public function setSiglaUf($siglaUf){
        $this->siglaUf = $siglaUf;
    }

    /**
     * Retorna o nome do autuador
     * @return string
     */ 
    public function getNomeAutuador(){
        return $this->nomeAutuador;
    }

    /**
     * Define o nome do autuador
     * @param string $nomeAutuador
     */ 
    public function setNomeAutuador($nomeAutuador){
        $this->nomeAutuador = $nomeAutuador;
    }

    /**
     * Retorna  o código de classificação
     * @return string
     */ 
    public function getCodigoClassificacao(){
        return $this->codigoClassificacao;
    }

    /**
     * Define o código de classificação
     * @param string $codigoClassificacao
     */ 
    public function setCodigoClassificacao($codigoClassificacao){
        $this->codigoClassificacao = $codigoClassificacao;
    }    

    /**
     * Retorna o código do assunto
     * @return int
     */ 
    public function getCodigoAssunto(){
        return $this->codigoAssunto;
    }

    /**
     * Define o código do assunto
     * @param int $codigoAssunto
     */ 
    public function setCodigoAssunto($codigoAssunto){
        $this->codigoAssunto = $codigoAssunto;
    }    

    /**
     * Retorna a descriação do assunto
     * @return string
     */ 
    public function getDescricaoAssunto(){
        return $this->descricaoAssunto;
    }

    /**
     * Define a descrição do assunto
     * @param string $descricaoAssunto
     */ 
    public function setDescricaoAssunto($descricaoAssunto){
        $this->descricaoAssunto = $descricaoAssunto;
    }

    /**
     * Retorna a descrição do complemento
     * @return string
     */ 
    public function getDescricaoComplemento(){
        return $this->descricaoComplemento;
    }

    /**
     * Define o valor da descriação do complemento
     * @param string $descricaoComplemento
     */ 
    public function setDescricaoComplemento($descricaoComplemento){
        $this->descricaoComplemento = $descricaoComplemento;
    }

    /**
     * Retorna o nome do setor de abertura
     * @return string
     */ 
    public function getNomeSetorAbertura(){
        return $this->nomeSetorAbertura;
    }

    /**
     * Define o nome do setor de abertura
     * @param string $nomeSetorAbertura
     */ 
    public function setNomeSetorAbertura($nomeSetorAbertura){
        $this->nomeSetorAbertura = $nomeSetorAbertura;
    }

    /**
     * Retorna o nome do setor atual
     * @return string
     */ 
    public function getNomeSetorAtual(){
        return $this->nomeSetorAtual;
    }

    /**
     * Define o nome do setor atual
     * @param string $nomeSetorAtual
     */ 
    public function setNomeSetorAtual($nomeSetorAtual){
        $this->nomeSetorAtual = $nomeSetorAtual;
    }

    /**
     * Retorna a sigla do setor atual
     * @return string
     */ 
    public function getSiglaSetorAtual(){
        return $this->siglaSetorAtual;
    }

    /**
     * Define a sigla do setor atual
     * @param string $siglaSetorAtual
     */ 
    public function setSiglaSetorAtual($siglaSetorAtual){
        $this->siglaSetorAtual = $siglaSetorAtual;
    }

    /**
     * Retorna a sigla do orgão atual
     * @return string
     */ 
    public function getSiglaOrgaoAtual(){
        return $this->siglaOrgaoAtual;
    }

    /**
     * Define a sigla do orgão atual
     * @param string $siglaOrgaoAtual
     */ 
    public function setSiglaOrgaoAtual($siglaOrgaoAtual){
        $this->siglaOrgaoAtual = $siglaOrgaoAtual;
    }
    
    /**
     * Define o nome do orgão atual
     * @return string
     */ 
    public function getNomeOrgaoAtual(){
        return $this->nomeOrgaoAtual;
    }

    /**
     * Define o nome do orgão atual
     * @param string $nomeOrgaoAtual
     */ 
    public function setNomeOrgaoAtual($nomeOrgaoAtual){
        $this->nomeOrgaoAtual = $nomeOrgaoAtual;
    }    

    /**
     * Retorna o objeto relativo ao interessado
     * @return array
     */ 
    public function getInteressado(){
        return $this->interessado;
    }

    /**
     * Define o objeto do interessado
     * @param array $interessado
     */ 
    public function setInteressado(array $interessado){
        $this->interessado = $interessado;
    }

    /**
     * Retorna o nome do interessado
     * @return string
     */
    public function getInteressadoNome(){
        return $this->getInteressadoValue('nome');
    }

    /**
     * Retorna o nome do interessado
     * @return string
     */
    public function getInteressadoTipo(){
        return $this->getInteressadoValue('tipo');
    }

    /**
     * Retorna o e-mail do interessado
     * @return string
     */
    public function getInteressadoEmail(){
        return $this->getInteressadoValue('email');
    }

    /**
     * Retorna o bairro do endereço do interessado
     * @return string
     */
    public function getInteressadoEnderecoBairro(){
        return $this->getInteressadoValue('bairro');
    }

    /**
     * Retorna o logradouro do endereço do interessado
     * @return string
     */
    public function getInteressadoEnderecoLogradouro(){
        return $this->getInteressadoValue('logradouro');
    }

    /**
     * Retorna o código do munícipio do endereço do interessado
     * @return string
     */
    public function getInteressadoEnderecoCodigoMunicipio(){
        return $this->getInteressadoValue('codigoMunicipio');
    }    

    /**
     * Retorna o munícipio do endereço do interessado
     * @return string
     */
    public function getInteressadoEnderecoMunicipio(){
        return $this->getInteressadoValue('municipio');
    }

    /**
     * Retorna o CEP do endereço do interessado
     * @return string
     */
    public function getInteressadoEnderecoCep(){
        return $this->getInteressadoValue('cep');
    }

    /**
     * Retorna o número do endereço do interessado
     * @return string
     */
    public function getInteressadoEnderecoNumero(){
        return $this->getInteressadoValue('numeroLogradouro');
    }

    /**
     * Retorna a sigla do endereço do interessado
     * @return string
     */
    public function getInteressadoEnderecoSiglaUf(){
        return $this->getInteressadoValue('siglaUf');
    }

    /**
     * Retorna o número de identificação do interessado
     * @return string
     */
    public function getInteressadoNumeroIdent(){
        return $this->getInteressadoValue('numeroIdentInter');
    }

    /**
     * Retorna o Telefone Residencial do interessado
     * @return string
     */
    public function getInteressadoTelefoneResidencial(){
        return $this->getInteressadoValue('telefoneResidencial');
    }
        
    /**
     * Retorna uma propriedade do interessado
     *
     * @param string $attrName
     * @return mixed
     */
    private function getInteressadoValue($attrName){
        return isset($this->interessado[$attrName]) ? $this->interessado[$attrName] : null;
    }

    /**
     * Sobrescreve o método padrão da interface JsonSerializable, redefinindo os nomes
     * dos dados principais da entidade Processo
     */
    public function jsonSerialize(){
        return $this->format();
    }

    public function toArray(){
        return $this->format();
    }

    protected function format(){
        return [
            'numeroFormatado' => $this->getNumeroFormatado(),
            'siglaOrgao'    => $this->getSiglaOrgao(),
            'nomeOrgao' => $this->getNomeOrgao(),
            'numero' => $this->getNumero(),
            'ano' => $this->getAno(),
            'numeroProcessoExterno' => $this->getNumeroProcessoExterno(),
            'numeroVolume' => $this->getNumeroVolume(),
            'dataAutuacao' => $this->getDataAutuacao(true),
            'nomeMunicipio' => $this->getNomeMunicipio(),
            'siglaUf' => $this->getSiglaUf(),
            'nomeAutuador' => $this->getNomeAutuador(),
            'codigoClassificacao' => $this->getCodigoClassificacao(),
            'codigoAssunto' => $this->getCodigoAssunto(),
            'descricaoAssunto' => $this->getDescricaoAssunto(),
            'descricaoComplemento' => $this->getDescricaoComplemento(),
            'nomeSetorAbertura' => $this->getNomeSetorAbertura(),
            'nomeSetorAtual' => $this->getNomeSetorAtual(),
            'siglaSetorAtual' => $this->getSiglaSetorAtual(),
            'siglaOrgaoAtual' => $this->getSiglaOrgaoAtual(),
            'nomeOrgaoAtual' => $this->getNomeOrgaoAtual(),
            'interessado' => [
                'nome' => $this->getInteressadoNome(),
                'tipo' => $this->getInteressadoTipo(),
                'email' => $this->getInteressadoEmail(),
                'numeroIdent' => $this->getInteressadoNumeroIdent(),
                'telefoneResidencial' => $this->getInteressadoTelefoneResidencial(),
                'endereco' => [
                    'bairro' => $this->getInteressadoEnderecoBairro(),
                    'logradouro' => $this->getInteressadoEnderecoLogradouro(),
                    'codigoMunicpio' => $this->getInteressadoEnderecoCodigoMunicipio(),
                    'municipio' => $this->getInteressadoEnderecoMunicipio(),
                    'cep' => $this->getInteressadoEnderecoCep(),
                    'numero' => $this->getInteressadoEnderecoNumero(),
                    'siglaUf' => $this->getInteressadoEnderecoSiglaUf()
                ]
            ]
        ];
    }
}