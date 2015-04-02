<?php
define("CHAMP_TABLE", "champs");
define("ITEM_TABLE", "items");
// It is expected that champs and items have two columns (id and name)
connectDB("localhost", "root", "", "challenge"); // ENTER DATA HERE
$content = json_decode(file_get_contents("data/champs.json"), true);
$data = $content['data'];
// ADD CHAMPS TO DB
	foreach($data as $key => $value) {
		addToDB($value['id'], $value['name'], CHAMP_TABLE);
	}

$content = json_decode(file_get_contents("data/items.json"), true);
$data = $content['data'];
// ADD ITEMS TO DB
	foreach ($data as $key => $value) {
		addToDB($value['id'], $value['name'], ITEM_TABLE);
	}
	
disconnectDB();	
	function addToDB($id, $name, $table) {
		$return = mysql_query("INSERT INTO " .$table ." (id, name) VALUES (" .$id .", '" .mysql_escape_string($name) ."')");
		if (!$return) {
			echo "<p>";
			printf($id .":" .$name .' couldn\'t be added<br>');
			echo mysql_error();
			echo "</p>";
		}
	}
	
	function connectDB($server, $username, $password, $database) {
		mysql_connect($server, $username, $password, $database);
		mysql_select_db($database);
	}
	
	function disconnectDB() {
		mysql_close();
	}
?>