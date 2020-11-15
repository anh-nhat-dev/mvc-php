<?php

namespace app\Middlewares;

class CheckUser {

    public function handle($request, $next) {

        $user_id = $_SESSION["user"];

        if (isset($user_id)) {
            return $next($request);
        }
        return \header("Location: /login");
    }
}