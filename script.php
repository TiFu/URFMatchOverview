<?php
// 5 min = 300 sec
for ($time = 1427875200;$time <= 1427943600;$time += 300){
$down1 = "https://euw.api.pvp.net/api/lol/euw/v4.1/game/ids?beginDate=$time&api_key=010ba2bc-2c40-4b98-873e-b1d148c9e379";
$z0rrrxzp = file_get_contents($down1);
$gazaaaxz = json_decode($z0rrrxzp, true);
foreach ($gazaaaxz as $key => $value) {
    $matchid = "{$value}\r\n";
    file_put_contents("matchid.txt", $matchid . "\n", FILE_APPEND);
}
}
