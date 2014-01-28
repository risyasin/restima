<?php


return array(
    'mongo' => (object) array(
                'uri' => 'mongodb://admin:gX6nXzeN@SG-mongo2-748.servers.mongodirector.com:27017',
                'database' => 'production',
                'authdb' => 'admin'
            ),
    'memcached' => (object) array(
                'servers' => array('localhost:11211')
            ),

    'row_limit'      => 100
);
