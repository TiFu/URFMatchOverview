<?php
$down1 = "https://euw.api.pvp.net/observer-mode/rest/featured?api_key=010ba2bc-2c40-4b98-873e-b1d148c9e379";
$z0rrrxzp = file_get_contents($down1);
$gazaaaxz = json_decode($z0rrrxzp, true);
foreach ($gazaaaxz['gameList'] as $key => $value) {
    $matchid = "{$value['gameId']}\r\n";
    file_put_contents("matchid.txt", $matchid."\n", FILE_APPEND);
}


    