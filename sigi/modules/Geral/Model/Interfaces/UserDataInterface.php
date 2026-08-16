<?php
namespace Geral\Model\Interfaces;

interface UserDataInterface
{
    public const NOME_CAMPO_VINCULO = 'vinculo';

    public const NOME_VINCULO_PROFESSOR = 'Professor';
    public const NOME_VINCULO_TECNICO = 'Técnico';
    public const NOME_VINCULO_ALUNO = 'Aluno';
    public const NOME_VINCULO_ESTAGIARIO = 'Estagiario';
    public const NOME_VINCULO_TERCEIRIZADO = 'Terceirizado';
    public const NOME_VINCULO_EXTENSAO = 'Extensao';
    public const NOME_VINCULO_FC = 'Função de Chefia';
    public const NOME_VINCULO_AFASTADO = 'Afastado';
    public const NOME_VINCULO_APOSENTADO = 'Aposentado';
    public const NOME_VINCULO_EXONERADO = 'Exonerado';

    public const VINCULOS_ESPERADOS = [
        self::NOME_VINCULO_PROFESSOR,
        self::NOME_VINCULO_TECNICO,
        self::NOME_VINCULO_ALUNO,
        self::NOME_VINCULO_ESTAGIARIO,
        self::NOME_VINCULO_TERCEIRIZADO,
        self::NOME_VINCULO_EXTENSAO,
        self::NOME_VINCULO_FC,
        self::NOME_VINCULO_AFASTADO,
        self::NOME_VINCULO_APOSENTADO,
        self::NOME_VINCULO_EXONERADO
    ];

    // Atributos que são suportados pela Factory, deverão estar presentes em Model/UserData.php
    public const ATRIBUTOS_DO_USUARIO = [
        self::NOME_CAMPO_VINCULO, 'nome', 'cpf', 'sexo', 'dataNascimento', 'matricula', 'situacao',
        'unidade', 'setor', 'cargo', 'funcao',
        'email', 'emailAlias', 'emailAlternativo',
        'telefoneComercial', 'telefoneCelular', 'telefoneResidencial',
        'situacao', 'isPrincipal', 'foto'
    ];
    
    public function getCpf();
    public function setCpf(String $cpf);
    public function getMatricula();
    public function setMatricula(String $matricula);
    public function getNome();
    public function setNome(String $nome);
    public function getVinculo();
    public function setVinculo($vinculo);
    public function getSituacao();
    public function setSituacao($situacao);
    public function getUnidade();
    public function setUnidade($unidade);
    public function getSetor();
    public function setSetor($setor);
    public function getCargo();
    public function setCargo($cargo);
    public function getFuncao();
    public function setFuncao($funcao);
    public function getEmail();
    public function setEmail($email);
    public function getEmailAlias();
    public function setEmailAlias($emailAlias);
    public function getEmailAlternativo();
    public function setEmailAlternativo($emailAlternativo);
    public function getTelefoneComercial();
    public function setTelefoneComercial($telefoneComercial);
    public function getTelefoneCelular();
    public function setTelefoneCelular($telefoneCelular);
    public function getTelefoneResidencial();
    public function setTelefoneResidencial($telefoneResidencial);
    public function getIsPrincipal();
    public function setIsPrincipal($isPrincipal);
    public function getFoto();
    public function setFoto($foto);
}
