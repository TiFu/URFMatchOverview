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
for($sq = 0; $sq < 10;$sq++){
$dir = "servers/$server[$sq]";
$files2 = scandir($dir, 1);
$m_kill = 0;
$m_assist = 0;
$m_death = 0;
$m_fb = 0;
$m_dk = 0;
$m_tk = 0;
$m_qk = 0;
$m_pk = 0;
$m_ks = 0;
$m_cs = 0;
$m_towerdestroy = 0;
$m_wardplace = 0;
$m_wardkill = 0;
$m_truedmg = 0;
$m_phycdmg = 0;
$m_magicdmg = 0;
$m_totaldmg = 0;

for ($c = 0; $c < count($championnum); $c++) {
    for ($i = 0; $i < count($files2); $i++) {
        $claw3 = "servers/$server[$sq]/" . file_ext_strip($files2[$i]) . ".json";
        $z0r3 = file_get_contents($claw3);
        $gaza3 = json_decode($z0r3, true);
        for ($j = 0; $j < 10; $j++) {
            $lastchamp = $gaza3['participants'][$j]['championId'];
            if ($lastchamp == $championnum[$c]) {
                $q_kill = $gaza3['participants'][$j]['stats']['kills'];
                $m_kill = $m_kill + $q_kill;
                $q_assist = $gaza3['participants'][$j]['stats']['assists'];
                $m_assist = $m_assist + $q_assist;                
                $q_death = $gaza3['participants'][$j]['stats']['deaths'];
                $m_death = $m_death + $q_death;                
                $q_fb = $gaza3['participants'][$j]['stats']['firstBloodKill'];
                if($q_fb == "true"){
                    $m_fb++;
                }
                $q_dk = $gaza3['participants'][$j]['stats']['doubleKills'];
                $m_dk = $m_dk + $q_dk;
                $q_tk = $gaza3['participants'][$j]['stats']['tripleKills'];
                $m_tk = $m_tk + $q_tk;
                $q_qk = $gaza3['participants'][$j]['stats']['quadraKills'];
                $m_qk = $m_qk + $q_qk;
                $q_pk = $gaza3['participants'][$j]['stats']['pentaKills'];
                $m_pk = $m_pk + $q_pk;
                $q_ks = $gaza3['participants'][$j]['stats']['killingSprees'];
                $m_ks = $m_ks + $q_ks;      
                $q_cs = $gaza3['participants'][$j]['stats']['minionsKilled'] + $gaza3['participants'][$j]['stats']['neutralMinionsKilled'];
                $m_cs = $m_cs + $q_cs;                
                $q_towerdestroy = $gaza3['participants'][$j]['stats']['towerKills'];
                $m_towerdestroy = $m_towerdestroy + $q_towerdestroy;                 
                $q_wardplace = $gaza3['participants'][$j]['stats']['wardsPlaced'];
                $m_wardplace = $m_wardplace + $q_wardplace;                  
                $q_wardkill = $gaza3['participants'][$j]['stats']['wardsKilled'];
                $m_wardkill = $m_wardkill + $q_wardkill;                  
                $q_truedmg = $gaza3['participants'][$j]['stats']['trueDamageDealtToChampions'];
                $m_truedmg = $m_truedmg + $q_truedmg;                                  
                $q_phycdmg = $gaza3['participants'][$j]['stats']['physicalDamageDealtToChampions'];
                $m_phycdmg = $m_phycdmg + $q_phycdmg;                                   
                $q_magicdmg = $gaza3['participants'][$j]['stats']['magicDamageDealtToChampions'];
                $m_magicdmg = $m_magicdmg + $q_magicdmg;                                                   
                $q_totaldmg = $gaza3['participants'][$j]['stats']['totalDamageDealtToChampions'];
                $m_totaldmg = $m_totaldmg + $q_totaldmg;                                                   
            }
        }
    }
$k_kill = $m_kill/$serverc[$sq];
$k_assist = $m_assist/$serverc[$sq];
$k_death = $m_death/$serverc[$sq];
$k_fb = $m_fb/$serverc[$sq];
$k_dk = $m_dk/$serverc[$sq];
$k_tk = $m_tk/$serverc[$sq];
$k_qk = $m_qk/$serverc[$sq];
$k_pk = $m_pk/$serverc[$sq];
$k_ks = $m_ks/$serverc[$sq];
$k_cs = $m_cs/$serverc[$sq];
$k_towerdestroy = $m_towerdestroy/$serverc[$sq];
$k_wardplace = $m_wardplace/$serverc[$sq];
$k_wardkill = $m_wardkill/$serverc[$sq];
$k_truedmg = $m_truedmg/$serverc[$sq];
$k_phycdmg = $m_phycdmg/$serverc[$sq];
$k_magicdmg = $m_magicdmg/$serverc[$sq];
$k_totaldmg = $m_totaldmg/$serverc[$sq];
    $nume = $championnum[$c];
    if (!$result = $db->query("UPDATE `$server[$sq]` SET `kills`=$k_kill,`death`=$k_death,`assist`=$k_assist,`fb`=$k_fb,`dk`=$k_dk,`tk`=$k_tk,`qk`=$k_qk,`pk`=$k_pk,`ks`=$k_ks,`cs`=$k_cs,`towerdestroy`=$k_towerdestroy,`wardplace`=$k_wardplace,`wardkill`=$k_wardkill,`truedmg`=$k_truedmg,`phycdmg`=$k_phycdmg,`magicdmg`=$k_magicdmg,`totaldmg`=$k_totaldmg WHERE id =  $nume")) {
        die('There was an error running the query [' . $db->error . ']');
    }
$m_kill = 0;
$m_assist = 0;
$m_death = 0;
$m_fb = 0;
$m_dk = 0;
$m_tk = 0;
$m_qk = 0;
$m_pk = 0;
$m_ks = 0;
$m_cs = 0;
$m_towerdestroy = 0;
$m_wardplace = 0;
$m_wardkill = 0;
$m_truedmg = 0;
$m_phycdmg = 0;
$m_magicdmg = 0;
$m_totaldmg = 0;
}
}