<?php

$db = new mysqli("localhost", "root", "", "challenge");

if ($db->connect_errno > 0) {
    die('Unable to connect to database [' . $db->connect_error . ']');
}

function file_ext_strip($filename) {
    return preg_replace('/.[^.]*$/', '', $filename);
}

$championnum = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 48, 50, 51, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64,
    67, 68, 69, 72, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 89, 90, 91, 92, 96, 98, 99, 101, 102, 103, 104, 105, 106, 107, 110, 111, 112, 113, 114, 115, 117, 119, 120, 121, 122, 126, 127, 131, 133, 134, 143, 150, 222, 154, 157, 161, 201, 201, 236, 238, 254, 266, 267, 268, 412, 421, 429, 432
);
$server = array("eune", "euw", "tr", "na", "kr", "lan", "las", "ru", "oce", "br");
$serverc = array("1199", "1166", "572", "1172", "840", "1205", "1212", "534", "1228", "1173");
$banrate = 0;
for($sq = 0; $sq < 10;$sq++){
$dir = "servers/$server[$sq]";
$files2 = scandir($dir, 1);    
for ($c = 0; $c < count($championnum); $c++) {
    for ($i = 0; $i < count($files2); $i++) {
        $claw3 = "servers/$server[$sq]/" . file_ext_strip($files2[$i]) . ".json";
        $z0r3 = file_get_contents($claw3);
        $gaza3 = json_decode($z0r3, true);
        for ($b = 0; $b < 2; $b++) {
            for ($r = 0; $r < 3; $r++) {
                $bans = $gaza3['teams'][$b]['bans'][$r]['championId'];
                if ($bans ==  $championnum[$c]){
                    $banrate++;
                }
            }
        }
    }
    $banrateq =  ($banrate/$serverc[$sq]) * 100;
    $nume = $championnum[$c];
    if (!$result = $db->query("UPDATE `$server[$sq]` SET `ban`= $banrateq Where id = $nume")) {
        die('There was an error running the query [' . $db->error . ']');
    }
    $banrate = 0;
}
}