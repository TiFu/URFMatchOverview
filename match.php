<?php
$content = file_get_contents("data/2040109985.json");
$content = json_decode($content);
echo "<pre>";
var_dump($content);
echo "</pre>";
?>