<div class="container">

    <div class="row">
        <div class="col-sm-12">
            <h2>Cadastrar solicitação</h2>
        </div>
        <div class="col-sm-12">
            <h2></h2>
        </div>
        <div class="row">
            <div class="col-sm-12">


                <div class="panel with-nav-tabs panel-default">
                    <div class="panel-heading">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab-beneficiado">Beneficiado</a></li>
                            <li><a data-toggle="tab" href="#tab-responsavel">Responsável</a></li>
                            <li><a data-toggle="tab" href="#tab-detalhamento">Detalhamento</a></li>
                            <li><a data-toggle="tab" href="#tab-bemcompat">Bem com patrimônio</a></li>
                            <li><a data-toggle="tab" href="#tab-bemsempat">Bem sem patrimônio</a></li>
                            <li><a data-toggle="tab" href="#tab-envio">Enviar</a></li>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">

                            <div id="tab-beneficiado" class="tab-pane fade in active">

                                <form class="form-horizontal" id="formBeneficiado">

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"> </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="beneficiado[busca]" class="col-sm-3 control-label">Beneficiado:</label>
                                        <div class="col-sm-8 form-inline">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="beneficiado[busca]" placeholder="Digite o CPF">
                                            </div>
                                            <button type="button" class="btn btn-success" id="btn-buscarBeneficiado" name="btn-buscarBeneficiado"><span class="glyphicon glyphicon-zoom-in" aria-hidden="false"></span> Buscar</button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="beneficiado[nome]" class="col-sm-3 control-label">Nome:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="beneficiado[nome]" value="" class="form-control" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="beneficiado[unidade]" class="col-sm-3 control-label">Unidade:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="beneficiado[unidade]" value="" class="form-control" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"> </label>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-success btn-md" name="btn-aba-beneficiado"><i class="fa fa-arrow-right"></i> Avançar</button>
                                        </div>
                                    </div>

                                </form>
                            </div>

                            <div id="tab-responsavel" class="tab-pane fade in">

                                <form class="form-horizontal" id="formResponsavel">

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"> </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="responsavel[busca]" class="col-sm-3 control-label">Responsável:</label>
                                        <div class="col-sm-8 form-inline">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="responsavel[busca]" placeholder="Digite o CPF">
                                            </div>
                                            <button type="button" class="btn btn-success" id="btn-buscarResponsavel" name="btn-buscarResponsavel"><span class="glyphicon glyphicon-zoom-in" aria-hidden="false"></span> Buscar</button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="responsavel[nome]" class="col-sm-3 control-label">Nome:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="responsavel[nome]" value="" class="form-control" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="responsavel[unidade]" class="col-sm-3 control-label">Unidade:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="responsavel[unidade]" value="" class="form-control" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"> </label>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-success btn-md" name="btn-aba-responsavel"><i class="fa fa-arrow-right"></i> Avançar</button>
                                        </div>
                                    </div>

                                </form>
                            </div>

                            <div id="tab-detalhamento" class="tab-pane fade in">
                                <form class="form-horizontal" id="formSolicitacoes">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"> </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="usuario[centro]" class="col-sm-3 control-label">Centro: </label>
                                        <div class="col-sm-8">
                                            <select name="usuario[centro]" class="form-control select-picker">
                                                <option value="">Selecione</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="usuario[setor]" class="col-sm-3 control-label">Setor: </label>
                                        <div class="col-sm-8">
                                            <select name="usuario[setor]" class="form-control select-picker">
                                                <option value="">Selecione</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="categoria" class="col-sm-3 control-label">Categoria: </label>
                                        <div class="col-sm-8">
                                            <select name="categoria" class="form-control select-picker" required>
                                                <option value="">Selecione</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="dtHrDevolucao" class="col-sm-3 control-label">Devolução:</label>
                                        <div class="col-sm-8 form-inline">
                                            <div class="input-group datepicker">
                                                <input type="text" name="dtDevolucao" size="8" maxlength="10" id="dtDevolucao" value="" class="form-control" placeholder="Data">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-success btn-md" name="btn-aba-detalhamento"><i class="fa fa-arrow-right"></i> Avançar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div id="tab-bemcompat" class="tab-pane fade in">
                                <form class="form-horizontal" id="formBemComPat">
                                    <div class="row" id="listagemBemComPat" style="margin-bottom:30px;">
                                        <div class="col-sm-1">
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="btn-group pull-right">
                                                <button type="button" class="btn btn-success btn-md" name="btn-aba-bemcompat-add"><i class="fa fa-plus"></i> Adicionar</button>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <table id="tabelaBemComPat" class="table table-bordered table-hover dataTable dtr-inline" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="col-xs-2">Id</th>
                                                        <th class="col-xs-2">Código</th>
                                                        <th class="col-xs-7">Descrição</th>
                                                        <th class="col-xs-1 text-center">Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-success btn-md" name="btn-aba-bemcompat"><i class="fa fa-arrow-right"></i> Avançar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div id="tab-bemsempat" class="tab-pane fade in">
                                <form class="form-horizontal" id="formBemSemPat">
                                    <div class="row" id="listagemBemSemPat" style="margin-bottom:30px;">
                                        <div class="col-sm-1">
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="btn-group pull-right">
                                                <button type="button" class="btn btn-success btn-md" name="btn-aba-bemsempat-add"><i class="fa fa-plus"></i> Adicionar</button>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <table id="tabelaBemSemPat" class="table table-bordered table-hover dataTable dtr-inline" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="col-xs-2">Id</th>
                                                        <th class="col-xs-2">Código</th>
                                                        <th class="col-xs-7">Descrição</th>
                                                        <th class="col-xs-1 text-center">Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-success btn-md" name="btn-aba-bemsempat"><i class="fa fa-arrow-right"></i> Avançar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>


                            <div id="tab-envio" class="tab-pane fade in">

                                <form class="form-horizontal" id="formEnvio">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"> </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="observacao" class="col-sm-3 control-label">Observação:</label>
                                        <div class="col-sm-8">
                                            <textarea id="observacao" maxlength="200" name="observacao" class="form-control" rows="3" placeholder="Caso queira deixar algum comentário" require></textarea>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-success btn-md" name="btn-aba-enviar"><i class="glyphicon glyphicon-floppy-disk"></i> Enviar solicitação</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="bemcompat-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="titulo-modal" aria-hidden="true" data-backdrop="static">
    <form name="formBemComPat" class="validator form-horizontal" role="form" data-toggle="validator">
        <!-- Alterado: Reduzi a largura para 80% e max-width para 1000px -->
        <div class="modal-dialog modal-xl" role="document" style="width: 80%; max-width: 800px; margin: 0 auto;">
            <div class="modal-content">
                <!-- Cabeçalho do Modal -->
                <div class="modal-header">
                    <h3 class="modal-title text-center">Bem com patrimônio</h3>
                </div>

                <!-- Busca Bem com Patrimônio -->
                <div class="row" id="buscaBemComPat" style="margin: 15px;">
                    <div class="col-sm-12">
                        <div class="form-group row align-items-center">
                            <!-- Ajuste: Justifiquei o label à direita com a classe text-right -->
                            <label for="bemcompat[busca]" class="col-sm-1 col-form-label text-right" style="padding-right: 5px;">Item:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="bemcompat[busca]" id="bemcompat[busca]" style="padding-left: 5px;">
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-success btn-block" id="btn-buscar-bemcompat" name="btn-buscar-bemcompat">
                                    <span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Listagem de Itens -->
                <div class="row" id="listagemBemComPat" style="margin: 15px;">
                    <div class="col-sm-12">
                        <!-- Adicionei a classe table-responsive para garantir que a tabela seja rolável em telas pequenas -->
                        <div class="table-responsive">
                            <table id="tabelaBemComPat2" class="table table-bordered table-hover dataTable dtr-inline" width="100%">
                                <thead>
                                    <tr>
                                        <th class="col-xs-1">Código</th>
                                        <th class="col-xs-10">Descrição</th>
                                        <th class="col-xs-1 text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Conteúdo dinâmico da tabela -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Rodapé do Modal -->
                <div class="modal-footer">
                    <!-- Alterado: Adicionei data-dismiss="modal" para fechar o modal -->
                    <button type="button" class="btn btn-default" name="btn-fechar-bemcompat" data-dismiss="modal">
                        <span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> Fechar
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="bemsempat-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="titulo-modal" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl" role="document" style="width: 80%; max-width: 800px; margin: 0 auto;">
        <div class="modal-content">
            <!-- Cabeçalho do Modal -->
            <div class="modal-header">
                <h3 class="modal-title text-center">Bem sem patrimônio</h3>
            </div>

            <!-- Busca Bem sem Patrimônio -->
            <div class="row" id="buscaBemSemPat" style="margin: 15px;">
                <div class="col-sm-12">
                    <div class="form-group row align-items-center">
                        <!-- Ajuste: Justifiquei o label à direita com a classe text-right -->
                        <label for="bemsempat[busca]" class="col-sm-1 col-form-label text-right" style="padding-right: 5px;">Item:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="bemsempat[busca]" id="bemsempat[busca]" style="padding-left: 5px;">
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-success btn-block" id="btn-buscar-bemsempat" name="btn-buscar-bemsempat">
                                <span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span> Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Listagem de Itens -->
            <div class="row" id="listagemBemSemPat" style="margin: 15px;">
                <div class="col-sm-12">
                    <!-- Adicionei a classe table-responsive para garantir que a tabela seja rolável em telas pequenas -->
                    <div class="table-responsive">
                        <table id="tabelaBemSemPat2" class="table table-bordered table-hover dataTable dtr-inline" width="100%">
                            <thead>
                                <tr>
                                    <th class="col-xs-1">Código</th>
                                    <th class="col-xs-10">Descrição</th>
                                    <th class="col-xs-1 text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Conteúdo dinâmico da tabela -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Rodapé do Modal -->
            <div class="modal-footer">
                <!-- Alterado: Adicionei data-dismiss="modal" para fechar o modal -->
                <button type="button" class="btn btn-default" name="btn-fechar-bemsempat" data-dismiss="modal">
                    <span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<?php require dirname(__FILE__) . '/../Modal.php'; ?>