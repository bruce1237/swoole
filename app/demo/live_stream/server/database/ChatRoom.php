<?php
namespace server\database;

class ChatRoom
{
    private static ?ChatRoom $instance = null;
    protected array $chatRoom;

    protected array $defaultChatRoomChat = [
        "2024-01-01:10:10"=>[
            ["ChatA"=>"hi"],
            ["ChatB"=>"hello"],
        ],
    ];
    
    public static function getInstance()
    {
        if (self::$instance ===  null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->importChat($this->defaultChatRoomChat);
    }

    public function importChat($chatRoomChat): void
    {
        foreach($chatRoomChat as $charRoom => $chat) {
            $this->chatRoom[$charRoom] = $chat;
        }
    }

    public function addChat(string $charRoom, array $chat): bool
    {
        $this->chatRoom[$charRoom][]=$charRoom;
        return true;
    }
}