<?php

$matchid = htmlspecialchars(filter_input(INPUT_POST, 'matchid'));
$server = htmlspecialchars(filter_input(INPUT_POST, 'server'));

// function to store the file on data foler 
function storeUrlToFilesystem($url, $localFile) {
    try {
        $data = file_get_contents($url);
        $handle = fopen($localFile, "w");
        fwrite($handle, $data);
        fclose($handle);
        return true;
    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
    }
    return false;
}

$down1 = "https://" . $server . ".api.pvp.net/api/lol/" . $server . "/v2.2/match/" . $matchid . "?includeTimeline=true&api_key=010ba2bc-2c40-4b98-873e-b1d148c9e379";
$des1 = "data/$matchid.json";
storeUrlToFilesystem($down1, $des1);
if (file_exists($des5) && filesize($des5) > 0) {

    echo "match id saved !";
} else {
    echo "match id error";
}
