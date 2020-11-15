<?php

namespace core;

use core\pattern\Singleton;

class Config extends Singleton {
    protected $configs = array();

    /**
     * 
     */
    public static function get($key, $default = NULL) {
      return  static::getInstance()->getConfig($key, $default);
    }

    /**
     * 
     */

     protected function exists($array, $key){
        return array_key_exists($key, $array);
     }

     /**
      * 
      */
     protected function getConfig($key, $default){
        if (!\is_string($key)) {
            return $default;
        }

        $configs = $this->configs;

        if (is_null($key)) {
            return $configs;
        }

        if ($this->exists($configs, $key)) {
            return $configs[$key];
        }

        if (strpos($key, ".") === false) {
            return $default;
        }

        foreach (\explode('.', $key) as $segment) {
           if (!is_array($configs) || !$this->exists($configs, $segment)) {
            return $default;
           }

           $configs = $configs[$segment];
        }

        return $configs;

     }

    /**
     * 
     */
    public static function load($config_dir){
        
        $files = scandir($config_dir);
        
        $config = static::getInstance();

        foreach($files as $file) {
            $file_path = $config_dir.'/'.$file;
            if (\is_file($file_path) && "php" == \pathinfo($file_path, PATHINFO_EXTENSION)) {
               $key =\basename($file_path, ".php");
               $values = require $file_path;

               if (isset($key) && is_array($values)) {
                $config->mergeConfig($key, $values);
               }
            }
        }

    }

    /**
     * 
     */
    protected function mergeConfig($key, $values){
        if (isset($this->configs[$key])) {
            $this->configs[$key] = array_merge($this->configs[$key], $values);
        } else {
            $this->configs[$key] = $values;
        }
    }

}