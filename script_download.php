<?php
function storeUrlToFilesystem($url, $localFile) {
    try {
        $data = file_get_contents($url);
		if ($data === FALSE) { // check if HTTP request was successfull
			echo "Request failed<br>";
			return false;
		}
        $handle = fopen($localFile, "w");
        fwrite($handle, $data);
        fclose($handle);
        return true;
    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
    }
    return false;
}
$url = "http://elofight.com/{region}{day}_.txt";
// all regions
$regions = array();
$regions[] = "euw";
$regions[] = "br";
$regions[] = "na";
$regions[] = "eune";
$regions[] = "kr";
$regions[] = "lan";
$regions[] = "las";
$regions[] = "oce";
$regions[] = "ru";
$regions[] = "tr";

// Day
if (isset($_GET['day'])) {
	$day = $_GET['day'];
} else {
	$day = date("d") - 1;
}

// Replace day
$url = str_replace("{day}", $day, $url); 
echo "Requesting file for " .$day .' .April<br>';
	foreach($regions as $region) {
		echo "Current region: " .$region .'<br>';
		$copy = str_replace("{region}", $region, $url);
		echo "Requesting matchId-File: <br>";
		$content = file_get_contents($copy);
		// ids for server
		$ids = explode("\n\n", $content);
		mkdir($region);
		foreach ($ids as $id) {
			storeUrlToFileSystem("https://" .$region .".api.pvp.net/api/lol/" .$region ."/v2.2/match/" .rtrim($id) ."?includeTimeLine=true&api_key=503c05e8-3148-432d-b475-61ece09d1629", $region ."/" .rtrim($id) .".json");			
		}
	}
?>