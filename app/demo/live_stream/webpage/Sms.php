<?php

namespace webpage;

use Redis;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;


class Sms
{
    protected string $redisHost = "127.0.0.1";
    protected Redis $redis;
    protected int $verificationCodeLength = 4;


    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect($this->redisHost);
        $this->redis->select(0);
    }

    /**
     * using Coroutine to operate redis
     * issue: 
     * the code will carry to execute to the code block after coroutine
     * which will lead to send the code to client but failed to cache it
     ************ for details see sendSmsVerificationFailed ************
     * 
     * fix:
     * using Channel(1): 1 means only allow 1 msg exists inside channel
     * push the code to the channel once cache success
     * or push false to the channel once cache failed
     * 
     * when coroutine complete, check cache status before send the code out
     * 
     * 
     * from the Log - info, the system flow as below, the Redis is working 
     * on the background
     * 
     * Log - Start process Redis Request
     * Log - check if code has been cached
     * Log - Cache status: bool(true)
     * Log - Cache Success, push to channel with Code
     * array(1) {[123]=>string(4) "05cd"}
     * Log - Cache completed
     *
     * @param array $data
     * @return string
     */
    public function sendSmsVerification(array $data): string
    {

        $mobile = $data["GET"]['mobile'];

        if ($code = $this->getVerificationCode($mobile)) {
            return "EXT: " . $code;
        }
        $code = $this->generateVerificationCode($this->verificationCodeLength);

        // use channel to store code cache result
        $channel = new Channel(1);

        // using Coroutine to run Redis as Redis is so slow 5 sec delay
        Coroutine::create(function () use ($mobile, $code, $channel) {

            echo "Log - Start process Redis Request\n";

            $cache = $this->cacheVerificationCode($mobile, $code);

            echo "Log - Cache status: ";
            
            if ($cache) {
                echo "Log - Cache Success, push to channel with Code\n";
                $channel->push([$mobile => $code]);
                $channel->push([$mobile => $code . "ccc"]);
            } else {
                echo "Log - Cache Success, push to channel with False\n";
                $channel->push([$mobile => false]);
            }

            echo "Log - Cache completed\n";
        });

        echo "Log - check if code has been cached\n";

        $success = $channel->pop();
        var_dump($success);
        if (isset($success[$mobile]) && $success[$mobile]) {
            return "NEW: " . $success[$mobile];
        }
        return "ERR - Verification Failed, please try again!";
    }




    /**
     * this Coroutine call failed.
     * as when the Redis returns false when cache fails
     * the code has already send to client, 
     * the ERR - ** msg never got chance to send
     *
     * @param array $data
     * @return string
     */
    public function sendSmsVerificationFailed(array $data): string
    {
        $mobile = $data["GET"]['mobile'];

        if (!$code = $this->getVerificationCode($mobile)) {
            $code = $this->generateVerificationCode($this->verificationCodeLength);

            // using Coroutine to run Redis as Redis is so slow 5 sec delay
            Coroutine::create(function () use ($mobile, $code) {

                $cache = $this->cacheVerificationCode($mobile, $code);

                // check if code has been cached
                if ($cache) {
                    echo "Cache Success\n";
                } else {
                    echo "Cache failed\n";
                    return "ERR - Verification Failed, please try again!";
                }

                echo "Cache completed\n";
            });

            echo "while waiting for Redis doing in background\n";
            echo "here is doing something else\n";
        }
        // send out code 
        return "EXT: " . $code;
    }



    protected function cacheVerificationCode(string $mobile, string $code, int $ttl = 60): bool
    {
        echo "cacheVerificationCode Log - connect to Redis\n";
        $redis = new Redis();
        $redis->connect($this->redisHost);
        $redis->select(0);

        workVerySlow();

        echo "cacheVerificationCode Log - set code:($code) with Mobile:($mobile) into cache\n";

        $rs = $redis->set($mobile, $code, ["NX", "EX" => $ttl]);
        $rs = false;
        $rs = true;
        return $rs;
    }

    protected function generateVerificationCode(int $length): string
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


    /**
     * using task model to send SMS code
     *
     * @param array $data
     * @return string
     */
    public function sendSmsVerificationTaskMode(array $data): string
    {
        $mobile = $data["GET"]['mobile'];

        if ($code = $this->getVerificationCode($mobile)) {
            return "EXT: " . $code;
        }
        $taskData = [
            "taskName" => "sendSmsCode",
            "data" => [
                "mobile" => $data['GET']['mobile'],
                "code" => $this->generateVerificationCode($this->verificationCodeLength),
            ],
        ];


        $httpServer = $data['Server'];

        echo "sendSmsVerificationTaskMode Log - start task\n";
        $httpServer->task($taskData);

        echo "sendSmsVerificationTaskMode Log - task Completed\n";

        echo "sendSmsVerificationTaskMode Log - cache Code\n";
        $this->cacheVerificationCode($taskData['data']['mobile'], $taskData['data']['code']);

        return "task: " . $taskData['data']['code'];
    }
}
