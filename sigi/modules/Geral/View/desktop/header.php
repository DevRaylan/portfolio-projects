<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="pt-br">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <title><?= \Geral\Model\Config::getValue('title') ?></title>
        <meta http-equiv="pragma" content="no-cache"/>
        <meta http-equiv="cache-control" content="no-cache"/>
        <meta http-equiv="imagetoolbar" content="no"/>
        
        <link rel="icon" type="image/ico" sizes="16x16" href="/imagens/Geral/favicon.ico">

        <link rel="stylesheet" type="text/css" href="/libs/Geral/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="/libs/Geral/bootstrap/css/bootstrap-theme.min.css" />
        <link rel="stylesheet" type="text/css" href="/libs/Geral/fontawesome/css/all.min.css" />
        <?php 
        if ($this->carregaLibs) {
        ?>
            <link rel="stylesheet" type="text/css" href="/libs/Geral/bootstrap-validator/css/bootstrapValidator.min.css" />
            <link rel="stylesheet" type="text/css" href="/libs/Geral/datatables/css/dataTables.bootstrap.css" />
            <link rel="stylesheet" type="text/css" href="/libs/Geral/datatables/css/buttons.bootstrap.min.css" />
            <link rel="stylesheet" type="text/css" href="/libs/Geral/datatables/css/responsive.bootstrap.min.css" />
            <link rel="stylesheet" type="text/css" href="/libs/Geral/bootstrap-select/css/bootstrap-select.min.css" />
            <link rel="stylesheet" type="text/css" href="/libs/Geral/bootstrap-datepicker/css/bootstrap-datetimepicker.min.css" />
            <link rel="stylesheet" type="text/css" href="/libs/Geral/jquery_file_upload/css/jquery.fileupload.css" />
            <link rel="stylesheet" type="text/css" href="/libs/Geral/jquery-jcrop/jquery.Jcrop.css" />
            <link rel="stylesheet" type="text/css" href="/libs/Geral/geral/style.css" />
        <?php
        }
        ?>

        <?php
        if ($this->cssFiles) {
            foreach ($this->cssFiles as $cssFile) {
        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo DIR_PUBLIC_LIBS.'/'.$this->url->getNameModule().'/'.$cssFile ?>" />
        <?php
            }
        }
        ?>

        <?php
        // Verifica se tem o parâmetro do tema pra aplicar
        if(!\Geral\Model\UserSession::isEmpty()){
            $userPreference = new \Geral\Model\UserPreference(\Geral\Model\UserSession::getParam('cpf'));
            if($tema = $userPreference->getPreference('tema')){ ?>
                <link rel="stylesheet" type="text/css" href="/libs/Geral/temas/<?= $tema ?>" />
        <?php } } ?>

        <script type="text/javascript" src="/libs/Geral/jquery/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="/libs/Geral/bootstrap/js/bootstrap.min.js"></script>
        <!-- Funções do framework para serem utilizadas na view --> 
        <script type="text/javascript" src="/libs/Geral/geral/udevFunctions.js"></script>
        <script type="text/javascript">
            // Iniciar a as funções do framework para a view.
            const udev = iniUdev(<?= json_encode($GLOBALS['url']->getSegments(0)) ?>, <?= json_encode($this->dados) ?>);
        </script>

        <?php
            if ($this->jsFiles) {
                foreach ($this->jsFiles as $jsFile) {
            ?>
            <script type="text/javascript" src="<?php echo DIR_PUBLIC_LIBS.'/'.$this->url->getNameModule().'/'.$jsFile ?>"></script>
            <?php
                }
            }
        ?>

    </head>
    <body <?php if(!$this->menuTopo){ echo 'class="sem-topo"'; } ?>>

        <noscript key="noscript" id="fw-noscript" className="fw-noscript" style="font-size:16px; color:#fff; text-align:center; position:fixed; top:0; left:0; width:100%; display:block; padding:15px; background:#f00; z-index:10000;">
            Seu browser não tem suporte a JavaScript. Por favor habilite a opção no seu navegador.
        </noscript>

        <?php
        if($this->menuTopo){
            include(DIR_MODULES.'/Geral/View/desktop/navbar_top.php');
        }

        $menu = \Geral\Model\Config::getValue('menu');

        if(!empty($menu[$this -> url -> getNameController()]) && is_array($menu[$this -> url -> getNameController()]) && $this->menuLateral)
            include(DIR_MODULES.'/Geral/View/desktop/navbar_side.php');
        ?>

        <!-- Page Content -->
        <?php
        $class = '';
        if(!$this->menuLateral) $class .= ' sem-menu';
        if($menuOpened == true) $class .= ' toggled';
        ?>
        <div id="page-content-wrapper" class="<?php echo $class; ?>">