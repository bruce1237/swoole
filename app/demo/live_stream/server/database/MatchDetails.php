<?php
namespace server\database;

class MatchDetails
{
    private static ?MatchDetails $instance = null;
    protected array $matchDetail;

    protected array $defaultMatchDetail = [
        "2024-01-01:10:10"=>[
            "section1"=>[],
            "section2"=>[],
            "section3"=>[],
            "section4"=>[],
        ]
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
        $this->importMatchDetails($this->defaultMatchDetail);
    }

    public function importMatchDetails($matchDetails):void
    {
        $this->matchDetail=$matchDetails;
    }

    public function addMatchDetails(string $match, string $section, string $details): array
    {
        $this->matchDetail[$match][$section][]=$details;
        return $this->matchDetail;
    }

    public function getMatchDetails(): array
    {
        return $this->matchDetail;
    }





}