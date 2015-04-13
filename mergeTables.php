<?php
set_time_limit(180);
include("config/config.php");
// Use mysqli (object orientation ftw)
$mysqli = mysqli_init();
$mysqli->real_connect(SERVER, DB_USER, DB_PW, DATABASE);

$servers = array("br", "eune", "euw", "kr", "lan", "las", "na", "oce", "ru", "tr");

foreach ($servers as $server) {
	$query = $mysqli->query("SELECT * FROM " .$server);
	echo "<h2>Working on " .$server ."</h2>";
	while ($line = $query->fetch_assoc()) {
		$string = "INSERT INTO average (championId, region, numgames, pick, kda, ban, winrate, kills, death, assist, fb, dk, tk, qk, pk, ks, cs, towerdestroy, wardplace, truedmg, phycdmg, magicdmg, totaldmg) VALUES (";
		foreach ($line as $key => $value) {
			if ($key == "name") {
				continue;
			}
			
			if ($key == "id") {
					$string .= $value .",";
					$string .= "'" .$server ."',";
			} else if ($key == "numgames") {
				$string .= $value .",";
			} else {
				$string .= "'" .$value ."',";
			}
		}
		$string = substr($string, 0, -1);
		$string .= ")";
		echo $line["id"] .": " .$mysqli->query($string) ."<br>";
		echo mysqli_error($mysqli);
	}
}
?>