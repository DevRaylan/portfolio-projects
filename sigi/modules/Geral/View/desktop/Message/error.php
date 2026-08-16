<?php 
$exceptionTypeToCss = [
    'W' => 'warning',
    'E' => 'danger',
    'A' => 'warning'
];
?>
<div class="container">
<h2><span class="glyphicon glyphicon-alert"></span> Erro do sistema</h2>
<p>Os seguintes erros foram encontrados no processamento desta requisição: </p>
<?php foreach($_GET['messages'] as $message) {?>
    <div class="panel panel-<?= $exceptionTypeToCss[substr($message['code'],0,1)] ?>">
      <div class="panel-heading">Código: <strong><?= $message['code']?></strong></div>
      <div class="panel-body">
            <p>Mensagem: <strong><?= $message['message']?></strong></p>
            <?php if(!empty($message['detail'])) { ?>
                <p>Detalhe: <strong><?= $message['detail'] ?></strong></p>
            <?php } ?>
            <?php if(!empty($message['docLink'])) { ?>
                <p>Link de suporte: <strong><a href="<?= $message['docLink'] ?>" target="_blank"><?= $message['docLink'] ?></a></strong></p>
            <?php } ?>
            <?php if(!empty($message['url'])) { ?>
                <p>Requisição: <strong><?= $message['url'] ?></strong></p>
            <?php } ?>
      </div>
    </div>
<?php } ?>
</div>