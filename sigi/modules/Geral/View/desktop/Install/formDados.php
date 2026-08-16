<style type="text/css">
  body {
    padding-top: 40px;
    padding-bottom: 40px;
    background-color: #f5f5f5;
  }

  .form-signin {
    text-align: center;
    max-width: 500px;
    padding: 19px 29px 29px;
    margin: 0 auto 20px;
    background-color: #fff;
    border: 1px solid #e5e5e5;
    -webkit-border-radius: 5px;
       -moz-border-radius: 5px;
            border-radius: 5px;
    -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
       -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
            box-shadow: 0 1px 2px rgba(0,0,0,.05);
  }
  .form-signin .form-signin-heading,
  .form-signin .checkbox {
    margin-bottom: 10px;
    color: #999;
  }
  .form-signin input[type="text"],
  .form-signin input[type="password"] {
    font-size: 16px;
    height: auto;
    margin-bottom: 15px;
    padding: 7px 9px;
  }
  .logo {
      text-align: center;
  }
</style>
<div class="container">
      <form class="form-signin" action="<?= $this -> url -> getUrlController() ?>/install" method="post" name="login_form">
        <div class="logo"><img src="<?= $this -> url -> getUrlImages() ?>/logo_login.jpg" alt="Identidade Udesc"></div>
        <br />
        <h3>Instalação do Framework UDESC</h3>
        <br />
        <div class="text-left">
        <p>Diretórios que o "www-data" deve ter permissão de escrita.</p>
        <p>
        <?php
        $directoryOk = true;
        foreach($directories as $directory => $writeable) {
            if($directoryOk && !$writeable) {
                $directoryOk = false;
            }
            ?>
            <?= $writeable ? '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>' : '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>' ?> <?= $directory ?><br />
        <?php
        }
        ?>
        </p>
        </div>
        <hr />
        <div class="text-left">
        <p>Extensões necessárias para a utilização do framework.</p>
        <p>
        <?php
        $extensionOk = true;
        foreach($extensions as $extension => $installed) {
            if($extensionOk && !$installed) {
                $extensionOk = false;
            }
            ?>
            <?= $installed ? '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>' : '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>' ?> <?= $extension ?><br />
        <?php
        }
        ?>
        </p>
        </div>
        
        <hr />
        <?php 
        if($varAmbienteIsSet) { ?>
            <p class="text-left"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Constante <strong>AMBIENTE</strong> do apache configurada com o valor: <strong><?= $varAmbienteIsSet ?></strong></p>
        <?php }
        else { ?>
            <p class="text-left"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> A constante "AMBIENTE" do apache não existe ou está com um valor não esperado.</p>
            <p class="text-left">Os valores aceitos são <strong>DEV, HOM ou PROD</strong>.</p>
        <?php
        }
        ?>
        
        <hr />
        <p class="alert alert-info"><strong>Atenção:</strong> para a instalação, todos os itens acima devem estar com o ícone <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>.</p>
        <?php
        if(!empty($msg)) { ?>
              <div class="alert alert-danger"><?= $msg ?></div>
              <a href="<?= $this -> url -> getUrlModule() ?>/Login" class="btn btn-large btn-block btn-primary">Realizar login</a>
          <?php } elseif($extensionOk && $directoryOk && $varAmbienteIsSet) { ?>
          <label>Insira um CPF para login de administrador do sistema: <input type="text" class="input-block-level" placeholder="CPF" title="Preencha este campo com o CPF.&#13Caso seja servidor da UDESC  possvel utilizar a matrcula institucional." name="cpf" value="" autofocus required /></label>
          <button type="submit" class="btn btn-large btn-block btn-primary">Instalar</button>
          <?php } ?>
      </form>
</div>
<div class="container">
  
</div> 