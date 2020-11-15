<?php

namespace core;

use core\pattern\Singleton;

class Request extends Singleton {

    public $server;

    public $values;

    /**
     * 
     */
    public static function capture(){
        $request = static::getInstance();
        $request->mapServer();
        $request->mapValues();
        return $request;
    }


    /**
     * 
     */
    protected function mapServer(){
        $this->server = (object) $_SERVER;
        unset($_SERVER);
    }


    /**
     * 
     */
    protected function mapValues(){

        if (\in_array($this->server->REQUEST_METHOD, array("PUT", "DELETE"))) {
            parse_str(file_get_contents('php://input'), $_REQUEST);
        }

        $this->values = (object) $_REQUEST;

        unset($_GET);
        unset($_POST);
        unset($_REQUEST);
    }

    public function __get($name){

        $values = $this->values;

        if (is_null($values) || !isset($values->$name)) {
            return NULL;
        }

        return $values->$name;
    }   

}