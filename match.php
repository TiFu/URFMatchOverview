<?php
//$content = file_get_contents("data/summoner.json");
$content = file_get_contents("data/1136907431.json");
echo $content;
$content = json_decode($content, true);
echo "<pre>";
//var_dump($content['data']);
var_dump($content);
echo "</pre>";
?>