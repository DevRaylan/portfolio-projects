<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Solicitação de equipamento</h2>
        </div>
        <div class="col-lg-12">
            <h2></h2>
        </div>


        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <div class="well well-sm form-inline text-right w-100">

                        <!-- Sequencial/Ano -->
                        <div class="form-group mr-3">
                            <label for="solicitacao[seqAno]" class="control-label">Sequencial/Ano:</label>
                            <input type="text" name="solicitacao[seqAno]" value="" class="form-control disabled" readonly />
                        </div>

                        <!-- Status -->
                        <div class="form-group mr-3">
                            <label for="solicitacao[status]" class="control-label">Status:</label>
                            <input type="text" name="solicitacao[status]" value="" class="form-control" readonly />
                        </div>

                        <!-- Botões -->
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary" name="btn-editar-solicitacao">
                                <i class="fas fa-calendar"></i> Prorrogar
                            </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary" name="btn-editar-solicitacaoModerar">
                                <i class="fas fa-pen"></i> Moderar
                            </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary" name="btn-imprimir">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>





        <div class="row">

            <form class="form-horizontal" id="form-horizontal">

                <div class="col-md-12">
                    <div class="panel with-nav-tabs panel-default">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#tab-detalhamento">Dados Gerais</a></li>
                                <li><a data-toggle="tab" href="#tab-bemcompat">Itens com patrimônio</a></li>
                                <li><a data-toggle="tab" href="#tab-bemsempat">Itens sem patrimônio</a></li>
                            </ul>
                        </div>
                        <div class="panel-body">
                            <div class="tab-content">

                                <div id="tab-detalhamento" class="tab-pane fade in active">


                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="solicitacao[cadastrador]" class="control-label col-xs-2">Cadastrada por: </label>
                                            <div class="col-xs-4">
                                                <input type="text" name="solicitacao[cadastrador]" value="" class="form-control" readonly />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="beneficiado[nome]" class="control-label col-xs-2">Beneficiado: </label>
                                            <div class="col-xs-4">
                                                <input type="text" name="beneficiado[nome]" value="" class="form-control" readonly />
                                            </div>
                                            <label for="beneficiado[centro]" class="control-label col-xs-2">Unidade: </label>
                                            <div class="col-xs-4">
                                                <input type="text" name="beneficiado[centro]" value="" class="form-control" readonly />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="responsavel[nome]" class="control-label col-xs-2">Responsável: </label>
                                            <div class="col-xs-4">
                                                <input type="text" name="responsavel[nome]" value="" class="form-control" readonly />
                                            </div>
                                            <label for="responsavel[centro]" class="control-label col-xs-2">Unidade: </label>
                                            <div class="col-xs-4">
                                                <input type="text" name="responsavel[centro]" value="" class="form-control" readonly />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="solicitacao[dtDevolucao]" class="control-label col-xs-2">Data de devolução: </label>
                                            <div class="col-xs-2">
                                                <input type="text" name="solicitacao[dtDevolucao]" value="" class="form-control" readonly />
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="solicitacao[categoria]" class="control-label col-xs-2">Categoria: </label>
                                            <div class="col-xs-10">
                                                <input type="text" name="solicitacao[categoria]" value="" class="form-control" readonly />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="solicitacao[centro]" class="control-label col-xs-2">Centro: </label>
                                            <div class="col-xs-4">
                                                <input type="text" name="solicitacao[centro]" value="" class="form-control" readonly />
                                            </div>
                                            <label for="solicitacao[setor]" class="control-label col-xs-2">Setor: </label>
                                            <div class="col-xs-4">
                                                <input type="text" name="solicitacao[setor]" value="" class="form-control" readonly />
                                            </div>
                                        </div>
                                    </div>






                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="detalhamento[observacao]" class="control-label col-xs-2">Observação:</label>
                                            <div class="col-xs-10">
                                                <textarea type="text" name="detalhamento[observacao]" value="" class="form-control" rows="2" readonly></textarea>
                                            </div>
                                        </div>
                                    </div>







                                </div>


                                <div id="tab-bemcompat" class="tab-pane fade in">
                                    <div class="row" id="listagemSolicitacaoBemComPat" style="margin-bottom:30px;">
                                        <div class="col-sm-12">
                                            <table id="tabelaSolicitacaoBemComPat" class="table table-bordered table-hover dataTable dtr-inline" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="20%">Patrimônio</th>
                                                        <th width="80%">Descrição</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div id="tab-bemsempat" class="tab-pane fade in">
                                    <div class="row" id="listagemSolicitacaoBemSemPat" style="margin-bottom:30px;">
                                        <div class="col-sm-12">
                                            <table id="tabelaSolicitacaoBemSemPat" class="table table-bordered table-hover dataTable dtr-inline" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="20%">Código</th>
                                                        <th width="80%">Descrição</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div id="tab-moderar" class="tab-pane fade in">
                                    <div class="form-group">
                                        <div class="btn-group pull-right">
                                            <button type="button" class="btn btn-sm btn-primary" name="btn-editar-solicitacaoModerar"><i class="fas fa-pen"></i> Editar</button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="moderar[status]" class="control-label col-xs-2">Status:</label>
                                        <div class="col-xs-10">
                                            <input type="text" name="moderar[status]" value="" class="form-control" readonly />
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="formSolicitacaoAlterar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="titulo-modal" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center">Prorrogar data da solicitação</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form class="form-horizontal" action="" method="post" name="modalformSolicitacaoAlterar" id="modalformSolicitacaoAlterar">
                        <div class="form-group">
                            <label for="dtDevolucao" class="col-sm-3 control-label">Devolução:</label>
                            <div class="col-sm-8 form-inline">
                                <div class="input-group datepicker">
                                    <input type="text" name="dtDevolucao" size="8" maxlength="10" id="dtDevolucao" value="" class="form-control" placeholder="Data">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btn-salvar-solicitacao"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="false"></span> Salvar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-ban-circle" aria-hidden="false"></span> Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div id="formSolicitacaoModerar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="titulo-modal" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center">Moderar solicitação</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form class="form-horizontal" action="" method="post" name="modalformSolicitacaoModerar" id="modalformSolicitacaoModerar">
                        <div class="form-group">
                            <label for="status" class="col-sm-3 control-label">Status: </label>
                            <div class="col-sm-8">
                                <select name="status" class="form-control select-picker">
                                    <option value="DIGITADA">DIGITADA</option>
                                    <option value="ATENDIDA">ATENDIDA</option>
                                    <option value="DEVOLVIDA">DEVOLVIDA</option>
                                    <option value="PENDENTE">PENDENTE</option>
                                    <option value="CANCELADA">CANCELADA</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="observacao" class="col-sm-3 control-label">Observação:</label>
                            <div class="col-sm-8">
                                <textarea id="observacao" name="observacao" class="form-control"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btn-salvar-solicitacaoModerar"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="false"></span> Salvar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-ban-circle" aria-hidden="false"></span> Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div id="usuario-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="titulo-modal" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center">Usuários</h4>
            </div>

            <div class="row" id="buscaUsuarios" style="margin-bottom:30px;">
                <label for="usuario[busca]" class="col-sm-3 control-label">Usuário:</label>
                <div class="col-sm-8 form-inline">
                    <div class="input-group">
                        <input type="text" class="form-control" name="usuario[busca]" id="usuario[busca]">
                    </div>
                    <button type="button" class="btn btn-success" id="btn-buscar-usuario" name="btn-buscar-usuario"><span class="glyphicon glyphicon-zoom-in" aria-hidden="false"></span> Buscar</button>
                </div>
            </div>

            <div class="row" id="listagemUsuarios" style="margin-bottom:30px;">
                <div class="col-sm-1">
                </div>
                <div class="col-sm-10">
                    <table id="tabelaUsuarios" class="table table-bordered table-hover dataTable dtr-inline" width="100%">
                        <thead>
                            <tr>
                                <th class="col-xs-1">Cpf</th>
                                <th class="col-xs-6">Nome</th>
                                <th class="col-xs-5 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-1">
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-group">
                    <div class="col-sm-12">
                        <button class="btn btn-default pull-right" data-dismiss="modal" type="button">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require dirname(__FILE__) . '/../Modal.php'; ?>