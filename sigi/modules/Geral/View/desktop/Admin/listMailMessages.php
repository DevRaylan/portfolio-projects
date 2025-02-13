<style>
    h1 {
        font-size: 36px;
        color: inherit;
    }
    #modal-email-details textarea {
        resize: vertical;
        cursor: default;
        background: #fff;
    }
    #recipients,
    #subject {
        overflow: hidden;
    }
    .clickable,
    .panel-heading {
        cursor: pointer;
    }
</style>
<div class="container">
    <div class="page-header">
        <h1>Mensagens enviadas pelas aplicações</h1>
    </div>
    <div class="panel-group" id="emails-accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
        
    <?php
    $i = 0;

    foreach($mailMessages as $module => $list) { ?>
        <div class="panel-heading" data-toggle="collapse" data-parent="#emails-accordion" data-target="#collapse-<?= $i ?>">
            <h4 class="panel-title">
                <?= $module ?><span class="badge pull-right" title="<?=count($list);?> arquivos"><?=count($list);?></span>
            </h4>
        </div>
        
        <div id="collapse-<?= $i ?>" class="panel-collapse collapse<?= ($i==0)?' in':'' ?>">
            <div class="panel-body">
                <?php
                    if (count($list) < 1) { 
                ?>
                    <em>Nenhum e-mail encontrado</em>
                <?php
            } else {
                ?>
                <table class="table table-hover">
                    <thead>
                        <th class="col-sm-1 text-right">ID</th>
                        <th class="col-sm-2 text-center">Data</th>
                        <th class="col-sm-5">Destinatários</th>
                        <th class="col-sm-4">Assunto</th>
                    </thead>        
                    <tbody>
                        <?php
                            $j = 1;
                            foreach($list as $index=>$email) { 
                        ?>
                            <tr class="clickable" data-app="<?= $module ?>" data-index="<?=$index?>">
                                <td class="text-right"><?=$email['id'] ?>. </td>
                                <td class="text-center"><?= $email['dtCriacao']->format('d/m/Y') ?></td>
                                <td><?=$email['emails'] ?> </td>
                                <td><?=$email['subject'] ?></td>
                            </tr>
                        <?php 
                            }
                        ?>
                    </tbody>
                </table>
            <?php 
                }
            ?>
            </div>
        </div>
    <?php 
        $i++;
    } ?>
        </div>
    </div>
</div>
<div class="modal fade in" id="modal-email-details" tabindex="-1" role="dialog" aria-labelledby="titulo-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title text-center" id="titulo-modal">Detalhes do e-mail</h4>
            </div>
            <div class="modal-body form-horizontal">
                <div class="container-fluid">
                    <div class="form-group">
                        <label for="status" class="control-label col-sm-2">Status: </label>
                        <div class="col-sm-2">
                            <p class="form-control form-control-static" id="status" name="status"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="id-email" class="control-label col-sm-2">ID: </label>
                        <div class="col-sm-1">
                            <p class="form-control form-control-static" id="id-email" name="id-email"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="dt-criacao" class="control-label col-sm-2">Data criação: </label>
                        <div class="col-sm-2">
                            <p class="form-control form-control-static" id="dt-criacao" name="dt-criacao"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="recipients" class="control-label col-sm-2">Destinatários: </label>
                        <div class="col-sm-9">
                            <p class="form-control form-control-static" id="recipients" name="recipients" title=""></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subject" class="control-label col-sm-2">Assunto: </label>
                        <div class="col-sm-9">
                            <p class="form-control form-control-static" id="subject" name="subject" title=""></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message" class="control-label col-sm-2">Mensagem: </label>
                        <div class="col-sm-9">
                            <textarea class="form-control form-control-static" rows="5" name="message" id="message" disabled=""></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="log" class="control-label col-sm-2">Log: </label>
                        <div class="col-sm-9">
                            <textarea class="form-control form-control-static" rows="5" name="log" id="log" disabled=""></textarea>
                        </div>
                    </div>
                </div>
            </div><!-- end modal-body -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $(".clickable").click(function(){
            viewEmailDetails($(this).attr('data-app'), $(this).attr('data-index'));
        })
    });
    
    function viewEmailDetails(app, index){
        var emailMessages = <?=json_encode($mailMessages)?>;
        emailMessages = emailMessages[app][index];console.log(emailMessages);
        $("#status").text(emailMessages['status']);
        $("#id-email").text(emailMessages['id']);
        $("#dt-criacao").text(moment(emailMessages['dtCriacao'].date).format('DD/MM/YYYY'));
        $("#recipients").text(emailMessages['emails']);
        $("#recipients").attr('title',emailMessages['emails']);
        $("#subject").text(emailMessages['subject']);
        $("#subject").attr('title',emailMessages['subject']);
        $("#message").text(emailMessages['message']);
        $("#log").text(emailMessages['log']);
        $("#modal-email-details").modal();
    }
</script>
