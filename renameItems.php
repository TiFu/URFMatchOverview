<?php
	include("config/config.php");
	$mysqli = mysqli_init();
$mysqli->real_connect(SERVER, DB_USER, DB_PW, DATABASE);
	$champs = $mysqli->query("SELECT * FROM " .CHAMP_TABLE);
	while ($champ = $champs->fetch_assoc()) {
		$files = scandir("images/champion/");
		$minDist = PHP_INT_MAX;
		$pic = "";
		foreach ($files as $file) {
			if (!is_dir($file)) {
				$dist = levenshtein($champ["name"] .".png", $file);
				if ($dist < $minDist) {
					$minDist = $dist;
					$pic = $file;
				}
			}
		}
		if (!rename("images/champion/" .$pic, "images/champion/" .$champ["name"] .".png")) {
			echo $pic . " couldn't be renamed<br>";
		}
		if (!rename("images/champion/" .str_replace(".png", "46.png", $pic), "images/champion/" .$champ["name"] ."46.png")) {
			echo "small pic " .$pic ." couldn't be renamed<br>";
		}
	}
?>