<?php
    $moduleName = $this -> url -> getNameModule();
    $menuOpened = \Geral\Model\Config::getValue($moduleName."MenuOpened", $moduleName);

    // verifica se o parâmetro de fixar o menu está selecionado para o usuário
    if(!\Geral\Model\UserSession::isEmpty()){
        $userPreference = new \Geral\Model\UserPreference(\Geral\Model\UserSession::getParam('cpf'), 'Geral');
        if(!empty($userPreference) && $userPreference->preferenceExists('fixaMenu')) {
            $menuOpened = boolval($userPreference->getPreference('fixaMenu'));
        }
    }

    // verifica qual icone foi definido no arquivo config.php e retorna a classe que deve ser utilizada
    function getIconeClass( $link )
    {
        if (isset($link['fontawesome'])){
            return $link['fontawesome'];
        }
        return "glyphicon glyphicon-" . ($link['glyphicon'] ? $link['glyphicon'] : 'remove-sign');
    }
?>
<div id="wrapper" class="<?= ($menuOpened == true) ? 'toggled' : '' ?>">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li>
                <a id="menu-toggle" href="#" tabindex="-1"><i class="glyphicon glyphicon-align-justify"></i>Fechar menu</a>                
                <a id="fixa-menu" href="#" tabindex="-1"><span class="glyphicon glyphicon-pushpin <?php if($menuOpened == true){ echo ' active'; } ?>" aria-hidden="true"></span></a>
            </li>
            <?php
            $menu = \Geral\Model\Config::getMenuApplication();
            $menu = !empty($menu[$this -> url -> getNameController()]) ? $menu[$this -> url -> getNameController()] : [];
            
            foreach($menu as $link) {
                if(empty($link['links'])) { ?>
                    <li class="<?= ($this -> url -> getUrlAction() == $link['url']) ? 'active' : '' ?>">
                        <a href="<?= $link['url'] ?>" title="<?= $link['descricao'] ?>" tabindex="-1">
                            <i class="<?= getIconeClass($link); ?>"></i><?= $link['descricao'] ?>
                        </a>
                    </li>
                <?php
                } else { ?>
                <?php
                // verifica se um dos subitens está selecionado
                $urlVars = $GLOBALS['url'] -> getSegments(2);
                $key = (is_array($urlVars) && $urlVars[0] == 'index') ? array_search(substr($this->url->getUrlAction(),0,-6),array_column($link['links'], 'url')) : array_search($this->url->getUrlAction(),array_column($link['links'], 'url'));
                $style = ($key !== false) ? 'style="display:block"' : '';
                ?>
                    <li>
                        <a href="#" onclick="$(this).next().slideToggle()">
                            <i class="<?= getIconeClass($link); ?>"></i><?= $link['descricao'] ?></span><span class="caret">
                        </a>
                        <ul class="submenu" <?php echo $style; ?>>
                            <?php
                            foreach($link['links'] as $sublink) { ?>
                                <?php
                                    if(is_array($urlVars) && $urlVars[0] == 'index'){
                                        $class = ($this -> url -> getUrlAction() == ($sublink['url'].'/index')) ? 'active' : '';
                                    }else{
                                        $class = ($this -> url -> getUrlAction() == $sublink['url']) ? 'active' : '';
                                    }
                                ?>
                                <li class="<?php echo $class; ?>">
                                    <a href="<?= $sublink['url'] ?>" title="<?= $sublink['descricao'] ?>">
                                        <i class="<?= getIconeClass($sublink) ?>"></i><?= $sublink['descricao'] ?>
                                    </a>
                                </li>
                                <?php 
                            }
                            ?>
                        </ul>
                    </li>
                <?php
                } // if 
            } // foreach ?>
        </ul>
        <span id="version">
            <i class="fas fa-code-branch"></i>
            <small>
                <?= file_get_contents('../config/version'); ?>
            </small>
        </span>
    </div>
    
    <!-- /#sidebar-wrapper -->
</div>