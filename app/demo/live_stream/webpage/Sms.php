<?php
namespace webpage;

use Redis;

class Sms
{
    protected string $redisHost="127.0.0.1";
    protected Redis $redis;
    protected int $verificationCodeLength = 4;


    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect($this->redisHost);
        $this->redis->select(0);
    }

    public function sendSmsVerification(string $mobile): string
    {
        $code = $this->generateVerificationCode($this->verificationCodeLength);
        $this->cacheVerificationCode($mobile, $code);
        return $code;
    }

    protected function cacheVerificationCode(string $mobile, string $code, int $ttl =60): bool
    {
        return $this->redis->set($mobile, $code, ["EX"=>$ttl]);
    }

    protected function generateVerificationCode(int $length):string
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }

    public function getVerificationCode(string $mobile): ?string
    {
        return $this->redis->get($mobile);
    }




    public function __destruct()
    {
        $this->redis->close();
    }
}
