<?php

namespace app\Controllers;
use core\{BaseController,View,Request};

class UserController extends BaseController {

    public function __construct(){
        $this->addMiddleware(\app\Middlewares\CheckUser::class, ["index"]);
    }

    public function index() {
        View::render("cms.users.index");
    }
}