<?php

namespace core;

use core\Router;
use core\Request;
use core\Config;

class App {
    
    private $route;


    public function __construct(){
        $this->route = Router::getInstance();
    }

    /**
     * 
     */
    public function boot(){
        \session_start();
        Config::load(\dirname(__DIR__).'/config');
        $this->loadRouter();
    }


    /**
     * 
     */
    private function loadRouter(){
        require dirname(__DIR__) ."/routes/web.php";
    }

    /**
     * 
     */
    public function make(Request $request){
        $this->route->dispatch($request->server->REQUEST_URI, $request->server->REQUEST_METHOD);
    }

}