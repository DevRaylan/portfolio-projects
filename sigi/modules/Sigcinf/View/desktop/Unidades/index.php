<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h2>Unidades UDESC</h2>
        </div>
        <div class="row">
            <div class="row">
                <div class="col-md-12">
                    <div class="well well-sm form-inline text-right">
                        <button type="button" class="btn btn-success btn-md" name="btNovo"><i class="fas fa-plus"></i> Nova unidade</button>
                    </div>
                </div>
            </div>
            <div class="row" id="listagemUnidades" style="margin-bottom:30px;">
                <div class="col-sm-12">
                    <table id="tabelaUnidades" class="table table-bordered table-hover dataTable dtr-inline" width="100%">
                        <thead>
                            <tr>
                                <th class="col-xs-3">Abreviatura</th>
                                <th class="col-xs-8">Nome</th>
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
<div class="modal fade" id="modalformUnidades" tabindex="-1" role="dialog" aria-labelledby="modalformUnidadesLabel" data-backdrop="static">
    <form name="formUnidades" class="validator form-horizontal" role="form" data-toggle="validator">   
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Unidades UDESC</h3>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="unidadeudesc[id]" value="0"/>
                    <div class="form-group">
                        <label for="unidadeudesc[abrev]" class="col-sm-4 control-label">Abreviatura:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="unidadeudesc[abrev]" maxlength="10" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="unidadeudesc[nome]" class="col-sm-4 control-label">Nome:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="unidadeudesc[nome]" maxlength="60" required>
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