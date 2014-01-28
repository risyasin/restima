<?php

namespace Restima\Output;

class Json
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
        header('Content-Type: application/json');
    }

}