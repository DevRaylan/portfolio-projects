<?php

namespace Sigcinf\Controller;

use Exception;
use Sigcinf\Model\Factory\BemSemPatFactory;
use Sigcinf\Model\Factory\BemComPatFactory;
use Sigcinf\Model\Factory\SolicitacaoFactory;
use Sigcinf\Model\Factory\SolicitacaoBemSemPatFactory;
use Sigcinf\Model\Factory\SolicitacaoBemComPatFactory;
use \Geral\Model\UserSession;
use Sigcinf\Model\Factory\UsuarioFactory;
use \Geral\Model\MailSender;
use \Geral\Model\Services\RelatorioPdf;

class SolicitacaoController extends \Geral\Controller\AbstractPrivateController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->AddJs('solicitacoes-listar-script.js', true);
        $this->view();
    }

    public function detail()
    {
        $this->AddJs('solicitacoes-detalhar-script.js', true);
        $this->view();
    }

    public function alterar()
    {
        $this->AddJs('solicitacoes-alterar-script.js', true);
        $this->view();
    }

    public function incluir()
    {
        $this->AddJs('solicitacoes-incluir-script.js', true);
        $this->view();
    }

    public function moderar()
    {
        $this->AddJs('solicitacoes-moderar-script.js', true);
        $this->view();
    }

    public function relatorio()
    {
        $this->AddJs('solicitacoes-relatorio-script.js', true);
        $this->view();
    }

    /*
     * Métodos para chamadas AJAX
     * */

    public function getAll()
    {

        $recordSet = SolicitacaoFactory::getAll(true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getDigitadasModerar()
    {

        if (!$this->auth->isAllowedTransactions(['dev', 'admin', 'gerente'])) {
            $this->setError('Usuário sem permissão de acesso.');
            $this->sendResponse();
            exit;
        }

        // pega o Centro do usuario moderador.
        if ($this->auth->isAllowedTransactions(['dev', 'admin'])) {
            $idCentroModerado = 0;
        } else {
            $cpf = UserSession::getParam('cpf');
            $oUsuario = UsuarioFactory::getByCpf1($cpf);
            $oCentro = $oUsuario->getUnidade();
            $idCentroModerado = $oCentro->getId();
        };

        $recordSet = SolicitacaoFactory::getByStatus(SolicitacaoFactory::ST_DIGITADA, $idCentroModerado, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getAtendidasModerar()
    {

        if (!$this->auth->isAllowedTransactions(['dev', 'admin', 'gerente'])) {
            $this->setError('Usuário sem permissão de acesso.');
            $this->sendResponse();
            exit;
        }

        // pega o Centro do usuario moderador.
        if ($this->auth->isAllowedTransactions(['dev', 'admin'])) {
            $idCentroModerado = 0;
        } else {
            $cpf = UserSession::getParam('cpf');
            $oUsuario = UsuarioFactory::getByCpf1($cpf);
            $oCentro = $oUsuario->getUnidade();
            $idCentroModerado = $oCentro->getId();
        };

        $recordSet = SolicitacaoFactory::getByStatus(SolicitacaoFactory::ST_ATENDIDA, $idCentroModerado, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getDevolvidasModerar()
    {

        if (!$this->auth->isAllowedTransactions(['dev', 'admin', 'gerente'])) {
            $this->setError('Usuário sem permissão de acesso.');
            $this->sendResponse();
            exit;
        }

        // pega o Centro do usuario moderador.
        if ($this->auth->isAllowedTransactions(['dev', 'admin'])) {
            $idCentroModerado = 0;
        } else {
            $cpf = UserSession::getParam('cpf');
            $oUsuario = UsuarioFactory::getByCpf1($cpf);
            $oCentro = $oUsuario->getUnidade();
            $idCentroModerado = $oCentro->getId();
        };

        $recordSet = SolicitacaoFactory::getByStatus(SolicitacaoFactory::ST_DEVOLVIDA, $idCentroModerado, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getAtrasadasModerar()
    {

        if (!$this->auth->isAllowedTransactions(['dev', 'admin', 'gerente'])) {
            $this->setError('Usuário sem permissão de acesso.');
            $this->sendResponse();
            exit;
        }

        // pega o Centro do usuario moderador.
        if ($this->auth->isAllowedTransactions(['dev', 'admin'])) {
            $idCentroModerado = 0;
        } else {
            $cpf = UserSession::getParam('cpf');
            $oUsuario = UsuarioFactory::getByCpf1($cpf);
            $oCentro = $oUsuario->getUnidade();
            $idCentroModerado = $oCentro->getId();
        };

        $recordSet = SolicitacaoFactory::getByStatus(SolicitacaoFactory::ST_ATRASADA, $idCentroModerado, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getPendentesModerar()
    {

        if (!$this->auth->isAllowedTransactions(['dev', 'admin', 'gerente'])) {
            $this->setError('Usuário sem permissão de acesso.');
            $this->sendResponse();
            exit;
        }

        // pega o Centro do usuario moderador.
        if ($this->auth->isAllowedTransactions(['dev', 'admin'])) {
            $idCentroModerado = 0;
        } else {
            $cpf = UserSession::getParam('cpf');
            $oUsuario = UsuarioFactory::getByCpf1($cpf);
            $oCentro = $oUsuario->getUnidade();
            $idCentroModerado = $oCentro->getId();
        };

        $recordSet = SolicitacaoFactory::getByStatus(SolicitacaoFactory::ST_PENDENTE, $idCentroModerado, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getCanceladasModerar()
    {

        if (!$this->auth->isAllowedTransactions(['dev', 'admin', 'gerente'])) {
            $this->setError('Usuário sem permissão de acesso.');
            $this->sendResponse();
            exit;
        }

        // pega o Centro do usuario moderador.
        if ($this->auth->isAllowedTransactions(['dev', 'admin'])) {
            $idCentroModerado = 0;
        } else {
            $cpf = UserSession::getParam('cpf');
            $oUsuario = UsuarioFactory::getByCpf1($cpf);
            $oCentro = $oUsuario->getUnidade();
            $idCentroModerado = $oCentro->getId();
        };

        $recordSet = SolicitacaoFactory::getByStatus(SolicitacaoFactory::ST_CANCELADA, $idCentroModerado, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getCpfResponsavelAndBeneficiado()
    {
        $cpf = UserSession::getParam('cpf');
        $recordSet = SolicitacaoFactory::getByCpf($cpf, '', true);
        $this->setSuccess('', ['data' => $recordSet]);
        $this->sendResponse();
    }

    //    public function getDigitadasCpf()
    //    {
    //        $cpf = UserSession::getParam('cpf');
    //        $recordSet = SolicitacaoFactory::getByCpf($cpf, 'DIGITADA', true);
    //        $this->setSuccess('', ['data' => $recordSet]);
    //        $this->sendResponse();
    //    }

    //    public function getAtendidasCpf()
    //    {
    //        $cpf = UserSession::getParam('cpf');
    //        $recordSet = SolicitacaoFactory::getByCpf($cpf, 'ATENDIDA',true);
    //        $this->setSuccess('', ['data' => $recordSet]);
    //        $this->sendResponse();
    //    }

    //    public function getDevolvidasCpf()
    //    {
    //        $cpf = UserSession::getParam('cpf');
    //        $recordSet = SolicitacaoFactory::getByCpf($cpf, 'DEVOLVIDA',true);
    //        $this->setSuccess('', ['data' => $recordSet]);
    //        $this->sendResponse();
    //    }

    //    public function getAtrasadasCpf()
    //    {
    //        $cpf = UserSession::getParam('cpf');
    //        $recordSet = SolicitacaoFactory::getByCpf($cpf, 'ATRASADA',true);
    //        $this->setSuccess('', ['data' => $recordSet]);
    //        $this->sendResponse();
    //    }

    //    public function getPendentesCpf()
    //    {
    //        $cpf = UserSession::getParam('cpf');
    //        $recordSet = SolicitacaoFactory::getByCpf($cpf, 'PENDENTE',true);
    //        $this->setSuccess('', ['data' => $recordSet]);
    //        $this->sendResponse();
    //    }

    //    public function getCanceladasCpf()
    //    {
    //        $cpf = UserSession::getParam('cpf');
    //        $recordSet = SolicitacaoFactory::getByCpf($cpf, 'CANCELADA',true);
    //        $this->setSuccess('', ['data' => $recordSet]);
    //        $this->sendResponse();
    //    }

    public function getByBuscaGeralCpf()
    {
        $busca = $this->request->post('busca');
        $recordSet = SolicitacaoFactory::getByBuscaGeralCpf($busca, true);
        $this->setSuccess('', ['data' => $recordSet]);
        $this->sendResponse();
    }

    public function getByBuscaGeralComPatrimonio()
    {
        $patrimonio = $this->request->post('busca');
        $recordSet = SolicitacaoBemComPatFactory::getByBuscaGeralPatrimonio($patrimonio, true);
        $this->setSuccess('', ['data' => $recordSet]);
        $this->sendResponse();
    }

    public function getByBuscaGeralSemPatrimonio()
    {
        $codigo = $this->request->post('busca');
        $recordSet = SolicitacaoBemSemPatFactory::getByBuscaGeralCodigo($codigo, true);
        $this->setSuccess('', ['data' => $recordSet]);
        $this->sendResponse();
    }

    public function getById()
    {
        $id = (int) $this->url->getSegment(4);

        $recordSet = SolicitacaoFactory::getById($id, true);
        $this->setSuccess('Solicitacao localizada com sucesso', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getBemSemPat()
    {

        $id = (int) $this->url->getSegment(4);

        $recordSet = SolicitacaoBemSemPatFactory::getBemSemPat($id, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function getBemComPat()
    {

        $id = (int) $this->url->getSegment(4);

        $recordSet = SolicitacaoBemComPatFactory::getBemComPat($id, true);
        $this->setSuccess('', ['data' => $recordSet]);

        $this->sendResponse();
    }

    public function adicionar()
    {

        $dados['beneficiado'] = $this->request->post('beneficiado');
        $dados['responsavel'] = $this->request->post('responsavel');
        $dados['especificacao'] = $this->request->post('especificacao');
        $dados['dtDevolucao'] = $this->request->post('dtDevolucao');
        $dados['centro'] = $this->request->post('centro');
        $dados['setor'] = $this->request->post('setor');
        $dados['categoria'] = $this->request->post('categoria');
        $dados['observacao'] = $this->request->post('observacao');
        $dados['bemsempat'] = $this->request->post('bemsempat');
        $dados['bemcompat'] = $this->request->post('bemcompat');

        $cpf = UserSession::getParam('cpf');
        $dados['cadastrador'] = $cpf;

        $qtItens = 0;

        if (isset($dados['bemsempat'])) {
            if (count($dados['bemsempat']) > 0) {
                $qtItens = $qtItens + count($dados['bemsempat']);
            };
        };

        if (isset($dados['bemcompat'])) {
            if (count($dados['bemcompat']) > 0) {
                $qtItens = $qtItens + count($dados['bemcompat']);
            };
        };

        if ($qtItens == 0) {
            $this->setError('Necessário preencher pelo menos 1 (um) item.');
            $this->sendResponse();
            exit;
        };

        try {
            $solicitacao = SolicitacaoFactory::adicionar($dados);
            if (isset($dados['bemsempat'])) {
                if (count($dados['bemsempat']) > 0) {
                    foreach ($dados['bemsempat'] as $bemsempatL) {
                        $bemsempat = BemSemPatFactory::getById($bemsempatL['id']);
                        SolicitacaoBemSemPatFactory::add($solicitacao, $bemsempat, 'Observacao');
                    };
                };
            };
            if (isset($dados['bemcompat'])) {
                if (count($dados['bemcompat']) > 0) {
                    foreach ($dados['bemcompat'] as $bemcompatL) {
                        $bemcompat = BemComPatFactory::getById($bemcompatL['id']);
                        SolicitacaoBemComPatFactory::add($solicitacao, $bemcompat, 'Observacao');
                    };
                };
            };
            $this->setSuccess('Solicitação adicionada com sucesso.');
        } catch (\Exception $e) {
            $this->setError('Erro ao adicionar solicitação.');
        }

        $this->sendResponse();
    }

    public function atualizar()
    {
        $dados['id'] = (int) $_POST['id'];
        $dados['dtDevolucao'] = $this->request->post('dtDevolucao');

        $registro = SolicitacaoFactory::getById($dados['id']);
        $status = $registro->getStatus();

        if (($status == SolicitacaoFactory::ST_DEVOLVIDA) or ($status == SolicitacaoFactory::ST_PENDENTE) or ($status == SolicitacaoFactory::ST_CANCELADA)) {
            $this->setError('Status não permitido');
            $this->sendResponse();
            exit;
        };

        try {
            $registro = SolicitacaoFactory::atualizar($dados);
            $this->enviarEmail('ATUALIZADA', $dados['id']);
            $this->setSuccess('Solicitacao atualizada com sucesso.');
        } catch (\Exception $e) {
            $this->setError('Erro ao atualizar solicitacao.');
        }

        $this->sendResponse();
    }

    public function atualizarStatus()
    {

        $dados['id'] = (int) $_POST['id'];
        $dados['status'] = $this->request->post('status');
        $dados['observacao'] = $this->request->post('observacao');

        $registro = SolicitacaoFactory::getById($dados['id']);
        $status = $registro->getStatus();

        if ($status == SolicitacaoFactory::ST_DIGITADA) {
            if (($dados['status'] == SolicitacaoFactory::ST_DEVOLVIDA) or ($dados['status'] == SolicitacaoFactory::ST_PENDENTE)) {
                $this->setError('Status não permitido');
                $this->sendResponse();
                exit;
            }
        };

        if ($status == SolicitacaoFactory::ST_ATENDIDA) {
            if (($dados['status'] == SolicitacaoFactory::ST_DIGITADA) or ($dados['status'] == SolicitacaoFactory::ST_CANCELADA)) {
                $this->setError('Status não permitido');
                $this->sendResponse();
                exit;
            }
        };

        if ($status == SolicitacaoFactory::ST_DEVOLVIDA) {
            $this->setError('Status não permitido');
            $this->sendResponse();
            exit;
        };

        if ($status == SolicitacaoFactory::ST_PENDENTE) {
            if (($dados['status'] == SolicitacaoFactory::ST_DIGITADA) or ($dados['status'] == SolicitacaoFactory::ST_ATENDIDA) or ($dados['status'] == SolicitacaoFactory::ST_CANCELADA)) {
                $this->setError('Status não permitido');
                $this->sendResponse();
                exit;
            }
        };

        if ($status == SolicitacaoFactory::ST_CANCELADA) {
            $this->setError('Status não permitido');
            $this->sendResponse();
            exit;
        };

        try {
            $registro = SolicitacaoFactory::atualizarStatus($dados);
            $this->enviarEmail($dados['status'], $dados['id']);
            $this->setSuccess('Solicitação atualizada com sucesso.');
        } catch (\Exception $e) {
            $this->setError('Erro ao atualizar solicitacao.');
        }

        $this->sendResponse();
    }

    public function enviarEmail($status, $id)
    {

        $registro = SolicitacaoFactory::getById($id);
        $seq = $registro->getSequencia();
        $ano = $registro->getAno();
        $dtDevolucao = $registro->getDtHrDevolucao();
        $dtDevolucao = substr($dtDevolucao, 8, 2) . '/' . substr($dtDevolucao, 5, 2) . '/' . substr($dtDevolucao, 0, 4);
        $emailBeneficiado = $registro->getBeneficiado()->getEmail();
        $emailResponsavel = $registro->getResponsavel()->getEmail();
        $emailSetor = $registro->getSetor()->getEmail();

        // subject
        $subject = "Solicitação nº {$seq}/{$ano} processada";

        // mensagem
        $message = '';
        $message = $message .  "<p>Solicitação nro. {$seq}/{$ano} </p>";
        $message = $message .  "<p>Situação: {$status} </p>";
        $message = $message .  "<p>Data de devolução: <strong>{$dtDevolucao}</strong> </p>";
        $message = $message .  "<p>Itens: </p>";

        $nro = 0;
        $registros = SolicitacaoBemSemPatFactory::getBemSemPat($id, true);
        foreach ($registros as $registroL) {
            $nro = $nro + 1;
            $message = $message .  "<p>{$nro}:  {$registroL['bemsempat']['codigo']} {$registroL['bemsempat']['descricao']} </p>";
        };

        $registros = SolicitacaoBemComPatFactory::getBemComPat($id, true);
        foreach ($registros as $registroL) {
            $nro = $nro + 1;
            $message = $message .  "<p>{$nro}:  {$registroL['bemcompat']['patrimonio']} {$registroL['bemcompat']['descricao']} </p>";
        };

        $message .= "<p>Acompanhamento das solicitações no endereço: </p>";
        $message .= "<p><a href='https://carlostreino.dev.udesc.br/Sigcinf'>https://carlostreino.dev.udesc.br/Sigcinf</a></p>";

        // destinatario
        if (($_SERVER['AMBIENTE'] == 'DEV') or ($_SERVER['AMBIENTE'] == 'HOM')) {
            $email[0] = 'carlos.luz@udesc.br';
            $email[1] = 'cinf.ceart@udesc.br';
        } else {
            $email[0] = $emailBeneficiado;
            $email[1] = $emailResponsavel;
            $email[2] = $emailSetor;
        };

        // enviar
        $mail = new MailSender;
        $mail->send($email, $subject, $message);
    }

    public function gerarRelatorio()
    {

        $id = $_GET['id'];;

        // Localização do arquivo gerado - Pasta temp do modulo
        $arquivo = "/termo-emprestimo.pdf";
        $titulo = "Termo de Empréstimo";
        $imagem = DIR_SYS . '/imagens/Geral/logo_udesc.jpg';
        $alturaImg = 10;
        $larguraImg = 120;

        // ------------ Geração do relatorio personalizado  ------------ 
        $rel = new RelatorioPdf();
        $rel->setTopo($titulo, $imagem, $alturaImg, $larguraImg);

        // meu teste
        $registro = SolicitacaoFactory::getById($id);
        $seq = $registro->getSequencia();
        $ano = $registro->getAno();
        $dtDevolucao = $registro->getDtHrDevolucao();
        $dtDevolucao = substr($dtDevolucao, 8, 2) . '/' . substr($dtDevolucao, 5, 2) . '/' . substr($dtDevolucao, 0, 4);
        $beneficiado = $registro->getBeneficiado()->getNome();
        $responsavel = $registro->getResponsavel()->getNome();
        $cadastrador = $registro->getCadastrador()->getNome();

        $dtInclusao = $registro->getDtHrInclusao();
        $hrInclusao = substr($dtInclusao, 11, 2) . ':' . substr($dtInclusao, 14, 2);
        $dtInclusao = substr($dtInclusao, 8, 2) . '/' . substr($dtInclusao, 5, 2) . '/' . substr($dtInclusao, 0, 4);

        $rel->SetFont('Arial', '', 12);
        $rel->Cell(0, 10, "Solicitação: " . $seq . "/" . $ano,  0, 1);

        $rel->SetFont('Arial', '', 12);
        $rel->Cell(0, 10, "Data da solicitação: " . $dtInclusao . " as " . $hrInclusao,  0, 1);

        $rel->SetFont('Arial', '', 12);
        $rel->Cell(0, 10, "Cadastrado por: " . $cadastrador,  0, 1);

        $rel->SetFont('Arial', '', 12);
        $rel->Cell(0, 10, "Beneficiado: " . $beneficiado,  0, 1);

        $rel->SetFont('Arial', '', 12);
        $rel->Cell(0, 10, "Data de devolução: " . $dtDevolucao,  0, 1);

        $rel->SetFont('Arial', '', 12);
        $rel->Cell(0, 10, " ",  0, 1);

        $dados = SolicitacaoBemComPatFactory::getBemComPat($id, true);

        if (!empty($dados)) {
            $rel->SetFont($rel::FONTE, $rel::ESTILO_NEGRITO, 12);
            $rel->Cell(40, 9, "Identificador", $rel::BORDA_ATIVA, $rel::LINHA_CONTINUA, $rel::ALINHA_CENTRO);
            $rel->Cell(150, 9, "Descrição", $rel::BORDA_ATIVA, $rel::LINHA_NOVA, $rel::ALINHA_CENTRO);
        }

        foreach ($dados as $bemcompat) {
            // Insere titulos da tabela
            $rel->SetFont($rel::FONTE, $rel::ESTILO_PADRAO, 9);
            $rel->Cell(40, 5, $bemcompat['bemcompat']['patrimonio'],  $rel::BORDA_ATIVA, $rel::LINHA_CONTINUA, $rel::ALINHA_CENTRO);
            $rel->Cell(150, 5, $bemcompat['bemcompat']['descricao'], $rel::BORDA_ATIVA, $rel::LINHA_NOVA, $rel::ALINHA_ESQ);
            $rel->Ln(10);
        }

        $dadosSem = SolicitacaoBemSemPatFactory::getBemSemPat($id, true);

        if (empty($dados)) {
            $rel->SetFont($rel::FONTE, $rel::ESTILO_NEGRITO, 12);
            $rel->Cell(40, 9, "Identificador", $rel::BORDA_ATIVA, $rel::LINHA_CONTINUA, $rel::ALINHA_CENTRO);
            $rel->Cell(150, 9, "Descrição", $rel::BORDA_ATIVA, $rel::LINHA_NOVA, $rel::ALINHA_CENTRO);
        }

        foreach ($dadosSem as $bemsempat) {
            // Insere titulos da tabela
            $rel->SetFont($rel::FONTE, $rel::ESTILO_PADRAO, 9);
            $rel->Cell(40, 5, $bemsempat['bemsempat']['codigo'],  $rel::BORDA_ATIVA, $rel::LINHA_CONTINUA, $rel::ALINHA_CENTRO);
            $rel->Cell(150, 5, $bemsempat['bemsempat']['descricao'], $rel::BORDA_ATIVA, $rel::LINHA_NOVA, $rel::ALINHA_ESQ);
            $rel->Ln(10);
        }

        $rel->SetFont('Arial', '', 12);
        $rel->Cell(0, 10, "Declaro que aceito e me responsabilizo pelo(s) equipamento(s) descrito(s) acima.",  0, 1);

        $rel->SetFont('Arial', '', 12);
        $rel->Cell(0, 10, "Responsável: " . $responsavel,  0, 1);

        $rel->SetFont('Arial', '', 12);
        $rel->Cell(0, 10, "Assinatura: ________________________________________________",  0, 1);

        $date = date("Y-m-d H:i:s");
        $rel->SetFont('Arial', '', 12);
        $rel->Cell(0, 10, "Data: " . substr($date, 8, 2) . '/' . substr($date, 5, 2) . '/' . substr($date, 0, 4),  0, 1);

        $rel->Output('D', $arquivo);
    }
}
