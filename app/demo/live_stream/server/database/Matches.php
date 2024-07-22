<?php

namespace server\database;

class Matches
{
    private static ?Matches $instance = null;
    private array $defaultMatches = [
        "2024-01-01:10:10" => [
            "teamA"=>[0], 
            "teamB"=>[0],
            "narrator"=>"Bo"
        ],
        "2024-01-01:11:10" => [
            "teamA"=>[0], 
            "teamC"=>[0],
            "narrator"=>"Bo"
        ],
        "2024-01-01:12:10" => [
            "teamA"=>[0], 
            "teamD"=>[0],
            "narrator"=>"Bo"
        ],
        "2024-01-01:13:10" => [
            "teamB"=>[0], 
            "teamC"=>[0],
            "narrator"=>"Bo"
        ],
        "2024-01-01:14:10" => [
            "teamB"=>[0], 
            "teamD"=>[0],
            "narrator"=>"Bo"
        ],
    ];


    protected array $matches;


    private function __construct()
    {
        $this->importMatches($this->defaultMatches);
    }

    public static function getInstance()
    {
        if (self::$instance ===  null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function importMatches(array $matches): bool
    {
        foreach ($matches as $date => $match) {
            $this->matches[$date] = $match;
        }
        return true;
    }

    public function getMatch(string $date): array
    {
        return $this->matches[$date];
    }

    public function getMatches(): array
    {
        return $this->matches;
    }
}


// $team = Team::getInstance();


// $r = $team->getTeam();
// echo "F:\n";
// var_dump($r);

// $team = Team::getInstance();
// $r = $team->getTeam();
// echo "S:\n";
// var_dump($r);
