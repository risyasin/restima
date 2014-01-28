<?php

namespace Restima;

/**
 * Base for the service application. can be abstracted for any other service type
 *
 * Class Base
 * @package Restful
 */

class Base
{

    /* @var $config Config::parse */
    public $config;

    /* @var $request  \Restful\Request */
    public $request;

    /* @var $response  \Restful\Response */
    public $response;

    private $_before_response = array();

    private $_after_response = array();

    private $_last_call = null;

    /* @var $callable  \Closure */
    private $callable = false;

    /**
     * Constructor
     * @return Base
     */

    public function __construct()
    {

        $this->config = Config::parse();

        $this->request = new Request();

        $this->response = new Response();

        return $this;

    }

    /**
     * Chain call functions "Before response",
     *
     * @param callable $closure
     * @return Base
     *
     */


    public function before(\Closure $closure)
    {
        if($this->_last_call != null){

            $this->_before_response[$this->_last_call] = $closure;

        } else { trigger_error('\Restful\Base::before method can only be attacted to an HTTP route!',E_USER_ERROR); exit; }

        return $this;
    }

    /**
     * Chain call functions "After response",
     *
     * @param callable $closure
     * @return  Base
     *
     */

    public function after(\Closure $closure)
    {
        if($this->_last_call != null){

            $this->_after_response[$this->_last_call] = $closure;

        } else { trigger_error('\Restful\Base::after method can only be attacted to an HTTP route!',E_USER_ERROR); exit; }

        return $this;
    }

    /**
     * Module Loader
     *
     * Loader requires a load method. __constructor has been reserved for any parent that must extend the module.
     * Module Classes must be placed in Modules directory with a proper namespace under \Modules
     *
     * @param $module_name
     */

    public function load($module_name)
    {
        try {

            $module_name = '\\Restima\\Modules\\'.$module_name;

            $module = new $module_name();

            call_user_func(array($module,'load'),$this);

        } catch(\Exception $e) { $this->_unknown_module($e); }

    }


    /**
     * Add a task to execution chain via method GET
     *
     * @param $path
     * @param callable $closure
     * @return $this
     *
     */

    public function get($path, \Closure $closure){

        if($this->request->match($path,'GET')){

            $this->_last_call = $this->request->method.' '.$path;

            $this->callable = $closure;

        }

        return $this;
    }

    /**
     * Add a task to execution chain via method POST
     *
     * @param $path
     * @param callable $closure
     * @return $this
     *
     */

    public function post($path, \Closure $closure){

        if($this->request->match($path,'POST')){

            $this->_last_call = $this->request->method.' '.$path;

            $this->callable = $closure;

        }

        return $this;
    }

    /**
     * Add a task to execution chain via method PUT
     *
     * @param $path
     * @param callable $closure
     * @return $this
     *
     */

    public function put($path, \Closure $closure){

        if($this->request->match($path,'PUT')){

            $this->_last_call = $this->request->method.' '.$path;

            $this->callable = $closure;

        }

        return $this;
    }

    /**
     * Add a task to execution chain via method DELETE
     *
     * @param $path
     * @param callable $closure
     * @return $this
     *
     */

    public function delete($path, \Closure $closure){

        if($this->request->match($path,'DELETE')){

            $this->_last_call = $this->request->method.' '.$path;

            $this->callable = $closure;

        }

        return $this;
    }


    /**
     * Run all tasks for assigned path whether it's defined in index or User class in App
     *
     */

    public function run()
    {

        if($this->callable instanceof \Closure){

            try {

                $this->_run_tasks('_before_response');

                call_user_func_array($this->callable, array($this->request,$this->response));

                $this->_run_tasks('_after_response');

            } catch(\Exception $e) { $this->_unknown_request($e); }

        } else {

            $controller_name = $this->request->urls[0];

            if($controller_name && class_exists($controller_name)){

                try {

                    $controller = new $controller_name($this->request, $this->response, $this->config);

                    $action_name = strtolower($this->request->method);

                    $this->_run_tasks('_before_response');

                    call_user_func_array(array($controller, $action_name), array($this->request, $this->response, $this->config));

                    $this->_run_tasks('_after_response');

                } catch(\Exception $e) { $this->_unknown_request($e); }

            } else { $this->_not_found(); }

        }

    }

    /**
     * Task runner for chaining methods
     *
     * @param $task_type
     */

    private function _run_tasks($task_type)
    {

        foreach($this->{$task_type} as $task) {

            if($task instanceof \Closure){

                /* @var $task  Callable */
                $task($this->request,$this->response,$this->config);

            }

        }

    }

    /**
     * Framework DEV variable setter function, prevents method overloading and protects class variables!
     *
     * @param $name
     * @param $value
     */


    public function set($name,$value){

        if(!method_exists($this,$name) && !property_exists($this,$name)){
            $this->$name = $value;
        } else { trigger_error('This property name ('.$name.') is reserved, thus can not be set!'); exit; }

    }


    /**
     * Unknown request handler.
     *
     * @param \Exception $e
     */

    private function _unknown_request(\Exception $e)
    {

        $this->response->send(array("code" => 550, "success" => false, "error"=>"I do not know how to handle this request type!","details"=>$this->request,"dump"=>$e));
        exit;

    }


    /**
     * Unknown request type handler
     *
     */

    private function _not_found()
    {

        $this->response->send(array("code" => 404, "success" => false, "error"=>"Not found! Not my fault! Developer did not define this request type!","details"=>$this->request));
        exit;

    }

    /**
     * Unknown or undefined Module handler
     *
     * @param \Exception $e
     */

    private function _unknown_module(\Exception $e)
    {

        $this->response->send(array("code" => 551, "success" => false, "error"=>"Module error!","details"=>$this->request,"dump"=>$e));
        exit;

    }


    /**
     * Methods for rest of irrelevant stuff that must be protected! if there is any!
     */

}
