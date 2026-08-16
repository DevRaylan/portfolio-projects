<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h2>Setores</h2>
        </div>
        <div class="row">
            <div class="row">
                <div class="col-md-12">
                    <div class="well well-sm form-inline text-right">
                        <button type="button" class="btn btn-success btn-md" name="btNovo"><i class="fas fa-plus"></i> Novo setor</button>
                    </div>
                </div>
            </div>
            <div class="row" id="listagemSetores" style="margin-bottom:30px;">
                <div class="col-sm-12">
                    <table id="tabelaSetores" class="table table-bordered table-hover dataTable dtr-inline" width="100%">
                        <thead>
                            <tr>
                                <th class="col-xs-4">Nome</th>
                                <th class="col-xs-3">Email</th>
                                <th class="col-xs-1">Unidade</th>
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


<!-- Modal com o formulÃ¡rio -->
<div class="modal fade" id="modalformSetores" tabindex="-1" role="dialog" aria-labelledby="modalformSetoresLabel" data-backdrop="static">
    <form name="formSetores" class="validator form-horizontal" role="form" data-toggle="validator">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header"><h3 class="modal-title">Setor</h3></div>
                <div class="modal-body">
                    <input type="hidden" name="setor[id]" value="0"/>
                    <div class="form-group">
                        <label for="setor[centro]" class="col-sm-3 control-label">Centro: </label>
                        <div class="col-sm-8">
                            <select name="setor[centro]" class="form-control select-picker" required>
                                <option value="">Selecione</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="setor[nome]" class="col-sm-3 control-label">Setor: </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="setor[nome]" maxlength="50" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="setor[email]" class="col-sm-3 control-label">E-mail:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="setor[email]" maxlength="100" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" name="btSalvar"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="false"></span> Salvar</button>
                    <button type="button" class="btn btn-default" name="btCancelar"><span class="glyphicon glyphicon-ban-circle" aria-hidden="false"></span> Cancelar</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require dirname(__FILE__).'/../Modal.php'; ?>