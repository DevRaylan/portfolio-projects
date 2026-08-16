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
                    url('/imagens/<?=$GLOBALS['url'] -> getUrlModule();?>/loading.gif')
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
    #tabelaConfigs .dt-middle {
        vertical-align: middle;
    }
    h1 {
        font-size: 36px;
        color: inherit;
    }
</style>

<div class="container">
    <div class="page-header">
        <h1><button type="button" onclick="showFormConfig()" class="btn btn-lg btn-info pull-right"> Nova configuração </button>Gerenciamento de conexões a servidores de arquivos</h1>
    </div>

    <p>Este módulo faz o gerenciamento das configuração de acesso a servidores de arquivos utilizados pelo sistema. Exemplo: servidores ftp.</p>
    
    <div id="ativos" class="tab-pane fade in active">
        <table id="tabelaConfigs" class="table table-striped table-hover table-bordered tabela-servicos dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th class="col-sm-1">Id</th>
                    <th class="col-sm-6">Descrição</th>
                    <th class="col-sm-2">Host</th>
                    <th class="col-sm-1">Port</th>
                    <th class="col-sm-1">Type</th>
                    <th class="col-sm-1">Opções</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>
    var configs = <?= $configs ?>;
    
    function getConfig(id) {
        var iConfig = -1;
        $.each(configs, function(i, config) {
            if(config.id == id) {
                iConfig = i;
                return false;
            }
        });
        
        return iConfig;
    }
    
    $(document).ready(function() {
        tabelaConfigs = $('#tabelaConfigs').DataTable( {
            data: configs,
            "columns": [
                {
                    "data": "id",
                    className: 'text-right dt-middle'
                },
                {"data": "descricao"},
                {
                    "data": "host",
                    className: 'text-left dt-middle'
                },
                {
                    "data": "port",
                    className: 'text-right dt-middle'
                },
                {"data": "type"},
                {
                    className: "text-center dt-middle",
                    render: function ( row, type, val, meta ) {
                        return "<span class='btn btn-warning btn-sm glyphicon glyphicon-pencil' onclick='showFormConfig(\""+val.id+"\")'></span>\
                                <span class='btn btn-danger btn-sm glyphicon glyphicon-trash' onclick='removeConfig(\""+val.id+"\")'></span>";
                    }
                }
            ],
            columnDefs: [
                { className: 'dt-middle', targets: [1, 4] },
            ],
            "order": [[0, 'asc']]
        } );

        $("#formConfig").submit(function(e){
            e.preventDefault();
            saveConfig();
        });
    });
</script>

<script>
// CRUD ========================================================================

    function saveConfig() {
        var data = {
                id : $("#inId").val(),
                descricao : $("#inDescricao").val(),
                host : $("#inHost").val(),
                port : $("#inPort").val(),
                type : $("#inType").val(),
                user : $("#inUser").val(),
                password : $("#inPassword").val(),
                rootDir : $("#inRootDir").val()
            },
            url = '<?= $this -> url -> getUrlController() ?>/saveConfig';
            
        $("#modalFormConfig").modal('hide');
        $("#loading-modal").show();

        $.ajax({
            url: url,
            type: "POST",
            data: data,
            dataType: "json"
        }).done(function(response) {
            if(response.result == 'success') {
                iConfig = getConfig(response.config.id);
                
                if(iConfig > -1) {
                    configs[iConfig] = response.config;
                } else {
                    configs.push(response.config);
                }
                
                updateListConfigs();
            }
            
            $("#loading-modal").hide();
            showAlert(response.result, response.msg);
        }).fail(function(data) {
            showAlert("error", data.responseText);
        }).always(function() {
            $("#loading-modal").hide();
        });
    }
    
    function removeConfig(id, confirmed) {
        if(!confirmed && !confirm('Deseja excluir esta configuração?', function() {removeConfig(id, true)})) {
            return false;
        }
        $("#loading-modal").show();
        
        $.ajax({
            url: "<?= $this->url->getUrlController() ?>/removeConfig/"+id,
            type: "GET",
            dataType: "json"
        }).done(function(data) {
            if(data.result == "success") {
                configs = jQuery.grep(configs, function(config) {
                  return config.id != id;
                });
                
                updateListConfigs();
            }
            $("#loading-modal").hide();
            showAlert(data.result, data.msg);
        }).fail(function(data) {
            showAlert("error", data.responseText);
        }).always(function() {
            $("#loading-modal").hide();
        });
    }
// FIM CRUD ====================================================================

// VIEW e FORMS ================================================================

    function showFormConfig(id) {
        if(id) {
            var iConfig = getConfig(id),
                config = configs[iConfig];
        } else {
            config = {
                descricao : '',
                host : '',
                port : '',
                type : '',
                user : '',
                password : '',
                rootDir : ''
            }
        }
        
        $("#formConfig #inId").val(config.id);
        $("#formConfig #inDescricao").val(config.descricao);
        $("#formConfig #inHost").val(config.host);
        $("#formConfig #inPort").val(config.port);
        $("#formConfig #inType").val(config.type);
        $("#formConfig #inUser").val(config.user);
        $("#formConfig #inPassword").val(config.password);
        $("#formConfig #inRootDir").val(config.rootDir);
        
        $("#modalFormConfig").modal();
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
    function updateListConfigs() {
        $('#tabelaConfigs').dataTable().fnClearTable();

        if(configs.length) {
            $('#tabelaConfigs').dataTable().fnAddData(configs);
        }
    }
</script>

<!-- div com formulario de novo/editar -->
<div class="modal fade" id="modalFormConfig" tabindex="-1" role="dialog" aria-labelledby="tituloModal" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="formConfig" class="form-horizontal" action="" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title text-center" id="tituloModal">Nova configuração</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="form-group">
                            <label for="descricao" class="control-label col-sm-4">Descricao: </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="descricao" id="inDescricao" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="descricao" class="control-label col-sm-4">Ip do servidor: </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="inHost" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="descricao" class="control-label col-sm-4">Porta: </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="inPort" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="descricao" class="control-label col-sm-4">Tipo: </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="inType" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="descricao" class="control-label col-sm-4">Diretório root: </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="inRootDir" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="descricao" class="control-label col-sm-4">Usuário: </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="inUser" required />
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="descricao" class="control-label col-sm-4">Senha: </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="inPassword" required />
                            </div>
                        </div>
                    </div>
                </div><!-- end modal-body -->
                <div class="modal-footer">
                    <input type="hidden" id="inId" value="" />
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancelar</button>
                    <button type="submit" class="btn btn-primary"> Salvar </button>
                </div>
            </form>
        </div>
    </div>
</div><!-- end form modalAddTransaction -->

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
