<?php

$baseDir = dirname(__FILE__);

// Autoload class
spl_autoload_register(function($class) use ($baseDir){
    $file = $baseDir . '/' . strtr($class, "\\", DIRECTORY_SEPARATOR) . '.php';
    if (file_exists($file)) {
        include $file;
    }
});