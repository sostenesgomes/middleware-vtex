<?php

function autoload($class) {

    $path = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;

    if (file_exists($path . $class . '.php')){
        include_once($class.'.php');

    }else{

        $path  	  = Config::get('model');
        $final    = '.class.php';
        $filename = $path . $class . $final;

        if (file_exists($filename)){
            include_once($filename);
        }
    }
}

spl_autoload_register("autoload");