<?php

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {

	die('direct access abort ');
}

if(function_exists('pgf_autoload') &&  is_callable('pgf_autoload')){

    spl_autoload_register('pgf_autoload');
}

    function pgf_autoload($class_name){

        $namespace='Payamito\GravityForm';
        if ( 0 !== strpos( $class_name, $namespace ) ) {
            return;
        }
    
        $class_name = str_replace( $namespace, '', $class_name );
        $class_name = str_replace( '\\', DIRECTORY_SEPARATOR, $class_name );
    
        $path = PAYAMITO_GF_DIR . $class_name . '.php';
        
        if(file_exists($path)){
            include_once $path;
        }
    }