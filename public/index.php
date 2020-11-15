<?php


require_once dirname(__DIR__) . '/autoload.php';

use core\App;
use core\Request;

$app = new App;

$app->boot();

$app->make(Request::capture());