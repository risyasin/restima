<?php

namespace Restima\Modules;


class Session {

    public function load(\Restima\Base $base)
    {
        session_start();

        $base->set('session_started',true);
        $base->set('session_id',session_id());

    }



}


