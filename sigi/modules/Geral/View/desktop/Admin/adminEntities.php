<?php
$entities = \Geral\Model\Config::getAllEntities();
$configs = \Geral\Model\Config::getAllConfigs();
?>
<style>
    .alert-heading {
        font-size: 24px;
        line-height: 1.2;
    }
    .ellipsis {
        position: relative;
    }
    
        .ellipsis:before {
            content: '&nbsp;';
            visibility: hidden;
        }
        
        .ellipsis span {
            position: absolute;
            left: 8px;
            right: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    
    .icone_status {
        width: 14px;
        height: auto;
        vertical-align: bottom;
    }

    .is-sync {
        background-image: url(/imagens/Geral/icone_bolaverde.png);
        background-repeat: no-repeat;
        background-position: center;
    }

    .no-sync {
        background-image: url(/imagens/Geral/icone_bolavermelha.png);
        background-repeat: no-repeat;
        background-position: center;
    }
    
    .toggle-column {
        margin-right: 20px;
    }
    
    .panel-heading {
        cursor: pointer;
    }

    .register-select {
        margin-bottom: 15px;
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
    
    #modalConfirm .input-group {
        margin-top: 15px;
    }
    
    #sql {
        font-family: monospace;
        background-color: white;
    }
</style>
<div class="container">
    <h2>Gerenciamento das classes Entity</h2>

    <div id="alertNoSyncEntities" class="alert alert-danger hidden">
        <h4 class="alert-heading">Atenção!</h4>
        <div>Uma ou mais entidades não existem no banco de dados. Para sincronizá-las, por favor, acessar o 
        <a href="<?= $this->url->getUrlModule() ?>/Update/check"><strong>Sincronizador de Entidades</strong></a>.</div>
    </div>

    <?php if(!empty($conflictedEntities)) { ?>
    

    <div class="alert alert-danger">
        <h4 class="alert-heading">Atenção!</h4>
        <div><?= nl2br($conflictedEntities) ?></div>
    </div>

    <?php }  else { ?>

    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
        
        <?php
        // Entidades não sincronizadas com o Banco de Dados.
        $hasUnsynchronizedEntities = false;
        $i = 0;

        foreach($entitiesByModule as $module => $entities) { ?>
            <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" data-target="#collapse-<?= $i ?>">
            <h4 class="panel-title">
                <?= $configs[$module]['title'] ?>
            </h4>
            </div>
            
            <div id="collapse-<?= $i ?>" class="panel-collapse collapse<?=(($i==0)?' in':'');?>">
            <div class="panel-body">
                <table class="table table-hover">
                    <thead>
                        <th class="col-sm-6">Entity</th>
                        <th class="col-sm-3">Opções</th>
                    </thead>        
                <tbody>
                <?php
                if($entities) {
                    foreach($entities as $entity => $sqls) {
                        $creates = [];
                ?>
                    <tr>
                        <td><?= $entity ?></td>
                        <td>
                            <?php 
                            // Se for classe do tipo entidade mostra as opções de visualização e exclusão
                            if(!in_array('isNotEntity', $sqls)) {
                            ?>

                            <span class="btn btn-default btn-sm glyphicon glyphicon-list-alt" title="Ver tabela" onclick="showTable('<?= addslashes($entity) ?>')"></span>
                            
                            <?php foreach ($sqls as $create) {
                                $create = str_replace("'", "\'", $create);
                                array_push($creates, str_replace('"', "\'", $create));
                            }

                            $creates = join('\n\n', $creates);

                            if(empty($sqls)) {
                                $hasUnsynchronizedEntities = true;
                            ?>

                            <span class="btn btn-default disabled btn-sm btn-disalbed glyphicon glyphicon-trash" title="Entidade não existente no Banco de Dados"></span>

                            <?php
                            } else { ?>

                            <span class="btn btn-danger btn-sm glyphicon glyphicon-trash" title="Remover tabela" onclick="adminDB($(this), 'removeTable', ['<?= addslashes($entity) ?>'], '<?=$creates?>')"></span>
                            
                            <?php
                            } 
                            } else {
                            ?>
                            * Não é uma entidade.
                            <?php } ?>
                        </td>
                    </tr>                
                <?php 
                    }
                } else { ?>
                    <tr><td colspan="2">Não existem classes para sincronizar com tabelas do bando de dados.</td></tr>
                <?php
                } ?>
                </tbody>
                </table>
                </div>
            </div>
        <?php 
            $i++;
            } ?>
        </div>
    <?php } ?>
    </div>
