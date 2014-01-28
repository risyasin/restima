<?php

require_once './bootstrap.php';

use Restima as Service;

$app = new Service\Base();

$app->load('Session');


$app->get('/', function($req,$res) use($app) {

    $app->response->send(array('status'=>'RESTful API is running!'));

});



$app->get('/help', function($req,$res) use($app) {

    $app->response->output_type = 'html';

    $app->response->send("Help documentation will be placed here");

});

$app->run();