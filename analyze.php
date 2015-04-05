<?php
include("config/config.php");
include("classes/match.php");

$mysqli = mysqli_init();
$mysqli->real_connect(SERVER, DB_USER, DB_PW, DATABASE);

// Steps:
/**
 *	1. Step: Which data points?
 *	2. Step: How can i access them?
 *	3. Step: ???
 *	4. Step: PROFIT
 *
 */
 
 
 // POSSIBLY CALCULATE ALL VALUES IN MEMORY AND THEN SAVE THEM!!!
 // MAYBE USE JAVA TO DO THIS BECAUSE FUCK TIMEOUTS
 /**
  *	Adds a game to the database. Either recalculates the average or adds a new entry.
  * @param $gameId id of this game
  * @param $valueName	name of the value (e. g. winThresh for Threshs win rate)
  * @param $region	One of the lol regions. e. g. euw, eune, na,...
  * @param $value value for this particular game
  * @param $mysqli mysqli object
  */
 function addGame($gameId, $valueName, $region, $value, $mysqli) {
	$exists = $mysqli->query("SELECT * FROM " .ANALYZED_GAMES_TABLE ." WHERE gameId = " .$gameId ." AND region = '" .$region ."'")->num_rows();
	if ($exists != 0) { // Game already analyzed
		return false;
	}

	$queryResult = $mysqli->query("SELECT value_name FROM " .AVERAGE_TABLE ." WHERE value_name='" .$valueName ."'");
	 if ($queryResult->num_rows() == 0) {
		 $result = $mysqli->query("INSERT INTO " .AVERAGE_TABLE ." (value_name, region, average, games) VALUES ('" .$valueName ."','" .$region ."'," .$value .", 1)");
	 } else {
		 $result = $mysqli->query("UPDATE " .AVERAGE_TABLE ." SET average = (average * games + " .$value ."), games = (games+1) WHERE value_name='" .$valueName ."' AND region='" .$region ."'");
	 }

	 if ($result) {
		 $mysqli->query("INSERT INTO " .ANALYZED_GAMES_TABLE ." (gameId, region) VALUES (" .$gameId .", '" .$region "')");
		 return true;
	 } else {
		 return false;
	 }
 }
?>