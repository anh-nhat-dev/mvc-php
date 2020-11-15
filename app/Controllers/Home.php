<?php

namespace app\Controllers;
use app\Models\User;
use core\{BaseController,View,Request};


class Home extends BaseController {

    private $userModel;

    public function __construct(){
        $this->userModel = new User;

    }
    
    public function add(){

        $title = "Trang chủ";

        View::render("home", compact("title"));
    }

    public function edit(Request $request){
        $name = $id;

        $users = $this->userModel->limit(3)->offset(6)->get();
        $user = $this->userModel->first();

        View::render("test.edit", compact("name", "users"));
    }
}