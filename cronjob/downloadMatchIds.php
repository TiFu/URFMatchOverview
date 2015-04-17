<?php

// 5 min = 300 sec
$time = ((int) (time() / (5 * 60))) * 5 * 60 - 600;
saveMatchIds("euw", $time);
saveMatchIds("eune", $time);
saveMatchIds("tr", $time);
saveMatchIds("na", $time);
saveMatchIds("ru", $time);
saveMatchIds("kr", $time);
saveMatchIds("lan", $time);
saveMatchIds("las", $time);
saveMatchIds("oce", $time);
saveMatchIds("br", $time);
// add other servers 
function saveMatchIds($server, $beginDate) {
    $down1 = "https://$server.api.pvp.net/api/lol/$server/v4.1/game/ids?beginDate=$beginDate&api_key={apiKeyHere}";
    $z0rrrxzp = file_get_contents($down1);
    $gazaaaxz = json_decode($z0rrrxzp, true);
    foreach ($gazaaaxz as $key => $value) {
        $matchid = "{$value}\r\n";
        file_put_contents("$server" .date("d") ."_.txt", $matchid . "\n", FILE_APPEND);
    }
}
