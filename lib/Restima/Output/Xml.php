<?php

/**
 * Restima - A RESTful PHP Micro-framework (http://restima.evrima.net/)
 *
 * @copyright Copyright (c) 2013 Yasin inat <risyasin@gmail.com>
 * @license   MIT
 */

namespace Restima\Output;

class Xml
{
    /**
     * Response data.
     *
     * @var string
     */
    protected $data;

    /**
     * Constructor.
     */
    public function __construct()
    {

    }

    /**
     * Render the response as JSON.
     *
     * @param object
     * @return string
     */
    public function render($data)
    {
        return json_encode($data);
    }

    public function headers()
    {
        header('Content-Type: application/xml');
    }

}