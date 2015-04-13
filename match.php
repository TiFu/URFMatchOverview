<?php
//$content = file_get_contents("data/summoner.json");
$content = file_get_contents("ratescripts/new/data/64799329.json");
$content = json_decode($content, true);
echo "<pre>";
//var_dump($content['data']);
var_dump($content);
echo "</pre>";
?>