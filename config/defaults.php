<?php


return array(
    'mongo' => (object) array(
                'uri' => 'mongodb://user:pass@host:27017',
                'database' => 'demo',
                'authdb' => 'admin'
            ),
    'memcached' => (object) array(
                'servers' => array('localhost:11211')
            ),

    'row_limit'      => 100
);
