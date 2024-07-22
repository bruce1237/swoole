<?php

namespace webpage;

use Redis;

session_start();

class Login
{


    public function login(array $data): bool
    {
        $mobile = $data['POST']['mobile'];
        $code = $data['POST']['code'];

        $redis = new Redis();
        $redis->connect("127.0.0.1");
        $redis->select(0);

        $redisCode = $redis->get($mobile);
        $code = substr($code, -4);
        $_SESSION['LOGIN'] = true;

        return $code == $redisCode;
    }

    public static function isLogin(): ?bool
    {
        if (isset($_SESSION['LOGIN']) && $_SESSION['LOGIN'] === true) {
            return true;
        }
    }

}
