<?php

/**
 * @class GeradorRelatório
 *
 * Esta classe oferece funcionalidades para geração de relatórios em formato PDF.
 * 
 * @version 1.0
 * @author Mayara Madeira Trevisol <mayara.trevisol@udesc.br>
 */

namespace Geral\Model\Services;

require_once DIR_ROOT . '/' . DIR_LIBS . '/fpdf182/fpdf.php'; // Inicia o FPDF

use FPDF;

class RelatorioPdf extends FPDF
{
    // ------------ Formatações Mínimas ------------ 
    const FONTE = "Arial";
    const ESTILO_PADRAO = "";
    const ESTILO_ITALICO = "I";
    const ESTILO_NEGRITO = "B";
    const BORDA_ATIVA = 1;
    const BORDA_INATIVA = 0;
    const LINHA_NOVA = 1;
    const LINHA_CONTINUA = 0;

    // Alinhamentos
    const ALINHA_CENTRO = "C";
    const ALINHA_DIR = "R";
    const ALINHA_ESQ = "L";
    const TAMANHO_LINHA = 190;

    /* Tipo de Output: Como o PDF será aberto
    * I - Abre no navegador
    * D - Força o download
    * F - Salva local na pasta do projeto (não recomendado)
    * S - Retorna o arquivo em formato String
    * Default - valor padrão I
    */
    const OUTPUT_NAVEGADOR = "I";
    const OUTPUT_DOWNLOAD = "D";

    function __construct()
    {
        parent::__construct();

        $this->AliasNbPages();
        $this->AddPage();
    }

    // Cabeçalho
    public function setTopo($titulo = 'Titulo Indefinido', $imagem = '', $alturaImg, $larguraImg)
    {
        // Imagem
        if (!empty($imagem)) {
            $this->Image($imagem, 10, $alturaImg, $larguraImg);
            $this->Ln($alturaImg + 10);
        }

        $this->SetFont($this::FONTE, $this::ESTILO_NEGRITO, 15);
        // Move o cursor para direita
        $this->Cell(80);
        // Insere o título
        $this->Cell(30, 9, $titulo, $this::BORDA_INATIVA, $this::LINHA_CONTINUA, $this::ALINHA_CENTRO);
        // Espaçamento pós título
        $this->Ln(20);
    }

    // Sobreescrita da função Footer da biblioteca FPDF, por esse motivo se manteve a assunatura fora do padrão de dev UDESC
    public function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Aplica fonte menor para o rodapé
        $this->SetFont($this::FONTE, $this::ESTILO_ITALICO, 8);
        // Insere numero da pagina
        $this->Cell(0, 9, 'Pg ' . $this->PageNo() . '/{nb}', $this::BORDA_INATIVA, $this::LINHA_CONTINUA, $this::ALINHA_CENTRO);
    }

    // Conteúdo tabelado
    public function setConteudoTabelado($dados)
    {
        // Particiona em colunas de tamanhos iguais
        $cabecalhos = array_keys($dados[0]);
        $nrColC = count($cabecalhos) - 1;
        $tamanhoCol = $this::TAMANHO_LINHA / $nrColC;

        // Insere titulos da tabela
        $this->SetFont($this::FONTE, $this::ESTILO_NEGRITO, 12);
        for ($i = 1; $i < $nrColC; $i++) {
            $this->Cell($tamanhoCol, 9, ucfirst($cabecalhos[$i]), $this::BORDA_ATIVA, $this::LINHA_CONTINUA, $this::ALINHA_CENTRO);
        }
        $this->Cell($tamanhoCol, 9, ucfirst($cabecalhos[$nrColC]), $this::BORDA_ATIVA, $this::LINHA_NOVA, $this::ALINHA_CENTRO);

        // Insere conteudo da tabela
        $this->SetFont($this::FONTE, $this::ESTILO_PADRAO, 9);
        foreach ($dados as $item) {
            $valores = array_values($item);
            $nrColV = count($valores) - 1;

            for ($v = 1; $v < $nrColV; $v++) {
                $this->Cell($tamanhoCol, 5, $valores[$v],  $this::BORDA_ATIVA, $this::LINHA_CONTINUA, $this::ALINHA_ESQ);
            }
            $this->Cell($tamanhoCol, 5, $valores[$nrColV], $this::BORDA_ATIVA, $this::LINHA_NOVA, $this::ALINHA_ESQ);
        }
    }

    // Gera arquivo PDF
    public function exportarRelatorio($arquivo, $tipoOutput)
    {
        $this->Output($tipoOutput, $arquivo);
    }
}
