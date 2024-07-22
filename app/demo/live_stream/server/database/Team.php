<?php

namespace server\database;

class Team
{
    private static ?Team $instance = null;
    private array $defaultTeams = [
        "teamA" => [
            "type" => "W"
        ],
        "teamB" => [
            "type" => "E"
        ],
        "teamC" => [
            "type" => "W"
        ],
        "teamD" => [
            "type" => "E"
        ],
    ];

    /**
     * team structure
     * name: team name
     * type: W/E, W: west team, E:east team
     *
     * @var array
     */
    protected array $team;


    private function __construct()
    {
        $this->importTeam($this->defaultTeams);
    }

    public static function getInstance()
    {
        if (self::$instance ===  null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function importTeam(array $teams): bool
    {
        foreach ($teams as $teamName => $team) {
            $this->team[$teamName] = $team;
        }
        return true;
    }

    public function getTeamByName(string $teamName): array
    {
        return $this->team[$teamName];
    }

    public function getTeam(): array
    {
        return $this->team;
    }
}


