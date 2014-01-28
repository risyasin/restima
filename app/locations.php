<?php


class locations extends \DataSources\MongoAdapter
{

    public $cache_expire = 5; // seconds

    public function __construct($req, $resp, $config)
    {

        $this->init($req, $resp, $config);

        $this->set_collection('location_point');

    }

}