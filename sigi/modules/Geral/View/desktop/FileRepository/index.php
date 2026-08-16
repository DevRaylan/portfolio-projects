<style>
    h1 {
        font-size: 36px;
        color: inherit;
    }
    .panel-heading {
        cursor: pointer;
    }
</style>
<div class="container">
    <div class="page-header">
        <h1>Lista de arquivos</h1>
    </div>
    <div class="panel-group" id="files-accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
        
    <?php
    $i = 0;

    foreach($downloadsByApp as $module => $list) { ?>
        <div class="panel-heading" data-toggle="collapse" data-parent="#files-accordion" data-target="#collapse-<?= $i ?>">
          <h4 class="panel-title">
              <?= $module ?><span class="badge pull-right" title="<?=count($list);?> arquivos"><?=count($list);?></span>
          </h4>
        </div>
        
        <div id="collapse-<?= $i ?>" class="panel-collapse collapse<?= ($i==0)?' in':'' ?>">
            <div class="panel-body">
                <?php
                    if (count($list) < 1) { 
                ?>
                    <em>Nenhum arquivo encontrado</em>
                <?php
            } else {
                ?>
                <table class="table table-hover">
                    <thead>
                        <th class="col-sm-1"></th>
                        <th class="col-sm-9">Nome do arquivo</th>
                        <th class="col-sm-2 text-center">Último acesso</th>
                    </thead>        
                    <tbody>
                        <?php
                            $j = 1;
                            foreach($list as $file) { 
                        ?>
                            <tr>
                                <td class="text-right"><?=$j++;?>. </td>
                                <td><a href="<?=$this->url->getUrlController().'/downloadAll/'.$file['name']?>" title="Baixar arquivo"><?= $file['name'] ?></a></td>
                                <td class="text-center"> *Nunca acessado </td>
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

<script>
</script>
