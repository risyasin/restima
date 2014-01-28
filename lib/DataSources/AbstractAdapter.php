<?php

/**
 * Restima - A RESTful PHP Micro-framework (http://restima.evrima.net/)
 *
 * @copyright Copyright (c) 2013 Yasin inat <risyasin@gmail.com>
 * @license   MIT
 */

namespace DataSources;

/**
 * Basic Adapter pattern. to bind routes onto collections.
 *
 * Class AbstractAdapter
 * @package DataSources
 */

abstract class AbstractAdapter
{
    /* @var $request  \Restima\Request */
    public $request;
    /* @var $response  \Restima\Response */
    public $response;

    /**
     * Captures parsed route info.
     *
     * @var $urls
     */
    public $urls;

    /**
     * Takes all GET/POST variables. also any replaces json request params as POST/PUT body
     *
     * @var $params
     */

    public $params;

    public $config;

    /**
     * Adapter initiator. takes request, response & config.
     *
     *
     * @param \Restima\Request $request
     * @param \Restima\Response $res
     * @param $config
     * @return mixed
     */

    abstract public function init(\Restima\Request $request,\Restima\Response $res, $config);

    /**
     * API Basic response method for any adapter. Most likely will be the same for any Adapter.
     *Restima
     * @param $data
     * @param null $message
     * @return object
     */

    public function api_response($data, $message = null)
    {
        if($message == null){ $message = null; }

        $out = (object) array("success" => true, "message" => $message, "data" => $data, "time" => 'N/A');

        if(!empty($this->request->request_time_float)){ $out->time = microtime(true) - $this->request->request_time_float; }

        return $out;
    }

    /**
     *  API Basic error response method. probably will be unchanged for any Adapter.
     *
     * @param null $message
     * @param int $code
     * @param array $dump
     * @return object
     */

    public function api_error($message = null, $code = 550, $dump = array())
    {
        if($message == null){ $message = 'Error message not defined'; }

        return (object) array("code" => $code, "success" => false, "error" => $message, "details" => $dump);
    }

    abstract public function stats();

    abstract protected function _data_list($limit,$page,$sort);

    abstract protected function _find($id);

    abstract protected function _insert($data);

    abstract protected function _update($id, $data);

    abstract protected function _delete($id);


}