<?php

function callResource(string $uri, ?array $params = null): string
{
    $info = analysisUri($uri);

    require_once $info['file'];
    $obj = new $info['class'];
    return call_user_func_array([$obj, $info['function']], $params);
    

    
}

function analysisUri(string $uri): array|false
{

    $uri = explode("/", $uri);


    if (!$uri || sizeof($uri) != 3) {
        return false;
    }

    return [
        "class" => "webpage\\" . $uri[1],
        "function" => $uri[2],
        "file" => APP_PATH . ucfirst($uri[1]).".php",
    ];

}
