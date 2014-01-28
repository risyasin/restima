<?php


class location_info extends DataSources\MongoAdapter
{

    public function __construct(\Restima\Request $req, \Restima\Response $resp, $config)
    {
        $this->init($req,$resp,$config);

        /* @var $this->metas    \MongoCollection */
        $this->metas = $this->db->{'locdata_metas'};

    }



    public function get()
    {
        if(count($this->urls)==2){

            $data = $this->db->{'locdatas'}->findOne(array("quarter_id"=>(int)$this->urls[1]));

            // $data->metas = $this->meta_map($data->proper_to);
            $this->metas->find(array())->toArray(function(){

            });

            $this->response->send($data);
        }

    }


    private function meta_map()
    {

        $return = (object) array("all" => array(), "mapped" => array());

        $metas = iterator_to_array($this->db->locdata_metas->find());

        foreach($metas as $meta){ $meta = (object) $meta;
            $return->all[$meta->mtype][$meta->meta_id] = (object) array("slug"=>$meta->value, "text"=> $meta->name);
        }

    }

}