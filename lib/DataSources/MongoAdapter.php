<?php



namespace DataSources;

/**
 * MongoAdapter for REST service. Makes the basic/proper bindings for route to collection!
 *
 * Class MongoAdapter
 * @package DataSources
 */


class MongoAdapter extends AbstractAdapter {

    /* @var $response  \Restima\Response */
    public $response;

    /* @var $request   \Restima\Request */
    public $request;

    public $urls;

    public $params;

    public $config;

    /* @var $cacher \Memcached */
    public $cacher = null;

    /* @var $db  \MongoClient */
    public $db;

    /* @var $source \MongoCollection */
    public $source = null;

    /**
     *
     * Adapter Init Call. starts the specified adapter to be use as a Restful Service.
     * this call is required for any adapter. Check out DataSources\AbstractAdapter
     *
     * @param \Restima\Request $req
     * @param \Restima\Response $res
     * @param $config
     * @return null
     */

    public function init(\Restima\Request $req, \Restima\Response $res, $config)
    {
        $this->request = $req;

        $this->response = $res;

        if(!$config->mongo){ $this->not_configured(); }

        $this->config = $config->mongo;

        if($config->row_limit){ $this->config->row_limit = $config->row_limit; }

        if(!empty($this->cache_expire)){

            $this->cacher = new \Memcached();

            if(!empty($config->memcached->servers)){

                foreach($config->memcached->servers as $server) {

                    $server = explode(':',$server); $this->cacher->addServer($server[0], $server[1]);

                }

            }

        }

        $this->urls = $req->urls;

        $this->params = (object) $req->params;

        $mongo = new \MongoClient($this->config->uri, array("db" => $this->config->authdb));

        // preserve & inherit DB Class to child!
        $this->db = $mongo->selectDB($this->config->database);

    }

    /**
     * Config error handler
     *
     */


    private function not_configured()
    {

        $this->api_error('Mongo connection not configured! Please check your settings or collection name!',502,$this->config);

    }


    /**
     * MApping a collection to the route!
     *
     * @param $cname
     */

    public function set_collection($cname){

        $this->source = $this->db->$cname;

    }


    /**
     *
     * Protected, data list function. handles GET /route
     *
     * @param null $limit
     * @param int $page
     * @param array $sort
     * @param array $query
     * @return object
     */


    protected function _data_list($limit = null, $page = 1,$sort = array(), $query = array()){

        if($limit == null){ $limit = $this->config->row_limit; }

        $skip = 0; if($page>1){ $skip = $limit * ($page-1); }

        if(!empty($this->cache_expire)){

            $cache_key = json_encode(array($this->urls[0],$page,$limit,$skip,$sort,$query));

            // $this->cacher->delete($cache_key);

            $cache = $this->cacher->get($cache_key);

            if($cache != null || strlen($cache) != 0){

                $cache->cache_key = $cache_key;

                return $this->api_response($cache, 'Query: '.json_encode($query).' [cached:'.$this->cache_expire.']');

            } else {

                $cur = $this->source->find($query)->limit($limit)->skip($skip)->sort($sort);

                $total = $cur->count();

                $response = (object) array(
                    "rows"  => iterator_to_array($cur),
                    "total" => (int) $total,
                    "limit" => (int) $limit,
                    "page_count"    => (int) ceil($total/$limit),
                    "current_page"  => (int) $page,
                    "sort"  => $sort,
                    "skip"  => (int) $skip,
                    "query" => $query
                );

                $this->cacher->set($cache_key, $response, time() + $this->cache_expire);

                return $this->api_response($response, 'Query: '.json_encode($query));

            }

        } else {
            $cur = $this->source->find($query)->limit($limit)->skip($skip)->sort($sort);

            $total = $cur->count();

            $response = (object) array(
                "rows"  => iterator_to_array($cur),
                "total" => (int) $total,
                "limit" => (int) $limit,
                "page_count"    => (int) ceil($total/$limit),
                "current_page"  => (int) $page,
                "sort"  => $sort,
                "skip"  => (int) $skip,
                "query" => $query
            );

            return $this->api_response($response, 'Query: '.json_encode($query));
        }

    }


    /**
     * Protected, Find method, to be used for GET /route/id
     *
     * @param null $id
     * @param array $fields
     * @return object
     */

    protected function _find($id = null,$fields = array())
    {

        $data = $this->source->findOne(array("_id"=>new \MongoId($id)),$fields);

        return $this->api_response($data);

    }

    /**
     * Protected insert method!
     *
     * @param $data
     * @return object
     */

