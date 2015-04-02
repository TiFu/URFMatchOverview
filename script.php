<?php
// 5 min = 300 sec
$time = ((int) (time() / (5 * 60))) * 5 * 60 - 600;
saveMatchIds("euw", $time);
// add other servers 
function saveMatchIds($server, $beginDate) {
    $down1 = "https://$server.api.pvp.net/api/lol/$server/v4.1/game/ids?beginDate=$beginDate&api_key=010ba2bc-2c40-4b98-873e-b1d148c9e379";
    $z0rrrxzp = file_get_contents($down1);
    $gazaaaxz = json_decode($z0rrrxzp, true);
    foreach ($gazaaaxz as $key => $value) {
        $matchid = "{$value}\r\n";
        file_put_contents("$server" .date("d") ."_.txt", $matchid . "\n", FILE_APPEND);
    }
}
