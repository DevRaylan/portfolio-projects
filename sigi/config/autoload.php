<?php
function autoload($className) {
    $arquivo = DIR_MODULES.'/'.str_replace('\\', '/', $className).'.php';
    
    if( file_exists($arquivo) ) {
        require($arquivo);
    } else {
        return false;
    }
}

spl_autoload_register( "autoload" );
