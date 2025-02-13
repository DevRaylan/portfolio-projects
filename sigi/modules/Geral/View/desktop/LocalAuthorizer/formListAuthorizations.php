<style>

    .modal-loading {
        display:    none;
        position:   fixed;
        z-index:    1000;
        overflow: hidden;
        top:        0;
        left:       0;
        height:     100%;
        width:      100%;
        background: rgba( 255, 255, 255, .8 )
                    url('/imagens/Geral/loading.gif')
                    no-repeat
                    50% 50%;
    }
    .tab-content {
        margin-top: 10px;
    }

    .legenda {
        margin-bottom: 10px;
    }
    .row.legenda .glyphicon {
        font-size: x-large;
    }
    .texto-legenda {
        vertical-align: super;
    }
    .panel-heading {
        cursor: pointer;
    }
    #alertaToast {
        position: fixed;
        top: 47px;
        left: 50%;
        transform: translate(-50%,0);
        display: none;
    }
    #alertaToast .close {
        margin-left: 7px;
    }
    #msgLoja {
        resize: none;
    }
    h1 {
        font-size: 36px;
        color: inherit;
    }
    input[type=checkbox] {
        cursor:pointer;
    }
</style>

<div class="container">
    <div class="page-header">
        <h1><button type="button" onclick="formNewUser()" class="btn btn-lg btn-info pull-right"> Novo usuário </button>Gerenciamento de Autorizações</h1>
    </div>

    <p>Este módulo faz o gerenciamento das transações que os usuários podem realizar nas aplicações.</p>
    <p>* Usuários sem transações são excluídos da lista.</p>
    <p><a href="#" data-toggle="collapse" data-target="#question_1" aria-expanded="true">Como configurar as transações da aplicação?</a></p>
    <div class="collapse" id="question_1">
    As transações de cada aplicação são configuradas no arquivo <strong>config.php</strong> utilizando a variável <strong>$config["transactions"]</strong> (array contendo todas as transações).</p>
    <p>Exemplo: Configurar transações de usuários para a aplicação <strong>Geral</strong>.</p>
    <pre>// arquivo config.php da aplicação geral (/modules/Geral/config/config.php).
// As transações da aplicação Geral são: admin e view.
$config[transactions] = [
    'admin' => [
        'titulo' => 'Administrador da aplicação.',
        'descricao' => 'Esta transação permite que o usuário realize qualquer tipo de ação na aplicação.'
    ],
    'view' => [
        'titulo' => 'Visualizar registros da aplicação.',
        'descricao' => 'Esta transação permite que o usuário faça apenas operações de consulta na aplicação.'
    ]
];
...
</pre>
    <br />
    <p><strong>Obs:</strong> Os valores "admin" e "view" são utilizados para a verificação da transação com o método <strong>isAllowedTransactions</strong> da classe <strong>Authorization</strong>.</p>
    <p>Exemplo de utilização:</p>
    <pre>
// Ao estender um Controller, a classe Authorization já estará instanciada na variável de contexto $this -> auth
// Verificar se o usuário pode realizar as transações "admin" e "editar"
if(!$this -> auth -> isAllowedTransactions(['admin', 'editar'])) {
    echo 'Usuário sem permissão para executar esta ação.';
    exit;
}
...
</pre>

    </div>

    <br />
    <div id="ativos" class="tab-pane fade in active">
        <table id="tabelaUsuarios" class="table table-striped table-hover table-bordered tabela-servicos dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th class="col-sm-1"><input type="checkbox" name="usuarios" onclick="selectAll(this)"></th>
                    <th class="col-sm-2">CPF</th>
                    <th class="col-sm-9">Nome</th>
                    <th class="col-sm-1">Opções</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <a class="btn btn-primary" href="#" onclick="autorizarUsuarios()">Aplicar autorizações aos usuários selecionados</a>
    </div>
</div>

<script>
    var transactionsByUser = <?= $transactionsByUser ?>,
        cpfsToName = <?= $cpfsToName ?>,
        tabelaUsuarios = '';

    $(document).ready(function() {
        tabelaUsuarios = $('#tabelaUsuarios').DataTable( {
            data: [],
            "language": {
                "url": '/libs/Geral/datatables/lang/Portuguese-Brasil.json'
            },
            "columns": [
                {
                    className: "text-center",
                    sortable: false,
                    render: function ( row, type, val, meta ) {
                        return '<input type="checkbox" name="users[]" value="'+val.cpf+'" />';
                    }
                },
                {
                    className: 'text-center',
                    render: function ( row, type, val, meta ) {
                        return "<a href='javascript: void(0)' title='Visualizar transações do usuário' onclick='showTransactions(\""+val.cpf+"\")'>"+val.cpf+"</a>";
                    }
                },
                {"data": "nome"},
                {
                    className: "text-center",
                    render: function ( row, type, val, meta ) {
                        return '<span class="btn btn-sm btn-danger glyphicon glyphicon-trash" onclick="confirm(\'Deseja remover este usuário?\', function() { removeUser(\''+val.cpf+'\')})" title="Excluir usuário"> </span>';
                    }
                }
            ],
            "order": [[0, 'desc']],
            "initComplete": function( settings, json ) {
                $(this).find('a').mask("000.000.000-00");
            }
        } );
        $('#tabelaUsuarios')
        updateListaUsuarios();

        $("#formNewUser #inCpf").mask('000.000.000-00');
        $("#formNewUser").submit(function(e){
            e.preventDefault();
            var cpf = $(this).find("#inCpf").val();
            $("#modalNewUser").modal('hide');
            jQuery('#modalNewUser').on('hidden.bs.modal', function (e) {
                jQuery('body').addClass('modal-open');
            });
            showTransactions(cpf);
        });

        $("#formTransactions").submit(function(e){
            e.preventDefault();

            saveTransactions($("#formTransactions #inCpf").val(), $("#formTransactions").serialize());
        });
        
        $("input[type=checkbox]").click(function(e){
            e.stopPropagation();
        });
        
        $(".todos-nenhum").click(function () {
            $("input[name='transactionsByApp["+$(this).attr('data-app')+"][]']").prop('checked', $(this).prop('checked'));
            $(this).closest('a').find(".todos-label").toggleClass('hidden');
            $(this).closest('a').find(".nenhum-label").toggleClass('hidden');
        });

    });
