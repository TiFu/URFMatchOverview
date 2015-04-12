<?php

$db = new mysqli("localhost", "root", "", "challenge");

if ($db->connect_errno > 0) {
    die('Unable to connect to database [' . $db->connect_error . ']');
}

function file_ext_strip($filename) {
    return preg_replace('/.[^.]*$/', '', $filename);
}

$server = array("eune", "euw", "tr", "na", "kr", "lan", "las", "ru", "oce","br");
for($sq = 0; $sq < 10;$sq++){
$dir = "servers/$server[$sq]";
$files2 = scandir($dir, 1);
    $blueteam = 0;
    $redteam = 0;
    $gamesnum = 0;
    for ($i = 0; $i < count($files2); $i++) {
        $claw3 = "servers/$server[$sq]/" . file_ext_strip($files2[$i]) . ".json";
        $z0r3 = file_get_contents($claw3);
        $gaza3 = json_decode($z0r3, true);
        for ($b = 0; $b <= 1; $b++) {
            $winr = $gaza3['teams'][$b]['winner'];
            if ($winr == true) {
                $blueteam++;
                $gamesnum++;
            } else {
                $redteam++;
                $gamesnum++;
            }
        }
    }
    if (!$resultx = $db->query("SELECT * FROM `$server[$sq]` ORDER BY `numgames` Desc")) {
        die('There was an error running the query [' . $db->error . ']');
    }
    $row = $resultx->fetch_assoc();
    $maxone = $row['id'];
    if (!$result = $db->query("UPDATE `severrate` SET `gamenum`=$gamesnum,`winrateblue`=$blueteam,`winratered`=$redteam,`topchamp`=$maxone WHERE name = '$server[$sq]'")) {
        die('There was an error running the query1 [' . $db->error . ']');
    }
    $gamesnum = 0;
}
