<?php


class locdatas extends \DataSources\MongoAdapter
{

    public function __construct($req, $resp, $config)
    {
        $this->init($req,$resp,$config);
    }

}