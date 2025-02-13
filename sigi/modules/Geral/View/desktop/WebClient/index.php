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
    #modalFormWebClient .help-block {
        margin-bottom: 0px;
    }
    h1 {
        font-size: 36px;
        color: inherit;
    }
    input[type=checkbox] {
        cursor:pointer;
    }
    #tabelaWebClients td {
        vertical-align: middle;
    }
    #base64 {
        font-family: monospace;
        background-color: white;
    }
    #modalBase64 .input-group {
        margin-top: 15px;
    }
</style>

<div class="container">
    <div class="page-header">
        <h1><button type="button" onclick="showFormWebClient()" class="btn btn-lg btn-info pull-right"> Novo cliente </button>Gerenciamento de clientes Web</h1>
    </div>

    <p>Este módulo faz o gerenciamento dos sistemas clientes que acessam os serviços web (WebService) do framework.</p>
    
    <table id="tabelaWebClients" class="table table-striped table-hover table-bordered tabela-servicos dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="col-sm-5">Descrição</th>
                <th class="col-sm-2 text-center">Token</th>
                <th class="col-sm-2 text-center">Key</th>
                <th class="col-sm-2 text-center">Último acesso</th>
                <th class="col-sm-1 text-center">opções</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<script>
    var webClients = <?= $webClients ?>,
        fakeinput;
    
    function getWebClient(token) {
        var iWebClient = -1;
        $.each(webClients, function(i, webClient) {
            if(webClient.token == token) {
                iWebClient = i;
               return false;
            }
        });
        
        return iWebClient;
    }
    
    $(document).ready(function() {
        tabelaWebClients = $('#tabelaWebClients').DataTable( {
            data: webClients,
            "columns": [
                {
                    render: function ( row, type, val, meta ){
                        return '<a href="javascript:showHeader(\''+val.token+'\',\''+val.keySecret+'\');" title="Mostrar header">'+val.descricao+'</a>';
                    }
                    // "data": "descricao"
                },
                {"data": "token"},
                {"data": "keySecret"},
                {"data": "dtUltimoAcesso"},
                {
                    className: "text-center",
                    render: function ( row, type, val, meta ) {
                        if (val.emails == null) val.emails = '';
                        if (val.ips == null) val.ips = '';
                        return "<span class='btn btn-default btn-sm glyphicon glyphicon-transfer' title='Permissões da aplicação' onclick='showPermissions(\""+val.token+"\")'></span>\
                                <span class='btn btn-warning btn-sm glyphicon glyphicon-pencil' title='Editar cliente' onclick='showFormWebClient(\""+val.token+"\", \""+val.descricao+"\", \""+val.ips+"\", \""+val.emails+"\")'></span>\
                                <span class='btn btn-danger btn-sm glyphicon glyphicon-trash' title='Remover cliente' onclick='removeWebClient(\""+val.token+"\")'></span>";
                    }
                },
            ],
            "order": [[0, 'desc']]
        } );

        $("#formWebClient").submit(function(e){
            e.preventDefault();
            saveWebClient();
        });
        
        $("#form-permissions").submit(function(e){
            e.preventDefault();
            savePermissions();
        });
        
        $("input[type=checkbox]").click(function(e){
            e.stopPropagation();
        });
        
        $("#todas-nenhuma").click(function () {
            $("input[name='endpoint[]']").prop('checked', $(this).prop('checked'));
            $(".todas-label").toggleClass('hidden');
            $(".nenhuma-label").toggleClass('hidden');
        });
        
        $(".btn-copy").tooltip();
        $(".btn-copy").on('mouseout',function(){
            $(".btn-copy").tooltip('hide');
        });
    });
</script>

