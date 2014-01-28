<?php

/**
 * Restima - A RESTful PHP Micro-framework (http://restima.evrima.net/)
 *
 * @copyright Copyright (c) 2013 Yasin inat <risyasin@gmail.com>
 * @license   MIT
 */

/** Autoloader - [Optional: autoload paths can be tweaked here]  */
require_once './bootstrap.php';

use Restima as Service;

/** Service base call. */
$app = new Service\Base();

/** Session support as a module. */
$app->load('Session');


$app->get('/', function($request,$response){
    /** @var $request Restima\Request */
    /** @var $response Restima\Response */
    $response->send(array('status'=>'RESTful API is running!'));

});


$app->get('/help', function(Service\Request $request, Service\Response $response) use($app) {

    $response->send("Help documentation will be placed here");

});

$app->get('/test/{varname}', function(Service\Request $request, Service\Response $response) use($app) {
    echo '<pre style="font: normal 12px Lucida Sans;">';
    print_r($request);
    echo '</pre>'; exit;
    $response->send("Help documentation will be placed here");

});


$app->run();