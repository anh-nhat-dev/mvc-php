<?php

use app\Controllers\{UserController, AuthController};
use core\Router;

Router::get("/login", ["controller" => [AuthController::class, "login"]]);
Router::get("/logout", ["controller" => [AuthController::class, "logout"]]);
Router::post("/login", ["controller" => [AuthController::class, "postLogin"]]);
Router::get("/cms/users", ["controller" => [UserController::class, "index"]]);