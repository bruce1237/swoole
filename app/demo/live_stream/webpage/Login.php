<?php

namespace webpage;

use Redis;


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
        $_SESSION['LOGIN_USER'] = $data['POST']['mobile'];

        return $code == $redisCode;
    }

    public static function isLogin(): ?bool
    {
        if (isset($_SESSION['LOGIN']) && $_SESSION['LOGIN'] === true) {
            return true;
        }
    }

}
