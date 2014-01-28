<?php

/**
 * Restima - A RESTful PHP Micro-framework (http://restima.evrima.net/)
 *
 * @copyright Copyright (c) 2013 Yasin inat <risyasin@gmail.com>
 * @license   MIT
 */


namespace Restima\Modules;

class Session {

    public function load(\Restima\Base $base)
    {
        session_start();

        $base->set('session_started',true);
        $base->set('session_id',session_id());

    }



}


