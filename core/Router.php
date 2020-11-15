<?php

namespace core;

use core\Request;
use core\pattern\Singleton;
use ReflectionMethod;

class Router extends Singleton {

    /**
     * 
     */
    private  $routes = array();

    /**
     * 
     */
    static $default_method = "GET";

    /**
     * 
     */

    private $route;

    /**
     * 
     */

    private $params = array();

    /**
     * 
     */
    const METHODS = ["GET", "POST", "PUT", "DELETE"];
    
    /**
     * 
     */
    private static function add($path, $method, $params = []){

        if (\is_array($method)) {
            $params = $method;
            $method = static::$default_method;
        }
        
        $route = static::getInstance();
        $route->addRoute($path, $method, $params);
    }

    /**
     * 
     * 
     */
    public function addRoute($path, $method, $params){

        // Convert the route to a regular expression: escape forward slashes
        $path = preg_replace('/\//', '\\/', $path);

        // Convert variables e.g. {controller}
        $path = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $path);

        // Convert variables with custom regular expressions e.g. {id:\d+}
        $path = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $path);

        // Add start and end delimiters, and case insensitive flag
        $path = '/^' . $path . '$/i';

        $this->routes[$path][] = array_merge(array(
            "method" => strtoupper($method),
        ), $params);
    }

    /**
     * 
     * 
     */
    public function getRoutes(){
        return $this->routes;
    }

    /**
     * 
     */

    public function dispatch($url, $method){
        $url = $this->removeQueryStringVariables($url);
        if ($this->match($url, $method)) {
            $controller = $this->route->controller[0];
            $action = $this->route->controller[1];
            $parameters = [];

            $stacks = [];



            if (\class_exists($controller)) {
                $controller_obj = new $controller;

                $middlewares = $controller_obj->getActionMidlewares($action);

                $request = Request::getInstance();
              
                if (\is_array($middlewares) && \count($middlewares) > 0) {
                    foreach($middlewares as $middleware) {
                        $middleware = new $middleware;
                        $stacks[] = function() use ($middleware, &$stacks, &$request)  {
                            return $middleware->handle($request, function(&$request) use (&$stacks) {
                                next($stacks)($request);
                            });
                        };
                    }
                }

                $reflect = new ReflectionMethod($controller, $action);
                
                foreach($reflect->getParameters() as $parameter) {
                    if ($parameter->getClass()->name === Request::class) {
                        \array_push($parameters, $request);
                    } elseif (is_null($parameter->getClass()->name)) {
                        \array_push($parameters, isset($this->params[$parameter->getName()]) ? $this->params[$parameter->getName()] : NULL);
                    }
                }

                $stacks[] = function() use ($controller_obj, $action, $parameters) {
                   return $controller_obj->callAction($action, $parameters);
                };

                
                $current = \current($stacks);
                $current();

            } else {
                throw new \Exception("controller $controller not exits");
            }


        } else {
            throw new \Exception("not match router");
        }
    }

    /**
     * 
     */
    protected function match($url, $method){
        
        foreach ($this->routes as $path => $info) {

            if (\preg_match($path, $url, $matches)) {
                $key = array_search($method, array_column($info, "method"));
                if ($key !== false) {
                    $this->route = (object) $info[$key];
                    foreach ($matches as $key => $match) {
                        if (\is_string($key)) {
                            $this->params[$key] = $match;
                        }
                    }
                    return true;
                }
                return false;
            }
        }
        return false;
    }

     /**
      * 
      */
    protected function removeQueryStringVariables($url)
    {
        if ($url != '') {
            $parts = explode('?', $url, 2);

            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }

    public static function __callStatic($name, $arguments){
       
        if (\in_array(strtoupper($name), static::METHODS)) {
            static::add($arguments[0], $name, $arguments[1]);
        }
    }
    
}