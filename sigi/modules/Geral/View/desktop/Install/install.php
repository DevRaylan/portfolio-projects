<style type="text/css">
    body {
      padding-top: 40px;
      padding-bottom: 40px;
      background-color: #f5f5f5;
    }

  .form-signin {
    text-align: center;
    max-width: 400px;
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
  
  .listaDeEntidades, .listaDeEntidades li {
      list-style: none;
      
      padding: 0;
  }
</style>
<div class="container form-signin">
        <div class="logo"><img src="<?= $this -> url -> getUrlImages() ?>/logo_login.jpg" alt="Identidade Udesc"></div>
        <br />
        <div class="text-left">
            <p>Sincronização das classes de entidade (diretório "Entity") com o banco de dados.</p>
        <?php
        if(!empty($entities)) { ?>
            <ul class="listaDeEntidades">
                <li>Entidades<ul>
                <?php foreach($entities as $entitie) { ?>
                    <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> <?= $entitie ?></li>
                <?php } ?>
                </ul><br />
            </li>
            </ul>
            <?php
        } 
        
        if(!empty($msgsOk)) {
            foreach($msgsOk as $msg) { ?>
                <div class="alert alert-success"><?= $msg ?></div>
            <?php }
        }
        
        if(!empty($msgsErro)) {
            foreach($msgsErro as $msg) { ?>
                <div class="alert alert-danger"><?= $msg ?></div>
            <?php } ?>
            <a href="<?= $this -> url -> getUrlController() ?>" class="btn btn-large btn-block btn-primary">Voltar</a>
            <?php
        } else { ?>
            <a href="<?= $this -> url -> getUrlModule() ?>/Login" class="btn btn-large btn-block btn-primary">Realizar login</a>
        <?php
        }
        ?>
    </div>
</div>
<div class="container">
  
</div> 