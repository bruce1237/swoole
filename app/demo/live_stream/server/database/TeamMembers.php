<?php

namespace server\database;

class TeamMembers
{
    private static ?TeamMembers $instance = null;
    private array $defaultMembers = [
        "teamA" => [
            "memberAA" => ["age"=>19, "position"=>1, "number"=>11],
            "memberAB" => ["age"=>19, "position"=>2, "number"=>12],
            "memberAC" => ["age"=>19, "position"=>3, "number"=>13],
            "memberAD" => ["age"=>19, "position"=>4, "number"=>14],
        ],
        "teamB" => [
            "memberBA" => ["age"=>20, "position"=>1, "number"=>21],
            "memberBB" => ["age"=>20, "position"=>2, "number"=>22],
            "memberBC" => ["age"=>20, "position"=>3, "number"=>23],
            "memberBD" => ["age"=>20, "position"=>4, "number"=>24],
        ],
        "teamC" => [
            "memberCA" => ["age"=>21, "position"=>1, "number"=>31],
            "memberCB" => ["age"=>21, "position"=>2, "number"=>32],
            "memberCC" => ["age"=>21, "position"=>3, "number"=>33],
            "memberCD" => ["age"=>21, "position"=>4, "number"=>34],
        ],
        "teamD" => [
            "memberDA" => ["age"=>22, "position"=>1, "number"=>41],
            "memberDB" => ["age"=>22, "position"=>2, "number"=>42],
            "memberDC" => ["age"=>23, "position"=>3, "number"=>43],
            "memberDD" => ["age"=>24, "position"=>4, "number"=>44],
        ],

    ];


    protected array $teamMembers;


    private function __construct()
    {
        $this->importMembers($this->defaultMembers);
    }

    public static function getInstance()
    {
        if (self::$instance ===  null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function importMembers(array $teamMembers): void
    {
        foreach ($teamMembers as $teamName => $members) {
            $this->teamMembers[$teamName] = $members;
        }
    }
    public function getTeamMember(string $teamName): array
    {
        return $this->teamMembers[$teamName];
    }
}
