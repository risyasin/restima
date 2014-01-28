<?php

namespace Restima\Output;

class Csv
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
        $out = array();

        $this->head($data);

        if(gettype($data->data) == 'object' && $data->data->rows){

            $out = $this->handle_rows($data->data->rows);

        } elseif(is_array($data->data)){

            $out[] = implode(';',$data->data);

        } else {

            $out = $data->data;

        }

        return implode("\r\n",$out);
    }

    public function headers()
    {

        header('Content-Type: text/csv');

    }


    private function head($data)
    {

        header('X-Api-Response: '.(string) $data->success); flush();

        header('X-Api-Message: '.(string) $data->message); flush();

    }

    private function handle_rows($data)
    {
        $out = array();

        $keys_header = false;

        foreach($data as $k => $v) {

            if(!$keys_header){

                header('X-Api-Keys: '.(string) implode(',',array_keys($v))); flush();

                $keys_header = true;

            }

            $out[] = $k.';"'.implode('";"',$v).'"';

        }

        return $out;

    }

}