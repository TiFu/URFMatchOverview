<?php
include("../config/config.php"); // include constants and config

$mysqli = mysqli_init();
$mysqli->real_connect(SERVER, DB_USER, DB_PW);

$query = file_get_contents("challenge.sql");
$query = str_replace("challenge", DATABASE, $query);
$query = preg_replace("/--.*/", "", $query);

	if ($mysqli->multi_query($query)) {
		echo "Installation successful.";
	} else {
		echo "Installation failed.<br>";
	}
	echo mysqli_error($mysqli);
?>