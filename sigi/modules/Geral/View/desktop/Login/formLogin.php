<style type="text/css">
  body {
    padding-top: 40px;
    padding-bottom: 40px;
    background-color: #f5f5f5;
  }

  .form-signin {
    text-align: center;
    max-width: 300px;
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
  .form-signin button {
    margin: 30px 0 10px;
  }
  .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

  .logo {
      text-align: center;
      margin-bottom: 30px;
  }
  .popover {
    width: 253px;
  }
</style>
<div class="container">
    <div class="form-signin">
      <form class="" action="<?= $this -> url -> getUrlController() ?>/autenticar" method="post" name="login_form">
      <input type="hidden" name="urlRetorno" value="<?= $urlRetorno ?>">
        <div class="logo"><img src="<?= $this -> url -> getUrlImages() ?>/logo_udesc_sigla.jpg" alt="UDESC" width="100%"></div>
          <?php if(!empty($msg)) { ?>
              <div class="alert alert-danger"><?= $msg ?></div>
          <?php } ?>
          <div class="form-group">
              <label class="sr-only" for="id">CPF ou matrícula</label>
              <input type="text" class="form-control input-block-level" placeholder="CPF" data-toggle="tooltip" data-placement="right" data-html="true" title="Informe seu CPF<br>(somente números)" name="id" value="" autofocus required />
          </div>
          <div class="form-group">
              <label class="sr-only" for="password">Senha</label>
              <input type="password" class="form-control input-block-level" placeholder="Senha" data-toggle="tooltip" data-placement="right" title="Informe sua senha" name="password" value="" required />
          </div>
          
          <?php if($_SERVER['AMBIENTE'] == 'DEV') { ?>
                  <div class="checkbox text-left">
                    <label>
                      <input type="checkbox" name="simularDados" value="1" <?= TIPO_LOGIN == "True" ? 'checked onclick="return false"' : 'false' ?>>
                      Simular dados do usuário
                    </label>
                  </div>
            <?php }

            if(BD_DRIVER == 'pdo_sqlite') { ?>
              <div class="alert alert-info">
                <strong>Atenção:</strong> o redirecionamento automático para a sincronização de entidades está desativado para banco de dados SQLite.
                Você pode acessar manualmente em <a href="<?= $this->url->getUrlModule().'/Update/index' ?>"><strong>Sincronizar entidades</strong></a>.
              </div>
            <?php } ?>

          <button type="submit" class="btn btn-large btn-block btn-success">Entrar</button>
      </form>
      <hr style="width:0%;margin:5px auto;">
      <a href="https://id.udesc.br/IdUdesc/RedefinirSenha" target="_blank" style="color:#468847">Redefinir a senha</a>
      <hr style="width:50%;margin:5px auto;">
      <a href="https://id.udesc.br/IdUdesc/RedefinirSenha" target="_blank" style="color:#468847">Primeiro acesso</a>
      <br/>
  </div>
</div>

<script>
    $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});

    <?php if($_SERVER['AMBIENTE'] == 'DEV') {
      if(TIPO_LOGIN == 'True') { ?>
        $('input[name=simularDados').prop('disabled', true)
        $(function () {
          $('.checkbox').popover({
            animation: false,
            container: 'label',
            title: 'Atenção!',
            placement: 'right',
            content: 'Com o "TIPO_LOGIN" definido como "True" a consulta de dados de usuários é feita apenas localmente.',
            trigger: 'hover'
          }).popover('show');
        });
      <?php }
    } ?>
</script>
