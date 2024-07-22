<?php

namespace webpage;

use server\database\MatchDetails;
use server\database\Matches;

class Narrator
{
    protected Matches $matches;
    protected MatchDetails $matchDetails;
    public function __construct()
    {
        $this->matches =  Matches::getInstance();
        $this->matchDetails = MatchDetails::getInstance();
    }
    public function getMatchNames(?array $data): array
    {

        return $this->matches->getAllMatchNames();
    }

    public function updateMatch(array $data): bool
    {
        var_dump($data['POST']);
        $matchName = $data['POST']["game"];
        $section = $data['POST']["section"];
        $content = $data['POST']["content"];
        $this->matchDetails->addMatchDetails($matchName, $section, $content);
        var_dump($this->matchDetails->getMatchDetails());
        return true;
    }
}
