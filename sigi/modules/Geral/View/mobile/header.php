<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="pt-br">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Sistema MVC Básico - Versão Mobile</title>
        <meta http-equiv="pragma" content="no-cache"/>
        <meta http-equiv="cache-control" content="no-cache"/>
        <meta http-equiv="imagetoolbar" content="no"/>
        
        <link rel="shortcut icon" type="image/x-icon" href="/imagens/logo_sistema.ico" />
        <link rel="stylesheet" type="text/css" href="/libs/Geral/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="/libs/Geral/bootstrap/css/bootstrap-theme.min.css" />
        <link rel="stylesheet" type="text/css" href="/libs/Geral/bootstrap-validator/css/bootstrapValidator.min.css" />
        <link rel="stylesheet" type="text/css" href="/libs/Geral/geral/style.css" />
            
        <script type="text/javascript" src="/libs/Geral/jquery/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="/libs/Geral/bootstrap-validator/js/bootstrapValidator.min.js"> </script>
        <script type="text/javascript" src="/libs/Geral/bootstrap-validator/js/languages/pt_BR.js"></script>
        <script src="/libs/Geral/jquery_file_upload/js/vendor/jquery.ui.widget.js"></script>
        <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
        <script src="/libs/Geral/jquery_file_upload/js//jquery.iframe-transport.js"></script>
        <!-- The basic File Upload plugin -->
        <script src="/libs/Geral/jquery_file_upload/js/jquery.fileupload.js"></script>
        <script src="/libs/Geral/highcharts/highcharts.js"></script>
        <script src="/libs/Geral/highcharts/data.js"></script>
        <script src="/libs/Geral/highcharts/exporting.js"></script>

        
        <script type="text/javascript" src="/libs/Geral/geral/sistema.js"></script>
        <style>
            body {
              padding-bottom: 20px;
              padding-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <?php if(\Geral\Model\UserSession::paramExists('logado')) { ?>
            <nav class="navbar navbar-default">
              <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                  <a class="navbar-brand" href="#">MVC</a>
                </div>
            
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                  <ul class="nav navbar-nav">
                    <li><a href="/">Home <span class="sr-only">(current)</span></a></li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Módulos <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li><a href="/usuario">Gerenciar usuários</a></li>
                        <li><a href="/contratos">Contratos</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Módulo 3</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Módulo 4</a></li>
                      </ul>
                    </li>
                  </ul>
                  
                  <!--form class="navbar-form navbar-left">
                    <div class="form-group">
                      <input type="text" class="form-control" placeholder="Search">
                    </div>
                    <button type="submit" class="btn btn-default">Submit</button>
                  </form-->
                  
                  <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= \Geral\Model\UserSession::getParam('nome') ?> <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li><a href="#">Alterar dados</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="/login/sair">Sair</a></li>
                      </ul>
                    </li>
                  </ul>
                </div><!-- /.navbar-collapse -->
              </div><!-- /.container-fluid -->
            </nav>
            <h1><?= self::$title ?></h1>
            <?php } ?>