</script>

<script>
// CRUD ========================================================================

    function saveTransactions(cpf, urlEncodedFormData) {
        cpf = getOnlyDigits(cpf);
        $("#loadingModal").show();

        $.ajax({
            url: udev.url.getUrlController() + "/saveTransactionsByCpf",
            type: "POST",
            data: urlEncodedFormData,
            dataType: "json"
        }).done(function(data) {
            if(data.result == "success") {
                if(typeof data.transactionsByApp == "undefined") {
                    delete transactionsByUser[cpf];
                } else {
                    transactionsByUser[cpf] = data.transactionsByApp;
                }
                
                // Refresh
                document.location = udev.url.getUrlAction();
            }

            $("#modalTransactions").modal('hide');
            showAlert(data.result, data.msg);
        }).fail(function(data) {
            showAlert("error", data.responseText);
        }).always(function() {
            $("#loadingModal").hide();
        });
    }

    function removeUser(cpf) {
        saveTransactions(cpf, 'cpf='+cpf);
    }
// FIM CRUD ====================================================================

// VIEW e FORMS ================================================================

    // autorizações em lote
    function selectAll(item) {
        if($(item).is(":checked")){
            $('input[name="users[]"]').each(function() {
                $(this).prop('checked', true);
            });
        }else{
            $('input[name="users[]"]').each(function() {
                $(this).prop('checked', false);
            });
        }
    }

    function autorizarUsuarios() {

        var dados = [];
        var cpfs = '';

        $('input[name="users[]"]:checked').each(function() {
            dados.push($(this).val());
            cpfs += $(this).val() + ', ';
        });

        $(".todos-label").removeClass("hidden");
        $(".nenhum-label").addClass("hidden");
        $('#modalTransactions #spanCpf').html(cpfs);
        $("#modalTransactions #inCpf").val([dados]);
        $("#modalTransactions input:checkbox").prop("checked", false);

        $("#modalTransactions").modal();
    }

    function formNewUser() {
        $("#formNewUser #inCpf").val('');
        $("#modalNewUser").modal();
    }

    function showTransactions(cpf) {
        $(".todos-label").removeClass("hidden");
        $(".nenhum-label").addClass("hidden");
        cpf = getOnlyDigits(cpf);
        $("#modalTransactions #inCpf").val(cpf);
        $('#modalTransactions #spanCpf').html(cpf).mask('000.000.000-00');
        $("#modalTransactions input:checkbox").prop("checked", false);

        if(transactionsByUser[cpf]) {
            $.each(transactionsByUser[cpf], function(app, transactions) {
                $.each(transactions, function(i, object) {
                    $("#formTransactions input[name='transactionsByApp\\["+app+"\\]\\[\\]'][value='"+object.transaction+"']").prop("checked", true);
                });
            });
        }

        $("#modalTransactions").modal();
    }

    function showRemoveConfirm(id) {
        $("#idConfirm").val(id);
        $("#removeConfirmModal").modal({
            backdrop: 'static',
            keyboard: false
        });
    }

    function showAlert(tipo, msg){
        var classeAlerta;
        if (tipo == "success") {
            classeAlerta = 'alert-success';
        } else if (tipo = "error") {
            classeAlerta = 'alert-danger';
        } else {
            die('Tipo de alerta não existente.');
        }
        $("#alertaToast").removeClass("alert-success alert-danger").addClass(classeAlerta);
        $("#alertaToast strong").text(msg);
        $("#alertaToast").fadeTo(500, .8).delay(2500).slideUp('slow');
    }

    function confirm(msg, _callback) {
        $("#modalConfirm #msg").html(msg);
        $("#modalConfirm #onClick").off('click').on('click', _callback);
        $("#modalConfirm").modal();
    }

// FIM VIEW e FORMS ============================================================

