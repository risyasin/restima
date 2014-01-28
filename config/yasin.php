<?php


return array(
    'mongo' => (object) array(
                'uri' => 'mongodb://yasin:3262062@127.0.0.1:27017',
                'database' => 'production',
                'authdb' => 'admin'
            ),
    'memcached' => (object) array(
                'servers' => array('localhost:11211')
            ),

    'row_limit'      => 100
);
