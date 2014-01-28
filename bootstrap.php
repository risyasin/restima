<?php
/**
 * Restima - A RESTful PHP Micro-framework (http://restima.evrima.net/)
 *
 * @copyright Copyright (c) 2013 Yasin inat <risyasin@gmail.com>
 * @license   MIT
 */

/**
 * Generic class autoloader.
 *
 * @param string $class_name
 */

function autoload_class($class_name) {
    $directories = array('./lib/','./lib/Restful/','./app/');
    foreach($directories as $directory) {
        //echo '<pre style="font: normal 12px Lucida Sans;">'; print_r($directory.$class_name.'--'); echo '</pre>';
        $filename = str_replace(array('\\',"\\"),'/',$directory.$class_name).'.php';
        if (is_file($filename)) { require_once $filename; break; }
    }
}

spl_autoload_extensions('.php');

/**
 * Register autoloader functions.
 */
spl_autoload_register('autoload_class');