// OUTROS ======================================================================
    function popularSelect(id, valores)
    {
        $(id).empty().append('<option value=""> </option>');

        $.each(valores, function(val, desc) {
            $(id).append('<option value="'+desc+'">'+desc+'</option>');
        })
    }

    function getOnlyDigits(val)
    {
        return val ? val.replace(/[^0-9]/g, '') : null;
    }

    function updateListaUsuarios()
    {
        users = [];

        $.each(transactionsByUser, function(cpf, transactions) {
            //users.push({'cpf' : cpf, 'nome' : usersData[cpf]});
            // Utilizar o cpf para pegar o respectivo nome. Implementação futura, por enquanto só mostra CPF.
            users.push({'cpf' : cpf, 'nome' : cpfsToName[cpf]});
        });

        $('#tabelaUsuarios').dataTable().fnClearTable();

        if(users.length) {
            $('#tabelaUsuarios').dataTable().fnAddData(users);
        }
    }

    $.fn.serializaObjeto = function() {
        var obj = {};
        var arr = this.serializeArray();
        $.each(arr, function() {
            if (obj[this.name] !== undefined) {
                if (!obj[this.name].push) {
                    obj[this.name] = [obj[this.name]];
                }
                obj[this.name].push(this.value || '');
            } else {
                obj[this.name] = this.value || '';
            }
        });
        return obj;
    };
</script>

<!-- div com formulario de novo/editar -->
<div class="modal fade" id="modalNewUser" tabindex="-1" role="dialog" aria-labelledby="tituloModal" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="formNewUser" class="form-horizontal" action="" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title text-center" id="tituloModal">Novo usuário</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="form-group">
                            <label for="descricao" class="control-label col-sm-4">CPF *: </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="cpf" id="inCpf" required>
                            </div>
                        </div>
                    </div>
                </div><!-- end modal-body -->
                <div class="modal-footer">
                    <input type="hidden" id="inCpf" value="" />
                    <button type="submit" class="btn btn-primary"> Adicionar </button>
                    <button class="btn btn-default pull-left" data-dismiss="modal" type="button">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- end form modalAddTransaction -->

<!-- div com a lista de transações do usuário -->
<div class="modal fade" id="modalTransactions" tabindex="-1" role="dialog" aria-labelledby="tituloModalTransactions" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formTransactions">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="tituloModal">Autorizações do usuário</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <blockquote><p>Usuário: <span id="spanCpf"></<span></p></blockquote>
                        <div id="accordion" class="panel-group" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-default">
                                    <?php
                                    $i = 0;
                                    foreach($transactionsByApp as $app => $transactions) {
                                        ?>
                                        <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" data-target="#collapse-<?= $i ?>">
                                          <h4 class="panel-title">
                                                <?= $app ?>
                                            </h4>
                                        </div>

                                        <div id="collapse-<?= $i ?>" class="panel-collapse collapse<?=(($i==0)?' in':'');?>">
                                            <div class="panel-body">
                                                <div class="list-group">
                                                    <a href="#" class="list-group-item" onclick="$(this).find('input[type=checkbox]').click()">
                                                            <label style="cursor:pointer;" for="todas-nenhuma">
                                                                <input class="todos-nenhum" type="checkbox" name="todos-nenhum" value="todos" data-app="<?= $app ?>">                                                            
                                                            </label>
                                                            <span class="todos-label">Selecionar todas</span><span class="nenhum-label hidden">Limpar seleção</span>
                                                    </a>
                                                </div>
                                                <div class="list-group">
                                                <?php
                                                if(!isset($transactions['transactions'])) {
                                                    echo '<a href="#" class="list-group-item">Nenhum transação está definda para esta aplicação.</a>';
                                                } else {                                                
                                                    foreach($transactions['transactions'] as $transaction => $data) { ?>
                                				        <a href="#" class="list-group-item" onclick="$(this).find('input').click()">
                                                            <label style="cursor: pointer; margin-bottom: 5px" for="transactionsByApp[<?= $app ?>][]">
                                                                <input type="checkbox" name="transactionsByApp[<?= $app ?>][]" value="<?= $transaction ?>"  /> <?= $data['titulo'] ?>
                                                            </label>
                                                            <p><?= $data['descricao'] ?></p>
                                                        </a>
                                                    <?php }
                                                }?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                        $i++;
                                    } ?>
                            </div>
                        </div>
                    </div>
                </div><!-- end modal-body -->

                <div class="modal-footer">
                    <input type="hidden" id="inCpf" name="cpf" value="" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</a>
                </div>
            </form>
        </div>
    </div>
</div><!-- end form modalTransactions -->

<div id="modalConfirm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="tituloConfirmModal" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="tituloConfirmModal">Confirmação</h4>
            </div>
            <div class="modal-body">
                <p id="msg"> </p>
                <input id="idConfirm" type="hidden" name="idConfirm" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Não</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="onClick">Sim</a>
            </div>
        </div>
    </div>
</div><!-- end removeConfirmModal -->

<div id="alertaToast" class="alert">
    <a href="#" class="close" data-hide="alert" aria-label="close"> × </a>
    <span><strong></strong></span>
</div>

<div id="loadingModal" class="modal-loading"></div>
