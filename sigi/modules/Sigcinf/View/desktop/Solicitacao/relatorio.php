        <style>
            .panel-title {
                font-weight: bold;
                cursor: pointer;
            }
        </style>

        <!-- Conteúdo da Página -->
        <div class="container">

            <!-- Cabeçalho -->
            <div class="page-header">
                <h1 class="h3">Relatórios</h1>
            </div>

            <!-- Relatórios -->
            <div id="accordion" class="panel-group" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">

                    <div class="panel-heading collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#buscarCPF" aria-expanded="false">
                        <h4 class="panel-title">Buscar por CPF</h4>
                    </div>
                    <div id="buscarCPF" class="panel-collapse collapse" aria-expanded="false">
                        <div class="panel-body">
                            <div class="form-inline">
                                <input type="text" name="buscar" value="" class="form-control" style="width: 400px;" placeholder="Digite o CPF">
                                <button type="button" class="btn btn-success" id="btn-buscar" name="btn-buscar" onclick="buscar()">Buscar</button>
                            </div>
                        </div>
                    </div>


                    <!-- Buscar com Patrimônio -->
                    <div class="panel-heading collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#buscarComPatrimonio" aria-expanded="false">
                        <h4 class="panel-title">Buscar com Patrimônio</h4>
                    </div>
                    <div id="buscarComPatrimonio" class="panel-collapse collapse" aria-expanded="false">
                        <div class="panel-body">
                            <div class="form-inline">
                                <input type="text" name="buscar" value="" class="form-control" style="width: 200px;" placeholder="Digite o Patrimônio">
                                <button type="button" class="btn btn-success" id="btn-buscar2" name="btn-buscar2" onclick="buscar()">Buscar</button>
                            </div>
                            <div class="panel-heading collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseSolicitacoes" aria-expanded="false">
                            </div>
                        </div>
                    </div>


                    <!-- Buscar sem Patrimônio -->
                    <div class="panel-heading collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#buscarSemPatrimonio" aria-expanded="false">
                        <h4 class="panel-title">Buscar sem Patrimônio</h4>
                    </div>
                    <div id="buscarSemPatrimonio" class="panel-collapse collapse" aria-expanded="false">
                        <div class="panel-body">
                            <div class="form-inline">
                                <input type="text" name="buscar" value="" class="form-control" style="width: 200px;" placeholder="Digite a Descrição ">
                                <button type="button" class="btn btn-success" id="btn-buscar3" name="btn-buscar3" onclick="buscar()">Buscar</button>
                            </div>
                            <div class="panel-heading collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseSolicitacoes" aria-expanded="false">
                            </div>
                        </div>
                    </div>


                    <div id="collapseSolicitacoes" class="panel-collapse collapse" aria-expanded="false">
                        <div class="panel-body">
                            <div class="col-sm-14">
                                <h4 class="panel-title">Solicitações</h4>
                                <div id="listagemSolicitacoes" class="tab-pane fade in active">
                                    <div class="row" id="listagemtabelaSolicitacoes" style="margin-bottom:30px;">
                                        <div class="col-sm-12">
                                            <table id="tabelaSolicitacoes" class="table table-bordered table-hover dataTable dtr-inline" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="10%">Seq/Ano</th>
                                                        <th width="10%">Situação</th>
                                                        <th width="70%">Beneficiado</th>
                                                        <th width="70%">Responsável</th>
                                                        <th width="10%">Data da solicitação</th>
                                                        <th width="10%">Ações</th>
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

            </div>

            <script>
                function buscar() {
                    // Exibir ou ocultar a lista de solicitações ao clicar em buscar
                    var collapseElement = document.getElementById('collapseSolicitacoes');
                    if (collapseElement.classList.contains('collapse')) {
                        // Abrir a sub-seção (expandir)
                        $(collapseElement).collapse('show');
                    } else {
                        // Fechar a sub-seção (colapsar)
                        $(collapseElement).collapse('hide');
                    }


                    // Aqui você pode adicionar o código para realizar a busca (se necessário)
                    // Por exemplo, fazer uma requisição AJAX para trazer os dados e preencher a tabela.
                }
            </script>