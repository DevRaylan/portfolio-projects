<style>
    .alert-heading {
        font-size: 24px;
        line-height: 1.2;
    }

    textarea {
        font-size: 10px;
    }
</style>
<div class="container">
    <h2>Sincronização das entidades UDev e aplicações <small>SQLs para execução no banco de dados.</small></h2>

    <?php if(!empty($conflictedEntities)) { ?>

    <div class="alert alert-danger">
        <h4 class="alert-heading">Atenção!</h4>
        <div><?= nl2br($conflictedEntities) ?></div>
    </div>

    <?php } elseif(empty(DEPLOY_KEY)) { ?>

        <div class="alert alert-danger">
            <h4 class="alert-heading">Atenção!</h4>
            <div>
                <p>Para utilizar este recurso, deve ser configurada a constante <strong>DEPLOY_KEY</strong>, presente no arquivo de configuração do UDev para os ambientes da aplicação.</p>
                <p>Os arquivos são:</p>
                <ul>
                    <li>/config/configDev.php: configurar a constante DEPLOY_KEY com o valor da key para deploy no ambiente de Desenvolvimento.</li>
                    <li>/config/configHOM.php: configurar a constante DEPLOY_KEY com o valor da key para deploy no ambiente de Homologação.</li>
                    <li>/config/configPROD.php: configurar a constante DEPLOY_KEY com o valor da key para deploy no ambiente de Produção.</li>
                </ul>

                <p><strong>Obs:</strong> a chave para deploy pode ser solicitada ao setor de <strong>Datacenter/SETIC</strong> via chamado, em <a href="https://chamados.udesc.br/otrs/customer.pl?Action=CustomerTicketMessage;Subaction=StoreNew;Dest=17||REITORIA::INFORMATICA::DATACENTER;ServiceID=25;Subject=Solicitação da chave de deploy para os ambientes da aplicação.;Expand=3;DynamicField_LocalAtendimento=UDESC;Body=Nome do Sistema: <br>Url do sistema no https://code.udesc.br: " target="_blank">https://chamados.udesc.br</a>.</p>
            </div>
        </div>

        <?php
          } elseif(!empty($sqlsToSyncBySchema)) {
        $sqlsOutput = [];
        $keyToAlert = [ 'INSERT ' => 'default', 'CREATE ' => 'default', 'ALTER ' => 'warning', 'DROP ' => 'danger'];

        foreach($sqlsToSyncBySchema as $schema => $sqls) {
            if(empty($sqls)) continue;
            
            foreach($sqls as &$sql) {
                foreach($keyToAlert as $key => &$alert) {
                    if(strpos($sql, $key) === 0) {
                        $sql = str_replace($key, '<div class="alert alert-'.$alert.'">'.$key, $sql).'</div>'; 
                    }
                }

                unset($alert);
            }

            $sqlsOutput[$schema] .= implode(PHP_EOL,$sqls);
            $i++;
        }
        ?>
                
            <?php foreach($sqlsOutput as $schema => $sqls) { ?>
            <div class="panel panel-<?= $schema == BD_NAME ? 'primary' : 'default' ?>">
                <div class="panel-heading text-center">Tabelas do schema <strong><?= $schema ?></strong></div>
                <div class="panel-body">
                    <?= $sqls ?>
                </div>
            </div>
            <?php } ?>
            
        <br />

        <form id="formDados" class="form text-center">
            <div class="form-group">
                <label>Deploy key: <input type="text" name="deployKey" class="form-control" value="<?= $deployKey ?>" /></label>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-warning glyphicon" title="Update"> <strong>Atualizar base de dados do Framework.</strong></button>
            </div>
        </form>
        <br />
        </div>
    <?php }  else { ?>
        <div class="alert alert-info text-center">Não há atualizações para serem realizadas.</div>
    <?php } ?>

    <div id="consoleModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Saída console</h4>
            </div>
            <div class="modal-body">
                <div id="consoleContent" class="alert alert-default"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="document.location='/'">Realizar login</button>
            </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>

<script>
    function updateDB() {
        if(!confirm('Confirma a atualização do banco de dados do Framework?')) {
            return false;
        }

        var dados = {
            'key' : $("input[name='deployKey']").val()
        }

        $.post("<?= $this -> url -> getUrlController() ?>/execute", dados, function(response) {
            //alert(response);
            $("#consoleContent").html('<pre>'+response+'</pre>');
            $("#consoleModal").modal();
        });
    }

    $("#formDados").submit(function(e) {
        e.preventDefault();

        updateDB();
    })
</script>
