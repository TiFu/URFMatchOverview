<?php
include_once("functions.php");
include_once("api_key.php");

// TODO change to correct url for riot API
$server = "euw";
$matchid = "2040109985"; 
$url = "https://" . $server . ".api.pvp.net/api/lol/" . $server . "/v2.2/match/" . $matchid . "?includeTimeline=true&api_key=" .API_KEY;

// Calculate current Time rounded down to last minute mark
$currentTime = time() - (time() % 60);
// TODO uncomment when api is released
//$url = $url .$currentTime; // add time as parameter to url (as required by the api).

$success = storeUrlToFileSystem($url, "matchIds/" .$currentTime .".json"); // store it

// Write error message to log file if something failed
if (!$success) {
	$handle = fopen("log.txt", "a");
	fwrite($handle, "Failed to download at "  .$currentTime ."\n");
	echo "Failed to download match ids";
	fclose($handle);
}
?>