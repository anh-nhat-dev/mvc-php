<?php

namespace core;

abstract class BaseController {

    protected $middlewares = [];

    public function callAction($method, $parameters)
    {
        return call_user_func_array([$this, $method], $parameters);
    }

    protected function addMiddleware($middleware, $actions = []){
       
        foreach ($actions as $action) {
            $this->middlewares[$action][] = $middleware;
        }
    }

    public function getActionMidlewares($action){
        return isset($this->middlewares[$action]) ? $this->middlewares[$action] : [];
    }

}