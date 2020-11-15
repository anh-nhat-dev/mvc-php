<?php

namespace core;

class View {

    public static function render($view, $args = []){

        \extract($args, EXTR_SKIP);

        $file = \dirname(__DIR__) . '/app/Views/'. \str_replace(".", DIRECTORY_SEPARATOR, $view). '.php';
        if (\is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }

    }
}