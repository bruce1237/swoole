<?php

namespace webpage;

use Redis;
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
        $matchName = $data['POST']["game"];
        $section = $data['POST']["section"];
        $content = date("H:i", time()) ." ". $data['POST']["content"];
        $matchDetails = $this->matchDetails->addMatchDetails($matchName, $section, $content);
        $this->recordMatch($matchDetails);

        // get connected client
        $redis = getRedis();
        $connectedClients = $redis->sMembers("connectedClientIds");
        
        $content = json_encode(["section"=>$section, "content"=>$content]);

        
        // push to each client
        /**
         * some client was connected, but push failed
         */
        foreach($connectedClients as $fd){
            
            if ($data['Server']->isEstablished($fd)) {
                
            
                $data['Server']->push((int)$fd, $content);
            }
        }
        return true;
        
    }

    protected function recordMatch(array $matchesDetails): bool
    {
        $redis = getRedis();
        return $redis->set("matchDetails", json_encode($matchesDetails));
    }
}
