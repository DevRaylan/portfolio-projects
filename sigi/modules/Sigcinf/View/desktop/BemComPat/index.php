<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h2>Bem com patrimônio</h2>
        </div>
        <div class="row">
            <div class="row">
                <div class="col-md-12">
                    <div class="well well-sm form-inline text-right">
                        <!-- Botão "Novo" -->
                        <button type="button" class="btn btn-success btn-md" name="btNovo"><i class="fas fa-plus"></i> Novo</button>
                    </div>
                </div>
            </div>
            <div class="row" id="listagemBemComPat" style="margin-bottom:30px;">
                <div class="col-sm-12">
                    <table id="tabelaBemComPat" class="table table-bordered table-hover dataTable dtr-inline" width="100%">
                        <thead>
                            <tr>
                                <th class="col-xs-2">Patrimônio</th>
                                <th class="col-xs-6">Descrição</th>
                                <th class="col-xs-1">Status</th>
                                <th class="col-xs-1">Unidade</th>
                                <th class="col-xs-1">Setor</th>
                                <th class="col-xs-1 text-center">Ações</th>
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

<!-- Modal com o formulário -->
<div class="modal fade" id="modalformBemComPat" tabindex="-1" role="dialog" aria-labelledby="modalformBemComPatLabel" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form name="formBemComPat" class="validator form-horizontal" role="form">
                <div class="modal-header">
                    <h3 class="modal-title" id="modalformBemComPatLabel">Bem com patrimônio</h3>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="bemcompat[id]" value="0"/>

                    <div class="form-group">
                        <label for="patrimonio" class="col-sm-3 control-label">Patrimônio</label>
                        <div class="col-sm-8">
                            <input type="text" id="patrimonio" class="form-control" name="bemcompat[patrimonio]" maxlength="9" required>
                        </div>    
                    </div>

                    <div class="form-group">
                        <label for="descricao" class="col-sm-3 control-label">Descrição</label>
                        <div class="col-sm-8">
                            <input type="text" id="descricao" class="form-control" name="bemcompat[descricao]" maxlength="30" required>
                        </div>    
                    </div>

                    <div class="form-group">
                        <label for="status" class="col-sm-3 control-label">Situação</label>
                        <div class="col-sm-8">
                            <select id="status" name="bemcompat[status]" class="form-control select-picker">
                                <option value="A">Ativo</option>
                                <option value="I">Inativo</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="centro" class="col-sm-3 control-label">Centro</label>
                        <div class="col-sm-8">
                            <select id="centro" name="bemcompat[centro]" class="form-control select-picker" required>
                                <option value="">Selecione</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="setor" class="col-sm-3 control-label">Setor</label>
                        <div class="col-sm-8">
                            <select id="setor" name="bemcompat[setor]" class="form-control select-picker" required>
                                <option value="">Selecione</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="btSalvar">
                        <span class="glyphicon glyphicon-floppy-disk" aria-hidden="false"></span> Salvar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" name="btCancelar">
                        <span class="glyphicon glyphicon-ban-circle" aria-hidden="false"></span> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<?php require dirname(__FILE__).'/../Modal.php'; ?>