    protected function _insert($data){

        if(is_object($data) || is_array($data)){

            try{

                $this->source->insert($data);

                return $this->api_response($data->_id);

            } catch(\MongoException $e) {

                return $this->api_error($e->getMessage(), $e->getCode(), (!empty($e->doc)?$e->doc:''));

            }

        } else {

            return $this->api_error('Data format is not proper to store!', 501, $data);

        }
    }

    /**
     * Protected update method!
     *
     * @param $id
     * @param $data
     * @return object
     *
     */

    protected function _update($id,$data){

        if(is_object($data) || is_array($data)){

            try{

                $this->source->update(array("_id"=>new \MongoId($id)),array('$set'=>$data));

                return $this->api_response($data);

            } catch(\MongoException $e) {

                return $this->api_error($e->getMessage(), $e->getCode(), (!empty($e->doc)?$e->doc:''));

            }

        } else {

            return $this->api_error('Data format is not proper to store!', 501, $data);

        }
    }

    /**
     * Protected! Replace method
     *
     * @param $id
     * @param $data
     * @return object
     */

    protected function _replace($id,$data){

        if(is_object($data) || is_array($data)){

            try{

                $this->source->update(array("_id"=>new \MongoId($id)),$data);

                return $this->api_response($data);

            } catch(\MongoException $e) {

                return $this->api_error($e->getMessage(), $e->getCode(), (!empty($e->doc)?$e->doc:''));

            }

        } else {

            return $this->api_error('Data format is not proper to store!', 501, $data);

        }
    }

    /**
     * Protected, Delete method
     *
     * @param $id
     * @return object
     */

    protected function _delete($id){

        $data = $this->source->remove(array("_id"=>new \MongoId($id)));

        return $this->api_response($data);

    }

    /**
     * Stats call for collection
     *
     *
     */

    public function stats(){

        $this->response->send($_SERVER);

    }

    /**
     * Unknown request handler
     *
     * @param int $code
     *
     */

    private function unknown_request($code = 599){

        $this->response->send($this->api_error('I do not know how to handle this request type!', $code, (array) $this->request));

    }


    /**
     * Rest GET method handler. uses "urls" to map different type of response.
     *
     *
     */

    public function get()
    {
        if(count($this->urls)==1){

            $limit = null; $sort = array(); $page = 1; $query = array();

            if(!empty($this->params->limit)){ $limit = $this->params->limit; }

            if(!empty($this->params->page) && is_numeric($this->params->page)) { $page = $this->params->page; }

            if(!empty($this->params->sort) && strpos(',',$this->params->sort) !== -1) {

                list($colname,$sort_type) = explode(',',$this->params->sort);

                if(in_array(strtolower($sort_type),array('d','desc'))){ $sort_type = -1; } else { $sort_type = 1; }

                $sort = array($colname=>$sort_type);
            }

            if(!empty($this->params->query)) { $query = (array) json_decode($this->params->query); }

            $this->response->send($this->_data_list($limit,$page,$sort,$query));

        } elseif(count($this->urls)==2) {

            $this->response->send($this->_find($this->urls[1]));

        } elseif(count($this->urls)==3) {

            $this->response->send($this->_find($this->urls[1],array($this->urls[2]=>true)));

        } else {

            $this->unknown_request(504);

        }
    }


    /**
     * REST POST method
     *
     */

    public function post()
    {
        if(count($this->urls)==1){

            $this->response->send($this->_insert($this->params));

        } elseif(count($this->urls)==2) {

            $this->response->send($this->_update($this->urls[1],$this->params));

        } else {

            $this->unknown_request(505);

        }
    }

    /**
     * REST PUT method!
     *
     */

    public function put()
    {
        if(count($this->urls)==1){

            //$this->response->send($this->_insert($this->params));

        } elseif(count($this->urls)==2) {

            $this->_delete($this->urls[1]);

            $this->response->send($this->_insert($this->urls[1],$this->params));

        } else {

            $this->unknown_request(505);

        }
    }

    /**
     * REST Delete method
     *
     */

    public function delete()
    {
        if(count($this->urls)==1){

            $this->response->send("Send 'confirm' parameter to delete all collection data!");

            if(!empty($this->params->confirm)){

                $data = $this->source->remove(array());

                $this->response->send($this->api_response($data,'Deleted'));

            }

        } elseif(count($this->urls)==2) {

            $this->response->send($this->_delete($this->urls[1]));

        } else {

            $this->unknown_request(506);

        }
    }

}