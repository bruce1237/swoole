<?php
namespace webpage;

session_start();

class ChatRoom
{
    protected int $chartRoomPort = 9502;
    public function sendChat(array $data)
    {
        $user = $_SESSION['LOGIN_USER'];
        $chatServer = $data['Server'];
        $msg = [

            "game" => $data['POST']['game'],
            "msg" => $user . "@" . date("H:i", time()) . ": " . $data['POST']['chatMsg'],
        ];

        foreach($chatServer->ports as $port){
            if ($port->port == $this->chartRoomPort) {
                $chartRoomPort = $port;
            }
        }

        // scan ports for the chatRoom port object;
        foreach ($chartRoomPort->connections as $fd) {
            $chatServer->push($fd, json_encode($msg));
        }
        $this->saveChat($msg['game'], $msg['msg']);
    }

    public function saveChat(string $game, string $msg):void
    {
        $redis = getRedis();
        $redis->sAdd($game, $msg);
    }

    public function loadChat(array $data): array
    {
        echo "loadChat Log - Loading...\n";
        $redis = getRedis();
        return $redis->sMembers($data['POST']['game']);
    }
}