<script>
// CRUD ========================================================================

    function saveWebClient() {
        var ips = ($("#ips").val().trim()=="")?null:$("#ips").val(), 
            emails = ($("#emails").val().trim()=="")?null:$("#emails").val(),
            data = {
                token : $("#inToken").val(),
                descricao : $("#inDescricao").val(),
                ips : ips,
                emails : emails
            },
            url = '<?= $this -> url -> getUrlController() ?>/saveWebClient';
            
        $("#modalFormWebClient").modal('hide');
        $("#loadingModal").show();

        $.ajax({
            url: url,
            type: "POST",
            data: data,
            dataType: "json"
        }).done(function(response) {
            if(response.result == 'success') {
                iWebClient = getWebClient(response.webClient.token);
                
                if(iWebClient > -1) {
                    webClients[iWebClient] = response.webClient;
                } else {
                    webClients.push(response.webClient);
                }
                
                updateListWebClients();
            }
            
            $("#loadingModal").hide();
            showAlert(response.result, response.msg);
        }).fail(function(data) {
            showAlert("error", data.responseText);
        }).always(function() {
            $("#loadingModal").hide();
        });
    }
    
    function savePermissions() {
        $("#modal-permissions").modal('hide');
        $("#loadingModal").show();
        var endpoints = {}, getActions = [], postActions = [], deleteActions = [];
        $("input[name='endpoint[]']").each(function(i,v){
            if (this.checked) {                
                var method = $(this).attr('data-method');
                if (method == "GET") {
                    getActions.push( this.value);
                } else if (method == "POST") {
                    postActions.push( this.value);
                } else if (method == "DELETE") {
                    deleteActions.push( this.value);
                }
            }
        });
        endpoints["GET"] = getActions;
        endpoints["POST"] = postActions;
        endpoints["DELETE"] = deleteActions;
        $.ajax({
            url: "<?= $this->url->getUrlModule() ?>/WebClient/saveWebClientEndpoints/"+$("#client-token").val(),
            type: "POST",
            data: {endpoints:endpoints},
            dataType: "JSON"
        }).done(function(response){
            $("#loadingModal").hide();
            showAlert(response.result, response.msg);
        }).fail(function(data) {
            $("#loadingModal").hide();
            showAlert("error", data.responseText);
        }).always(function() {
            $("#loadingModal").hide();
        });
    }
    
    function removeWebClient(token, confirmed) {
        if(!confirmed && !confirm('Deseja excluir este cliente?', function() {removeWebClient(token, true)})) {
            return false;
        }
        
        $.ajax({
            url: "<?= $this->url->getUrlController() ?>/removeWebClient/"+token,
            type: "GET",
            dataType: "json"
        }).done(function(data) {
            if(data.result == "success") {
                webClients = jQuery.grep(webClients, function(webClient) {
                  return webClient.token != token;
                });
                
                updateListWebClients();
            } else {
                showAlert(data.result, data.msg);
            }
        }).fail(function(data) {
            showAlert("error", data.responseText);
        }).always(function() {
            $("#loadingModal").hide();
        });
    }
// FIM CRUD ====================================================================

