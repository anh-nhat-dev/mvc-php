<?php

namespace core\pattern;

abstract class Singleton {
    private static $instances = array();

    protected function __construct(){
        
    }

    /**
     * 
     * 
     */
    public static function getInstance(){
        $class = \get_called_class();
        if (!isset(static::$instances[$class])) {
        static::$instances[$class] = new static;
       }

       return static::$instances[$class];
    }

    private function __clone() {}
    private function __wakeup() {}
}