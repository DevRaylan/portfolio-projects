<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h2>Bem sem patrimônio</h2>
        </div>
        <div class="row">
            <div class="row">
                <div class="col-md-12">
                    <div class="well well-sm form-inline text-right">
                        <button type="button" class="btn btn-success btn-md" name="btNovo"><i class="fas fa-plus"></i> Novo</button>
                    </div>
                </div>
            </div>
            <div class="row" id="listagemBemSemPat" style="margin-bottom:30px;">
                <div class="col-sm-12">
                    <table id="tabelaBemSemPat" class="table table-bordered table-hover dataTable dtr-inline" width="100%">
                        <thead>
                            <tr>
                                <th class="col-xs-2">Código</th>
                                <th class="col-xs-4">Descrição</th>
                                <th class="col-xs-1">Quantidade</th>
                                <th class="col-xs-1">Status</th>
                                <th class="col-xs-1">Unidade</th>
                                <th class="col-xs-1">Setor</th>
                                <th class="col-xs-3 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- EDITAR DADOS -->
<div class="modal fade" id="modalformBemSemPat" tabindex="-1" role="dialog" aria-labelledby="modalformBemSemPatLabel" data-backdrop="static">
    <form name="formBemSemPat" class="validator form-horizontal" role="form" data-toggle="validator">
        <div class="modal-dialog modal-lg" role="document"> <!-- Adicionei modal-lg para aumentar o tamanho -->
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"> Detalhes do bem sem patrimônio</h3>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="bemsempat[id]" value="0"/>
                    <div class="form-group">
                        <label for="bemsempat[codigo]" class="col-sm-3 control-label">Código:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="bemsempat[codigo]" maxlength="10">
                        </div>    
                    </div>
                    <div class="form-group">
                        <label for="bemsempat[descricao]" class="col-sm-3 control-label">Descrição:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="bemsempat[descricao]" maxlength="50">
                        </div>    
                    </div>
                    <div class="form-group">
                        <label for="bemsempat[status]" class="col-sm-3 control-label">Situação:</label>
                        <div class="col-sm-8">
                            <select name="bemsempat[status]" class="form-control select-picker">
                                <option value="A">Ativo</option>
                                <option value="I">Inativo</option>
                            </select>
                        </div>    
                    </div>
                    <div class="form-group">
                        <label for="bemsempat[centro]" class="col-sm-3 control-label">Centro:</label>
                        <div class="col-sm-8">
                            <select name="bemsempat[centro]" class="form-control select-picker" required>
                                <option value="">Selecione</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bemsempat[setor]" class="col-sm-3 control-label">Setor:</label>
                        <div class="col-sm-8">
                            <select name="bemsempat[setor]" class="form-control select-picker" required>
                                <option value="">Selecione</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" name="btn-salvar">
                        <span class="glyphicon glyphicon-floppy-disk" aria-hidden="false"></span> Salvar
                    </button>
                    <button type="button" class="btn btn-default" name="btn-cancelar">
                        <span class="glyphicon glyphicon-ban-circle" aria-hidden="false"></span> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- ATUALIZAR QUANTIDADE -->
<div class="modal fade" id="modalformBemSemPatQtd" tabindex="-1" role="dialog" aria-labelledby="modalformBemSemPatQtdLabel" data-backdrop="static">
    <form name="formBemSemPatQtd" class="validator form-horizontal" role="form" data-toggle="validator">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Atualização</h3>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="bemsempatqtd[id]" value="0"/>
                    <div class="form-group">
                        <label for="bemsempatqtd[operacao]" class="col-sm-3 control-label">Operação:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="bemsempatqtd[operacao]" maxlength="10" readonly >
                        </div>    
                    </div>
                    <div class="form-group">
                        <label for="bemsempatqtd[qtd]" class="col-sm-3 control-label">Quantidade:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="bemsempatqtd[qtd]" maxlength="10" >
                        </div>    
                    </div>
                    <div class="form-group">
                        <label for="bemsempatqtd[motivo]" class="col-sm-3 control-label">Motivo:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="bemsempatqtd[motivo]" maxlength="50" >
                        </div>    
                    </div>
                    <div class="form-group">
                        <label for="bemsempatqtd[observacao]" class="col-sm-3 control-label">Observação:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="bemsempatqtd[observacao]" maxlength="50" >
                        </div>    
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" name="btn-salvar"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="false"></span> Salvar</button>
                    <button type="button" class="btn btn-default" name="btn-cancelar"><span class="glyphicon glyphicon-ban-circle" aria-hidden="false"></span> Cancelar</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Histórico -->
<div class="modal fade" id="modalformBemSemPatHist" tabindex="-1" role="dialog" aria-labelledby="modalformBemSemPatHistLabel" data-backdrop="static">
    <form name="formBemSemPatHist" class="validator form-horizontal" role="form" data-toggle="validator">
        <!-- Alterado: Adicionado width:90% e max-width:1200px para aumentar o tamanho do modal na tela -->
        <div class="modal-dialog modal-xl" role="document" style="width: 90%; max-width: 1200px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Histórico</h3>
                </div>
                <!-- Alterado: Adicionado margin:15px para dar mais espaçamento interno ao conteúdo -->
                <div class="row" id="listagemBemSemPatHist" style="margin: 15px;">
                    <div class="col-sm-12">
                        <table id="tabelaBemSemPatHist" class="table table-bordered table-hover dataTable dtr-inline" width="100%">
                            <thead>
                                <tr>
                                    <th class="col-xs-1">Data</th>
                                    <th class="col-xs-1">Operação</th>
                                    <th class="col-xs-1">Quantidade</th>
                                    <th class="col-xs-3">Motivo</th>                                    
                                    <th class="col-xs-4">Observação</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" name="btn-fechar-hist">
                        <span class="glyphicon glyphicon-ban-circle" aria-hidden="false"></span> Fechar
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>


<?php require dirname(__FILE__).'/../Modal.php'; ?>