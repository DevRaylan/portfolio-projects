<nav class="navbar navbar-fixed-top navbar-inverse">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <div class="menu-thumb">
          <?php if($_SESSION['usuario']['foto']){ ?>                
            <img src="data:image/jpeg;base64,<?= $_SESSION['usuario']['foto']; ?>" class="img-rounded" />                 
          <?php } else {?>
            <i class="fas fa-user"></i>
          <?php } ?>
        </div>
      </button>
      
      <?php if ($this->expandeMenuTopo) { ?>
        <a class="navbar-brand" href="#" onclick="$('#icones_modulos').slideToggle()"><span class="glyphicon glyphicon-th"></span></a>
        <a class="navbar-brand hidden-xs hidden-sm" href="#" onclick="$('#icones_modulos').slideToggle()"><?= \Geral\Model\Config::getValue('title') ?></a>
        <a class="navbar-brand visible-xs visible-sm" href="#" onclick="$('#icones_modulos').slideToggle()"><?= \Geral\Model\Config::getValue('blockTitle') ?></a>
      <?php } else { ?>
        <span class="navbar-brand hidden-xs hidden-sm"><?= \Geral\Model\Config::getValue('title') ?></span>
        <span class="navbar-brand visible-xs visible-sm"><?= \Geral\Model\Config::getValue('blockTitle') ?></span>
      <?php } ?>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse menuDados" id="bs-example-navbar-collapse-1">

      <?php if (\Geral\Model\UserSession::paramExists('logado')) { ?>

        <ul class="nav navbar-nav navbar-right">
          
          <li class="dropdown">
            
            <a href="#" class="dropdown-toggle menu-profile" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <?= !empty(\Geral\Model\Session::getParam('simularDados')) ? '<span class="glyphicon glyphicon-ban-circle aviso-simulacao" title="Simulação de dados ativado"></span>' : '' ?>
              <div class="menu-thumb">
                <?php if($_SESSION['usuario']['foto']){ ?>                
                  <img src="data:image/jpeg;base64,<?= $_SESSION['usuario']['foto']; ?>" class="img-rounded" />                 
                <?php } else {?>
                  <i class="fas fa-user"></i>
                <?php } ?>
              </div>
            </a>
            
            <ul class="dropdown-menu subMenuDados">

              <li>
                <a>
                  <?= \Geral\Model\UserSession::getParam('nome') ?> <small class="vinculo">(<?= $_SESSION['usuario']['vinculo'] ?>) </small>
                </a>
              </li>
              <li role="separator" class="divider"></li>
              
              <?php if($_SESSION['usuario']['multivinculos'] === true) { ?>
                <li><a href="#" onclick="$('#modalSelVinculo').modal()">Selecionar outro vínculo</a></li>
                <li role="separator" class="divider"></li>
              <?php } ?>
            
              <li><a href="/Geral/Index/meusDados">Meus dados</a></li>
              <li role="separator" class="divider"></li>
              
              <li><a href="/Geral/Login/sair">Sair</a></li>
              <li role="separator" class="divider"></li>
              
            </ul>
          </li>
        </ul>

      <?php } ?>
      
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
  <div id="icones_modulos" style="display:none">
    <?php foreach (\Geral\Model\Config::getAllConfigs(['title', 'blockTitle', 'blockIcon', 'blockIconFA']) as $module => $config) {
      $active = $GLOBALS['url']->getNameModule() == $module ? 'active' : '';
      ?>
      <div class="<?= $active ?>">
        <a href="/<?= $module ?>" title="<?= $config['title'] ?>"><span class="icone <?= isset($config['blockIconFA']) ? $config['blockIconFA'] : ("glyphicon glyphicon-" . ($config['blockIcon'] ? $config['blockIcon'] : 'remove-sign')) ?>"></span>
          <div class="descricao"><?= $config['blockTitle'] ?></div>
        </a>
        <div style="margin-top: -20px" class="text-right">
          <span class="glyphicon glyphicon-<?= \Geral\Model\UserSession::paramExists('defaultApp') && \Geral\Model\UserSession::getParam('defaultApp') == $module ? 'star' : 'star-empty'?>"
            onclick="setDefaultApp(this, '<?= $module ?>')" title="Definir como aplicação padrão."></span>&nbsp;</div>
      </div>
    <?php } ?>
  </div>
</nav>

<script>
  function setDefaultApp(span, app)
  {
    $.get("/Geral/Index/setDefaultApp/"+app, function(response) {
      if(response.result == 'success') {
        $('#icones_modulos .glyphicon-star').removeClass('glyphicon-star').addClass('glyphicon-star-empty');
        $(span).removeClass('glyphicon-star-empty').addClass('glyphicon-star');
        alert(response.msg);
      } else {
        alert('Erro ao tornar esta apicação como padrão')
      }
    });
  }
</script>