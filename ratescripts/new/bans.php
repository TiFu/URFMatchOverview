<?php
set_time_limit(0);
include("../../config/config.php");

$mysqli = mysqli_init();
$mysqli->real_connect(SERVER, DB_USER, DB_PW, DATABASE);

	// No dir. do stuff
	$match = json_decode(file_get_contents($_GET["file"]), true);
	$bans = getBans($match);
	$region = strtolower($match["region"]);
	
	// Update bans
	foreach ($bans as $ban) {
		$mysqli->query("UPDATE " .AVERAGE_TABLE . " SET banRate = (banRate + 1) WHERE championId = " .$ban ." AND region = " ."'" .$region ."'");
		echo mysqli_error($mysqli);
	}

// Returns a list of bans (championId)
function getBans($match) {
	$bans = array();
	$teams = $match["teams"];
	foreach ($teams as $team) {
		foreach ($team["bans"] as $ban) {
				$bans[] = $ban["championId"];
		}
	}
	return $bans;
}
?>