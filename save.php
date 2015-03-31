<?php
include_once("functions.php");
include_once("api_key.php");
$matchid = htmlspecialchars(filter_input(INPUT_POST, 'matchid'));
$server = htmlspecialchars(filter_input(INPUT_POST, 'server'));


$down1 = "https://" . $server . ".api.pvp.net/api/lol/" . $server . "/v2.2/match/" . $matchid . "?includeTimeline=true&api_key=" .API_KEY;
$des1 = "data/$matchid.json";
storeUrlToFilesystem($down1, $des1);
if (file_exists($des1) && filesize($des1) > 0) {

    echo "match id saved !";
} else {
    echo "match id error";
}
