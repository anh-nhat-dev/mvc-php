<?php

namespace app\Controllers;

use app\Models\User;
use core\{View,BaseController, Request};


class AuthController extends BaseController {


    private $userModel;

    /**
     * 
     */
    public function __construct(){
        $this->userModel = new User;
        $this->addMiddleware(\app\Middlewares\CheckLogin::class, ["login", "postLogin"]);
        $this->addMiddleware(\app\Middlewares\CheckUser::class, ["logout"]);
    }
    /**
     * 
     */
    public function login() {
        View::render("cms.login");
    }

    /**
     * 
     */
    public function postLogin(Request $request){

        $user = $this->userModel->where("email", $request->email)->first();

        $error;

        if (!$user) {
            $error = "Không tìm thấy tài khoản";
        }

        if (is_null($error) && $user->password !== $request->password) {
            $error = "Mật khẩu không chính đúng";
        }

        if (is_null($error)) {
            $_SESSION["user"] = $user->id;
            return \header("Location: /cms/users");
        }
        View::render("cms.login", \compact("error"));
        
    }

    /**
     * 
     */

     public function logout(){
        unset($_SESSION['user']);
        return \header("Location: /login");
     }
}