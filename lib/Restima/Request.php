<?php

namespace Restima;

/**
 * Request parser for basic framework to serve as a rest. Shortly parses only for REST requirements
 *
 * Class Request
 * @package Restful
 */

class Request
{
    /**
     * URL elements.
     *
     * @var array
     */
    public $urls = array();
    
    /**
     * The HTTP method used.
     *
     * @var string
     */
    public $method;
    
    /**
     * Any parameters sent with the request.
     *
     * @var array
     */
    public $params;

    /**
     * Collections of named url variables.
     *
     * @var $vars
     */

    public $vars;

    public $request;

    public $protocol;

    public $request_time;

    public $request_time_float;

    public $server_name;

    public $host;

    public $user_agent;

    /**
     * Constructor
     *
     * @return Request
     */

    public function __construct()
    {
        $this->urls = array();

        $this->parse();

        return $this;
    }


    /**
     * Request parser
     */

    private function parse()
    {
        $this->urls = array();

        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);

        $script_path = explode('/',trim($_SERVER['SCRIPT_NAME'],'/'));

        if(end($script_path) == 'index.php'){ array_pop($script_path); }

        if(strlen(trim($script_path[0])) == 0){ array_shift($script_path); }

        $request_uri = $_SERVER['REQUEST_URI'];

        if(strpos('?',$request_uri) !== -1){ $request_uri = explode('?',$request_uri); $request_uri = $request_uri[0]; }

        $url_path = explode('/', trim($request_uri, '/'));

        $parts = array_diff_assoc($url_path,$script_path);

        if(count($parts)>0){

            $this->urls = array_values($parts);

            $this->request = $this->method.' /'.implode('/',$parts);

        } else {

            $this->request = $this->method.' /';
        }

        if(!empty($_SERVER['SERVER_NAME'])){ $this->server_name = $_SERVER['SERVER_NAME']; }

        if(!empty($_SERVER['HTTP_HOST'])){ $this->host = $_SERVER['HTTP_HOST']; }

        if(!empty($_SERVER['SERVER_PROTOCOL'])){ $this->protocol = $_SERVER['SERVER_PROTOCOL']; }

        if(!empty($_SERVER['HTTP_USER_AGENT'])){ $this->user_agent = $_SERVER['HTTP_USER_AGENT']; }

        if(!empty($_SERVER['REQUEST_TIME'])){ $this->request_time = $_SERVER['REQUEST_TIME']; }

        if(!empty($_SERVER['REQUEST_TIME_FLOAT'])){ $this->request_time_float = $_SERVER['REQUEST_TIME_FLOAT']; }

        if($this->method == 'GET'){

            $this->params = $_GET;

        } elseif ($this->method == 'POST') {

            if(!empty($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE']=='application/x-www-form-urlencoded'){

                $this->params = $_POST;

            } else {

                    $raw = http_get_request_body();
                    try {

                        $this->params = $this->json_decode($raw);

                    } catch(\UnexpectedValueException $e) {

                        $this->params = $raw;

                    }
            }

        } elseif ($this->method == 'PUT') {

            parse_str(file_get_contents('php://input'), $this->params);

        } else {

            $this->params = array();

        }

    }

    /**
     * Path parser to match requested & defined routes!
     *
     * @param $request_path
     * @return array
     */

    public function parse_path($request_path)
    {
        $script_path = explode('/',trim($_SERVER['SCRIPT_NAME'],'/'));

        if(end($script_path) == 'index.php'){ array_pop($script_path); }

        if(strlen(trim($script_path[0])) == 0){ array_shift($script_path); }

        $path_parts = explode('/', ltrim($request_path, '/'));

        if(count($path_parts) == 1 && strlen($path_parts[0]) == 0){ return array(); }

        return $path_parts;
    }

    /**
     * Path matcher for framework.
     *
     * @param $path
     * @param $method
     *
     * @return boolean
     */

    public function match($path,$method)
    {
        if($method == $this->method){

            $path = explode('/',ltrim($path,'/'));

            if(count($path) == 1 && $path[0]=='' && count($this->urls) == 0){ return true; }

            if(count($path)>0 && count($path) == count($this->urls)){

                $matching = array();

                foreach($path as $i => $pp) {
                    if(!empty($pp[0])){

                        if($pp[0] == '{'){ $this->vars[rtrim(ltrim($pp,'{'),'}')] = $this->urls[$i]; $pp = $this->urls[$i]; }

                        $matching[$i] = $pp;
                    }

                }

                return (count(array_diff($this->urls,$matching)) == 0)?true:false;

            } else { return false; }

        } else { return false; }

    }

    /**
     * JSON decode wrapper
     *
     * @param $data
     * @return mixed
     */


    private function json_decode($data)
    {
        return json_decode($data);
    }

}
