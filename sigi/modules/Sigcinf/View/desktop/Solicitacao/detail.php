<div class="container">

    <div class="row">
        <div class="col-lg-12">
            <h2>Detalhamento da Solicitação</h2>
        </div>
        <div class="col-lg-12">
            <h2></h2>
        </div>



        <div class="form-group">
            <div class="row">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-sm-12 form-inline">
                            <div class="input-group">
                            </div>
                            <div class="well well-sm form-inline text-left" class="form-control w-100">


                                <div class="form-group">
                                    <label for="solicitacao[seqAno]" class="col-xs-4 control-label">Sequencial/Ano:</label>
                                    <div class="col-xs-4">
                                        <input type="text" name="solicitacao[seqAno]" value="" class="form-control disabled" readonly />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="solicitacao[status]" class="control-label col-xs-2">Status: </label>
                                    <div class="col-xs-10">
                                        <input type="text" name="solicitacao[status]" value="" class="form-control" readonly/>
                                    </div>
                                </div>

                            </div>
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
                                            <div class="col-xs-10">
                                                <input type="text" name="solicitacao[cadastrador]" value="" class="form-control" readonly/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">            
                                        <div class="form-group">
                                            <label for="solicitacao[beneficiado]" class="control-label col-xs-2">Beneficiado: </label>
                                            <div class="col-xs-10">
                                                <input type="text" name="solicitacao[beneficiado]" value="" class="form-control" readonly/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">            
                                        <div class="form-group">
                                            <label for="solicitacao[responsavel]" class="control-label col-xs-2">Responsável: </label>
                                            <div class="col-xs-10">
                                                <input type="text" name="solicitacao[responsavel]" value="" class="form-control" readonly/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="solicitacao[dtDevolucao]" class="col-xs-4 control-label">Data de devolução:</label>
                                            <div class="col-xs-4">
                                                <input type="text" name="solicitacao[dtDevolucao]" value="" class="form-control disabled" readonly />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">            
                                        <div class="form-group">
                                            <label for="solicitacao[categoria]" class="control-label col-xs-2">Categoria: </label>
                                            <div class="col-xs-10">
                                                <input type="text" name="solicitacao[categoria]" value="" class="form-control" readonly/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">            
                                        <div class="form-group">
                                            <label for="solicitacao[centro]" class="control-label col-xs-2">Centro: </label>
                                            <div class="col-xs-4">
                                                <input type="text" name="solicitacao[centro]" value="" class="form-control" readonly/>
                                            </div>
                                            <label for="solicitacao[setor]" class="control-label col-xs-2">Setor: </label>
                                            <div class="col-xs-4">
                                                <input type="text" name="solicitacao[setor]" value="" class="form-control" readonly/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="solicitacao[observacao]" class="control-label col-xs-2">Observação:</label>
                                            <div class="col-xs-10">
                                                <textarea type="text" name="solicitacao[observacao]" value="" class="form-control" rows="2" readonly></textarea>
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
                                                        <th width="20%">Codigo</th>
                                                        <th width="80%">Descricao</th>
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
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require dirname(__FILE__).'/../Modal.php'; ?>

