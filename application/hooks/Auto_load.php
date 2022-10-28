<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function register_autoloader()
{
    spl_autoload_register('site_autoloader');
}

/*
 * Custom autoloader.
 * This piece of code will allow controllers and other classes
 * that do not start with "CI_" to be loaded when
 * extending controllers, models, and libraries.
 */
function site_autoloader($class)
{
    if(strpos($class, 'CI_') !== 0)
    {
        if(file_exists($file = APPPATH.'core/'.$class.'.php'))
        {
            require_once $file;
        }
//        elseif(file_exists($file = APPPATH.'libraries/'.$class.'.php'))
//        {
//            require_once $file;
//        }
//        elseif(file_exists($file = APPPATH.'models/'.$class.'.php'))
//        {
//            require_once $file;
//        }
    }
}