// VIEW e FORMS ================================================================

    function showFormWebClient(token, descricao, ips, emails) {
        $("#formWebClient .modal-title").html(token?'Editar cliente':'Novo cliente');
        $("#formWebClient #inToken").val(token);
        $("#formWebClient #inDescricao").val(descricao);
        $("#formWebClient #ips").val(ips);
        $("#formWebClient #emails").val(emails);
        $("#modalFormWebClient").modal();
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
    
    function showHeader(token, key) {
        var base64 = "Authorization: Basic ";
        $("#base64").val("");
        if (token && key){
            base64 += btoa(token+":"+key);
            $("#base64").val(base64);
        }
        $("#modalBase64").modal();
    }
    
    function showPermissions(token) {
        $("#client-token").val(token);
        $("#loadingModal").show();
        $(".todas-label").removeClass("hidden");
        $(".nenhuma-label").addClass("hidden");
        var urls, method, input;
        $.ajax({
            url: "<?= $this->url->getUrlModule() ?>/WebClient/getWebClientEndpoints/"+token,
            type: "POST",
            data: {
                token: token
            },
            dataType: "JSON"
        }).done(function(data){
            if(data.result == 'success'){
                $('input[type=checkbox]').attr('checked', false);
                urls = data.endpoints;
                $.each(urls, function(i, meth){
                    method = i;
                    $.each(meth, function(j, val){
                        $('input[value="'+val+'"]').each(function(i, v){
                            $input = $(this);
                            if ($input.attr("data-method")==method) {
                                $input.prop('checked', true);
                            }
                        });
                    })
                });
                $("#modal-permissions").modal();
            } else {
                showAlert("error", data.msg);
            }
        }).fail(function(data) {
            showAlert("error", data.responseText);
        }).always(function() {
            $("#loadingModal").hide();
        });
    }

// FIM VIEW e FORMS ============================================================

// OUTROS ======================================================================
    function updateListWebClients() {
        $('#tabelaWebClients').dataTable().fnClearTable();

        if(webClients.length) {
            $('#tabelaWebClients').dataTable().fnAddData(webClients);
        }
    }
    
    function copyHeader(btn) {   
        var txt2copy = $(btn).closest('div.input-group').find('input').val();
        
        if (!fakeinput) {
            fakeinput = $('<input type="text"/>').css({
                position: "absolute",
                left: "-1000px",
                top: "-1000px"
            });
            $("#modalBase64").append(fakeinput);     
        }
          
        fakeinput.val(txt2copy);        
        fakeinput.select();        
        var ok = document.execCommand("Copy");        
        if (ok) {
            $(btn).tooltip('toggle');
        } 
    }
</script>

<!-- div com formulario de novo/editar -->
<div class="modal fade" id="modalFormWebClient" tabindex="-1" role="dialog" aria-labelledby="tituloModal" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="formWebClient" class="form-horizontal" action="" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title text-center" id="tituloModal">Novo cliente</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="form-group">
                            <label for="ips" class="control-label col-sm-3">IPs: </label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="ips" id="ips"/>
                                <p class="help-block"><small>Separados por vírgula.</small></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="descricao" class="control-label col-sm-3">Descrição: </label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="descricao" id="inDescricao" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="emails" class="control-label col-sm-3">E-mail(s): </label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="emails" id="emails"/>
                                <p class="help-block"><small>Separados por vírgula.</small></p>
                            </div>
                        </div>
                    </div>
                </div><!-- end modal-body -->
                <div class="modal-footer">
                    <input type="hidden" name="token" id="inToken" value="" />
                    <button type="submit" class="btn btn-primary"> Salvar </button>
                    <button class="btn btn-default pull-left" data-dismiss="modal" type="button">Cancelar</button>
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

<div id="modalBase64" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="tituloBase64Modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="tituloBase64Modal">Header da Requisição</h4>
            </div>
            <div class="modal-body form-horizontal">
                <div class="form-group">
                    <div class="input-group col-sm-offset-1 col-sm-10">
                        <input type="text" value="teste" class="form-control" name="base64" id="base64" readonly />
                        <div class="input-group-btn">
                            <button class="btn btn-default btn-copy" type="button" onclick="copyHeader($(this));" data-toggle="tooltip" 
                            data-trigger="manual" data-placement="bottom" title="Copiado!">
                                <i class="glyphicon glyphicon-copy"></i> Copiar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div><!-- end modalBase64 -->

<div id="modal-permissions" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="titulo-permissions" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-permissions">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="titulo-permissions">Permissões da Aplicação</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div id="accordion-permissions" class="panel-group">
                            <div class="panel panel-default">
                                <?php 
                                $i = 0;
                                $labelColorsClasses = ["GET"=>"label-success", "POST"=>"label-warning", "DELETE"=>"label-danger"];
                                foreach($endpointsByApp as $app => $urls) {
                                ?>
                                    <div class="panel-heading" data-toggle="collapse" data-parent="#accordion-permissions" data-target="#collapse-<?= $i ?>">
                                        <h4 class="panel-title">
                                            <?= $app ?>
                                        </h4>
                                    </div>
                                    
                                    <div id="collapse-<?= $i ?>" class="panel-collapse collapse<?=(($i==0)?' in':'');?>">
                                        <div class="panel-body">
                                            <?php    
                                            if(empty($urls)) {
                                                echo '<em>Nenhum endpoint defindo para esta aplicação.</em>';
                                            } else { ?>             
                                            <div class="list-group">
                                                <a href="#" class="list-group-item" onclick="$(this).find('input[type=checkbox]').click()">
                                                        <label style="cursor:pointer;" for="todas-nenhuma">
                                                            <input id="todas-nenhuma" type="checkbox" name="todas-nenhuma" value="todos">                                                             
                                                        </label>
                                                        <span class="todas-label">Selecionar todas</span><span class="nenhuma-label hidden">Limpar seleção</span>
                                                </a>
                                            </div>                                  
                                            <div class="list-group">
                                                <?php    
                                                    foreach($urls as $method => $urls) {
                                                        foreach($urls as $url => $details) {
                                                ?>
                                                    <a href="#" class="list-group-item" onclick="$(this).find('input[type=checkbox]').click()">
                                                        <label style="cursor:pointer; margin-bottom: 5px;" for="endpoint[]">
                                                            <input type="checkbox" data-method="<?=$method?>" name="endpoint[]" value="<?=$url?>"> 
                                                            <span class="label <?=$labelColorsClasses[$method]?>"><?=$method?></span> <?= $url ?>
                                                        </label>
                                                        <p><strong><?= $details['titulo'] ?></strong><br><?= $details['descricao'] ?></p>
                                                    </a>
                                                    <?php 
                                                        }
                                                    }?>
                                            </div>
                                            <?php
                                            }?>
                                        </div>
                                    </div>
                                <?php 
                                $i++;
                                }?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="client-token" name="client-token" value="">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="alertaToast" class="alert">
    <a href="#" class="close" data-hide="alert" aria-label="close"> × </a>
    <span><strong></strong></span>
</div>

<div id="loadingModal" class="modal-loading"></div>
