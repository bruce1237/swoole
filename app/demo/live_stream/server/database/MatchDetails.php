<?php
namespace server\database;

class MatchDetails
{
    private static ?MatchDetails $instance = null;
    protected array $matchDetail;

    protected array $defaultMatchDetail = [
        "time"=>"2024-01-01:10:10",
        "teams"=>["teamA", "teamB"],
        "details"=>[
            "section1"=>[],
            "section2"=>[],
            "section3"=>[],
            "section4"=>[],
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
        $this->importMembers($this->defaultMembers);
    }





}