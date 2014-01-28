<?php

namespace Restima;

use \Restima\Output as Output;


/**
 * Response Handler with auto content negotiator
 * Reads only http accept header values to determine response types.
 * Uses \Output namespace as handler.
 *
 * Class Response
 * @package Restful
 */

class Response
{

    public $accept_type;

    public $output_type;

    public $transfer_mode;

    public $engine;


    /**
     * Constructor.
     */

    public function __construct($transfer_mode = null)
    {

        $this->transfer_mode = $transfer_mode;

        if(!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'],',') !== -1){

            $expects = explode(',',$_SERVER['HTTP_ACCEPT']);

            // ignore RFC 2616 Fielding q value. Server/Developer speaks here!
            $this->accept_type = Config::$precedence[0];

            $this->output_type = 'json';

            foreach(Config::$precedence as $pt) {

                if(in_array($pt,$expects)){ $this->accept_type = $pt; break; }

            }

            $output_params = explode('/',$this->accept_type);

            if(!empty($output_params[1]) && strlen($output_params[1])>2){

                $this->output_type = $output_params[1];

            }
        }

        if($this->output_type === 'json'){

            $this->engine = new Output\Json();

        } elseif($this->output_type === 'xml'){

            $this->engine = new Output\Xml();

        } elseif($this->output_type === 'csv'){

            $this->engine = new Output\Csv();
        } else {
            // Default
            $this->engine = new Output\Json();

        }

    }

    /**
     *
     * Uses output adapter's send method to render output.
     *
     * @param $data
     *
     */


    public function send($data)
    {

        if($this->transfer_mode === 'chunked'){

            header("Transfer-encoding: chunked");

            flush();

        }

        $this->engine->headers();

        echo $this->engine->render($data);

    }

}