<?php
set_time_limit(0);
include("../../config/config.php");

if (!isset($_GET["matchesFolder"])) {
	echo "Please specify the matchesFolder parameter.";
	exit(1);
}

$mysqli = mysqli_init();
$mysqli->real_connect(SERVER, DB_USER, DB_PW, DATABASE);

// Create 1240 entries (each region and each champion pair)
$count = $mysqli->query("SELECT * FROM " .AVERAGE_TABLE)->num_rows;
echo "Count: " .$count ."<br>";
if ($count != 1240) {
	echo "Truncating table";
	$mysqli->query("TRUNCATE TABLE " .AVERAGE_TABLE); // Clean it
	$servers = array("br", "eune", "euw", "kr", "lan", "las", "na", "oce", "ru", "tr");
	$champs = $mysqli->query("SELECT * FROM " .CHAMP_TABLE);
	while ($champ = $champs->fetch_assoc()) {
		foreach ($servers as $server) {
			$mysqli->query("INSERT INTO " .AVERAGE_TABLE ." (championId, region) VALUES (".$champ["id"] .",'" .$server ."')");
		}
	}
} else {
	echo "All fine.";
}

$servers = array("br", "eune", "euw", "lan", "las", "kr", "tr", "ru", "na", "oce");

if ($mysqli->query("SELECT * FROM " .WINRATE_TABLE)->num_rows != 10) {
	$mysqli->query("TRUNCATE TABLE " .WINRATE_TABLE);
	foreach ($servers as $server) {
		$mysqli->query("INSERT INTO " .WINRATE_TABLE ." (region, blueSideWins, redSideWins) VALUES ('" .$server ."', 0, 0)");
	}
}

$handle = opendir($_GET["matchesFolder"]);
$columns2 = $mysqli->query("SELECT * FROM " .AVERAGE_TABLE)->fetch_fields();
$columns = array();
	foreach ($columns2 as $column) {
		$columns[] = $column->name;	
	}

while (false !== ($entry = readdir($handle))) {
	if (is_dir($entry)) {
		continue;
	}
	// No dir. do stuff
	$match = json_decode(file_get_contents($_GET["matchesFolder"] ."/" .$entry), true);
	$bans = getBans($match);
	$winners = getWinners($match);
	$participants = $match["participants"];
	$region = strtolower($match["region"]);
		
	foreach ($participants as $participantId) {
		$participant = $participantId["stats"]; // use his stats^^
		$participant["pickRate"] = 1;
		$participant["winRate"] = $winners[$participantId["participantId"]];

		// do magic stuff to add to db
		$update = "UPDATE " .AVERAGE_TABLE ." SET ";
		foreach ($columns as $column) {
			if (isset($participant[$column]) && $column != "championId") {
				$update .= $column ."= ((" .$participant[$column] . " + " .$column ." * numberOfGames) / (numberOfGames+1)),";
			}
		}
		$update .= "numberOfGames = (numberOfGames + 1)";
		$update .= " WHERE championId = " .$participantId["championId"] ." AND region = '" .$region ."'";
		$mysqli->query($update); // Update champ
	}
		// add win rate
		$winningTeam = getWinningTeam($match);
		if ($winningTeam == 100) {
			$win = "blueSideWins";
		}  else if ($winningTeam == 200) {
			$win = "redSideWins";
		}
		$mysqli->query("UPDATE " .WINRATE_TABLE ." SET " .$win . " = (" .$win ."+1) WHERE region = '" .$region ."'");
	
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

function getWinningTeam($match) {
	foreach ($match["teams"] as $team) {
		if ($team["winner"]) {
			return $team["teamId"];
		}
	}
	
	return 0;
}

// Returns map from participantId -> hasWon?
function getWinners($match) {
	$champions = array();
	
	if ($match["teams"][0]["winner"]) {
		$winningTeam = $match["teams"][0]["teamId"];
	} 
	if ($match["teams"][1]["winner"]) {
		$winningTeam = $match["teams"][1]["teamId"];
	}
	
	foreach ($match["participants"] as $participant) {
			if (!isset($champions[$participant["participantId"]])) {
				$champions[$participant["participantId"]] = 0;
			}
			if ($participant["teamId"] == $winningTeam) {
				$champions[$participant["participantId"]] += 1;
			}
	}
	
	return $champions;
}
?>