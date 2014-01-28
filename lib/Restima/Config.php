<?php


/**
 * Restima - A RESTful PHP Micro-framework (http://restima.evrima.net/)
 *
 * @copyright Copyright (c) 2013 Yasin inat <risyasin@gmail.com>
 * @license   MIT
 */

namespace Restima;

/**
 * Config parser.  only uses parse method.
 *
 * Class Config
 * @package Restful
 */
class Config {

    public static $dir;

    public static $default_config = 'defaults';

    private static $check_keys = true;

    public static $environment_key = 'APPLICATION_ENV';

    public static $developer_key = 'APPLICATION_DEV';

    /**
     * Precedence overwrites if it parses JSON in any precedence,
     * @var $precedence
     */

    public static $precedence = Array('application/json','application/xml','text/csv');


    /**
     * Parse all config files in specified directory, as it's need by webserver's environment settings.
     *
     * @param null $dir
     * @return object
     */

    public static function parse($dir = null)
    {
        self::$dir = ($dir != null ? rtrim($dir,'/').'/': dirname(dirname(dirname(__FILE__))).'/config/');

        $config = (object) array();
        // No configuration needed!
        if(!is_dir(self::$dir)){ return $config; }

        if(!is_readable(self::$dir.self::$default_config.'.php')){ return $config; }

        $cfg_arr = (array) require_once self::$dir.self::$default_config.'.php';

        if(self::$check_keys){

            if(!empty($_SERVER[self::$environment_key]) && is_readable(self::$dir.$_SERVER[self::$environment_key].'.php')){
                $env_arr = (array) require_once self::$dir.$_SERVER[self::$environment_key].'.php';
                $cfg_arr = array_replace($cfg_arr,$env_arr);
            }

            if(!empty($_SERVER[self::$developer_key]) && is_readable(self::$dir.$_SERVER[self::$developer_key].'.php')){
                $dev_arr = (array) require_once self::$dir.$_SERVER[self::$developer_key].'.php';
                $cfg_arr = array_replace($cfg_arr,$dev_arr);
            }

            $config = (object) $cfg_arr;
        }
        return $config;
    }

    /**
     * Reload config, resets PHP's filesystem cache!
     *
     * @return object
     */

    public static function reload()
    {
        clearstatcache();
        return self::parse();
    }
}