</div>
<script>
    var fakeinput, tabelaRegistros;

    function adminDB(bt, acao, entities, sql, confirmed) {
        if(!confirmed && !confirm(acao, sql, function() {adminDB(bt, acao, entities, sql, true)})) {
            return false;
        }
        
        $.post("<?= $this -> url -> getUrlController() ?>/"+acao, { "entities" : entities }, function(response) {
            var response = $.parseJSON(response);
            
            if(response.result == 'success') {
                $(bt).removeClass('btn-danger btn-warning').addClass('btn-default disabled');
                $('#alertNoSyncEntities').removeClass('hidden');
            }

            showAlert(response.result, response.msg);
        });
    }

    function copySQL(btn) {     
        var sql = $(btn).closest('div.input-group').find('textarea').val(); 
        if (!fakeinput) {
            fakeinput = $('<input type="text"/>').css({
                position: "absolute",
                left: "-1000px",
                top: "-1000px"
            });
            $("#modalConfirm").append(fakeinput);     
        }
          
        fakeinput.val(sql);        
        fakeinput.select();
        
        var ok = document.execCommand("Copy");
        
        if (ok) {
            $(btn).tooltip('toggle');
        } 
    }

    $(document).ready(function() {
        $.extend( true, $.fn.selectpicker.defaults, {
            "selectAllText": 'Todos',
            "deselectAllText": 'Nenhum',
            "countSelectedText": 'Selecionados {0} de {1}',
        } );

        $(".btn-copy").tooltip();
        $(".btn-copy").on('mouseout',function(){
            $(".btn-copy").tooltip('hide');
        });

        <?= $hasUnsynchronizedEntities ? '$("#alertNoSyncEntities").removeClass("hidden");' : '' ?>
    });
    
    function confirm(acao, sql, _callback) {
        var titulo = '<strong>' + 'REMOVER TABELA' + '</strong> - Confirmação';
        
        $("#tituloConfirmModal").html(titulo);
        $("#sql").val(sql);
        $("#on-click").off('click').on('click', _callback);
        $("#modalConfirm").modal();
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
    
    function openBlob(b64) {
        const win = window.open();
        win.document.write('<iframe src="' + b64  + '" frameborder="0" style="border:0; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%;" allowfullscreen></iframe>');
    }
    
    function showTable(entity) {
        $.ajax( {
            url: "<?= $this -> url -> getUrlController() ?>/getEntityRegisters?entity="+entity,
            dataType: 'json',
            success: function ( json ) {
                var columns = [{data:''}], 
                    toggleBar = $('<select id="register-select" class="selectpicker toggle-bar register-select" name="register-select" multiple title="Registros da Entidade" data-selected-text-format="count>2" data-actions-box="true" data-style="btn-default"></select>'), 
                    dtTabela;
                $('.toggle-bar').remove();
                if ($.fn.DataTable.isDataTable("#tabela-registros")){
                    tabelaRegistros.destroy();
                }
                if (json.registers.length > 0) {
                    var n = 0;
                    columns = [];
                    $.each(json.registers[0], function(key,value){
                        var item = {};
                        item.data = key;
                        item.title = key;
                        item.className = "ellipsis";
                        item.render = function(data, type, row) {
                            if(typeof data == 'string' && data.indexOf('data:') === 0) {
                                return '<span  title="Abrir arquivo"><a onclick="openBlob(\''+data+'\')" href="javascript:void(0);">Abrir arquivo</a></span>';
                            } else {
                                let dataFromObj = data;

                                if(data && typeof data == 'object') {
                                    if (data.date) dataFromObj = moment(data.date).format('DD-MM-YYYY H:mm:ss');
                                    else {
                                        dataFromObj += "\n";

                                        $.each(data, function(key, valor) {
                                            dataFromObj += '- '+key+': '+valor+"\n";
                                        });
                                    }
                                }
                                
                                return '<span title="'+dataFromObj+'">'+dataFromObj+'</span>'
                            }
                        } 
                        columns.push(item);
                        $(toggleBar).append('<option value="'+n+'">'+key+'</option>');
                        n++;
                    });
                    $('#tabela-registros').empty().parent().prepend(toggleBar);
                    $('#register-select').selectpicker('selectAll')
                        .on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
                            if (clickedIndex != null) {
                                tabelaRegistros.column(clickedIndex).visible(isSelected);
                            }
                    });
                    $('#register-select').prev().find('.bs-select-all').on('click', function() {
                        tabelaRegistros.columns().visible(true);
                    });
                    $('#register-select').prev().find('.bs-deselect-all').on('click', function() {
                        tabelaRegistros.columns().visible(false);
                    });
                    tabelaRegistros = $('#tabela-registros').DataTable( {
                        destroy: true,
                        columns: columns,
                        data: json.registers,
                        initComplete: function(settings, json){
                            $("#tabela-registros th").removeClass('ellipsis');
                            if ($('#register-select option').length > 4) {
                                $('#register-select').selectpicker('val', ['0', '1', '2', '3'] );
                                this.api().columns().visible(false);
                                this.api().columns(['0', '1', '2', '3']).visible(true);
                            }
                        }
                    });
                } else {
                    $('#tabela-registros').html('<div class="text-center"><h4>Nenhum registro encontrado!</h4></div>');
                }
                $("#modal-registers").modal();
            }
        });
    }
</script>

<div id="modalConfirm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="tituloConfirmModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="tituloConfirmModal">Confirmação</h4>
            </div>
            <div class="modal-body form-horizontal">
                <label class="col-sm-offset-1">
                    <p>Você está prestes a executar o seguinte SQL</p>
                    <p>Deseja continuar?</p>
                </label>
                <div class="form-group">
                    <div class="input-group col-sm-offset-1 col-sm-10">
                        <textarea class="form-control custom-control" rows="3" value="teste" name="sql" id="sql" readonly style="resize:none;"></textarea>
                        <span class="input-group-addon btn btn-default btn-copy" type="button" onclick="copySQL($(this));" data-toggle="tooltip" 
                        data-trigger="manual" data-placement="bottom" title="Copiado!">
                            <i class="glyphicon glyphicon-copy"></i> Copiar</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="on-click">Executar</a>
            </div>
        </div>
    </div>
</div><!-- end modalConfirm -->

<div id="modal-registers" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Titulo Modal Registers" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="modal-registers-title">Registros</h4>
            </div>
            <div class="modal-body">
                <table id="tabela-registros" class="table table-striped table-hover table-bordered nowrap" style="width:100%">
                    <thead>
                        <tr>
                        </tr>
                    </thead>
                    
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div><!-- end modal-registers -->

<div id="alertaToast" class="alert">
    <a href="#" class="close" data-hide="alert" aria-label="close"> × </a>
    <span><strong></strong></span>
</div>
