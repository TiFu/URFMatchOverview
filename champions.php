<?php
error_reporting(E_ALL);
// Config and constants
include("config/config.php");
include("functions.php");
include("classes/match.php");
include("templates.php");
// Use mysqli (object orientation ftw)
$mysqli = mysqli_init();
$mysqli->real_connect(SERVER, DB_USER, DB_PW, DATABASE);
// Load match
if (isset($_GET['matchId'])) {
    $matchId = $_GET['matchId'];
} else {
    $matchId = getRandomMatchId();
}

if (!is_int($matchId)) {
    // do error handling here
}
$match = new Match(file_get_contents("data/" . $matchId . ".json"), $mysqli);
$startEvents = $match->getEvents(array("CHAMPION_KILL", "BUILDING_KILL", "ELITE_MONSTER_KILL"));
$logEvents = array();

// Filter red and blue buff out of the events
foreach ($startEvents as $event) {
    if ($event['eventType'] == "ELITE_MONSTER_KILL") {
        if ($event['monsterType'] == "BARON_NASHOR" || $event['monsterType'] == "DRAGON") {
            $logEvents[] = $event;
        }
    } else {
        $logEvents[] = $event;
    }
}
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>URF Match Timeline</title> 
        <meta charset="UTF-8" />
        <script src="script/d3.v3.min.js"></script>
        <script type="text/javascript" src="script/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" src="script/jquery.tablesorter.min.js"></script> 
        <!-- timeline -->
        <link id="data-uikit-theme" rel="stylesheet" href="css/uikit.docs.min.css">
        <script src="script/uikit.min.js"></script>
        <script src="script/tooltip.js"></script>
        <script src="script/jquery.pause.min.js"></script>
        <script src="script/jquery.timer.js"></script>
        <script src="script/timeline.js"></script>
        <script src="script/functions.js"></script>
        <script src="script/update.js"></script>
        <script src="script/timelineCallbacks.js"></script>
        <link rel="stylesheet" href="css/timeline.css">
        <script>
            window.drawn = false;

            $winner = '<?php echo $match->getWinner()['teamId']; ?>';
            $duration = '<?php echo $match->getDuration(); ?>';
            $textboxInterval = null;

            $team = new Array();
            $team['100'] = "blue";
            $team['200'] = "red";

            $participants = new Array();
<?php
foreach ($match->getParticipants() as $participant) {
    echo '$participants[' . $participant['participantId'] . '] = ' . json_encode($participant) . ';';
}
?>

            $champs = new Array();
<?php
$champs = $mysqli->query("SELECT * FROM " . CHAMP_TABLE);
while ($champ = $champs->fetch_assoc()) {
    echo '$champs[' . $champ['id'] . '] = "' . $champ['name'] . '";';
}
?>

            var $evts =
<?php
$jsonEvents = json_encode($logEvents);
echo $jsonEvents;
?>;
            $participants = new Array();
<?php
foreach ($match->getParticipants() as $participant) {
    echo '$participants[' . $participant['participantId'] . '] = ' . json_encode($participant) . ';';
}
?>
            $items = new Array();
            $items[0] = "Empty";
<?php
$items = $mysqli->query("SELECT id, name FROM " . ITEM_TABLE);
while ($item = $items->fetch_assoc()) {
    echo '$items[' . $item['id'] . '] = ' . json_encode($item["name"]) . ";";
}
?>
            $champs = new Array();
<?php
$champions[] = array();
$champs = $mysqli->query("SELECT * FROM " . CHAMP_TABLE);
while ($champ = $champs->fetch_assoc()) {
    $champions[$champ["id"]] = $champ["name"];
    // JS champs
    // TODO: only save the champs needed! (propably change the select)
    echo '$champs[' . $champ['id'] . '] = "' . $champ['name'] . '";';
}
?>

            /**
             *  Initialize the timeline with events, showEvent and hoverText (currently BS)
             */
            $(document).ready(function () {
                // Update textBox periodically (every 250ms)
                $textboxInterval = setInterval(updateTextBox, 250);
                $commentBox = $("#comments");
                $('#timeline').timeliner({events: [<?php
$string = "";
foreach ($logEvents as $event) {
    $string .= ((int) ($event['timestamp'] / 1000)) . ',';
}
echo rtrim($string, ',');
?>], showEvent: [<?php
$string = "";
foreach ($logEvents as $event) {
    $string .= ($event['eventType'] != 'CHAMPION_KILL' && $event['eventType'] != 'STAT_UPDATE') . ',';
}
echo rtrim($string, ',');
?>], hoverText: [<?php
$string = "";
foreach ($logEvents as $event) {
    $string .= "\"" . $match->createHoverText($event) . "\",";
}
echo rtrim($string, ',');
?>], timeLength: <?php echo $match->getDuration() ?>});
                // Update textBox periodically (every 250ms)
                $textboxInterval = setInterval(updateTextBox, 250);
                $commentBox = $("#comments");
                for (i = 1; i <= 10; i++) {
                    $participants[i]["field"] = $("#participant" + i);
                    $participants[i]["field"]["items"] = $participants[i]["field"].find(".champbuild").find("img");
                    $participants[i]["field"]["currentGold"] = $participants[i]["field"].find("#currentGold");
                    $participants[i]["field"]["currentMinions"] = $participants[i]["field"].find("#currentMinions");
                    $participants[i]["field"]["level"] = $participants[i]["field"].find(".level");
                }
                $towerCountField = new Array();
                $towerCountField[100] = $('#towerCount100');// blue team;
                $towerCountField[200] = $('#towerCount200');

                $dragonCountField = new Array();
                $dragonCountField[100] = $('#dragonCount100');
                $dragonCountField[200] = $('#dragonCount200');

                $baronCountField = new Array();
                $baronCountField[100] = $('#baronCount100');
                $baronCountField[200] = $('#baronCount200');

                $goldCountField = new Array();
                $goldCountField[100] = $('#blueGold');
                $goldCountField[200] = $('#redGold');

                $blueTeamKillsField = $('#blueTeamKills');
                $redTeamKillsField = $('#redTeamKills');

                $('#blueVictory').html($winner == 100 ? "Victory" : "Defeat");
                $('#redVictory').html($winner == 200 ? "Victory" : "Defeat");
            });
        </script>
        <!-- timeline -->
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    </head>
    <body>
        <div class="main_info">
                        <div class="backurf"> 
                <table id="keywords" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th colspan="2"><span data-uk-tooltip title="Champion Rank">Rank</span></th>
                            <th><span data-uk-tooltip title="Champion Pick Rate">Pick</span></th>
                            <th><span data-uk-tooltip title="Kill/Death/Assist Rate">KDA</span></th>
                            <th><span data-uk-tooltip title="Champion Ban Rate">Ban</span></th>
                            <th><span data-uk-tooltip title="Champion Kills Rate">Kills</span></th>
                            <th><span data-uk-tooltip title="Champion Death Rate">Death</span></th>
                            <th><span data-uk-tooltip title="Champion Assist Rate">Assist</span></th>
                            <th><span data-uk-tooltip title="First Blood Rate">F B</span></th>
                            <th><span data-uk-tooltip title="Double Kill Rate">D K</span></th>
                            <th><span data-uk-tooltip title="Triple Kill Rate">T K</span></th>
                            <th><span data-uk-tooltip title="Quadra Kill Rate">Q K</span></th>
                            <th><span data-uk-tooltip title="Penta Kill Rate">P K</span></th>
                            <th><span data-uk-tooltip title="Killing Spree Rate">K S</span></th>
                            <th><span data-uk-tooltip title="Creeps Slain Rate (Minions & Jungle Monsters)">C S</span></th>
                            <th><span data-uk-tooltip title="Tower Destroy Rate">Tower Destroy</span></th>
                            <th><span data-uk-tooltip title="Wards Place Rate">Wards Place</span></th>
                            <th><span data-uk-tooltip title="Wards Kills Rate">Wards Killed</span></th>
                            <th><span data-uk-tooltip title="True Damage Rate (For Champions)">True Dmg</span></th>
                            <th><span data-uk-tooltip title="Physical Damage Rate (For Champions)">Physical Dmg</span></th>
                            <th><span data-uk-tooltip title="Magic Damage Rate (For Champions)">Magic Dmg</span></th>
                            <th><span data-uk-tooltip title="Total Damage Rate">Total Dmg</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $db = new mysqli('localhost', 'root', '', 'challenge');
                        $claw3 = file_get_contents("data/" . $matchId . ".json");
                        $gaza3 = json_decode($claw3, true);
                        $serv = strtolower($gaza3['region']);
                        $num = 1;
$championnum = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,48,50,51,53,54,55,56,57,58,59,60,61,62,63,64,
    67,68,69,72,74,75,76,77,78,79,80,81,82,83,84,85,86,89,90,91,92,96,98,99,101,102,103,104,105,106,107,110,111,112,113,114,115,117,119,120,121,122,126,127,131,133,134,143,150,222,154,157,161,201,201,236,238,254,266,267,268,412,421,429,432
    );                        
                        for ($c = 1; $c < count($championnum);$c++) {
                            $lastchamp = $championnum[$c];
                            if (!$result = $db->query("SELECT * FROM $serv WHERE id = $lastchamp ORDER BY 'numgames' DESC")) {
                                die('There was an error running the query [' . $db->error . ']');
                            } else {
                                $result2 = $db->query("SELECT * FROM champs WHERE id = $lastchamp");
                                $row = $result->fetch_assoc();
                                $rowx = $result2->fetch_assoc();
                                $result3 = $db->query("
                                   SELECT `id` , sum( `numgames` ) AS `numgames` , sum( `pick` ) AS `pick` , sum( `kda` ) AS `kda` , sum( `ban` ) AS `ban` , sum( `kills` ) AS `kills` , sum( `death` ) AS `death` , sum( `assist` ) AS `assist` , sum( `fb` ) AS `fb` , sum( `dk` ) AS `dk` , sum( `tk` ) AS `tk` , sum( `qk` ) AS `qk` , sum( `pk` ) AS `pk` , sum( `ks` ) AS `ks` , `cs` , sum( `cs` ) AS `cs` , sum( `towerdestroy` ) AS `towerdestroy` , sum( `wardplace` ) AS `wardplace` , sum( `wardkill` ) AS `wardkill` , sum( `truedmg` ) AS `truedmg` , sum( `phycdmg` ) AS `phycdmg` , sum( `magicdmg` ) AS `magicdmg` , sum( `totaldmg` ) AS `totaldmg`
FROM (
SELECT *
FROM eune where id = $lastchamp
UNION ALL
SELECT *
FROM euw where id = $lastchamp
UNION ALL
SELECT *
FROM oce where id = $lastchamp
UNION ALL
SELECT *
FROM lan where id = $lastchamp
UNION ALL
SELECT *
FROM las where id = $lastchamp
UNION ALL
SELECT *
FROM tr where id = $lastchamp
UNION ALL
SELECT *
FROM kr where id = $lastchamp
UNION ALL
SELECT *
FROM ru where id = $lastchamp
UNION ALL
SELECT *
FROM na where id = $lastchamp
UNION ALL
SELECT *
FROM br where id = $lastchamp
)x
GROUP BY `id`
");
                                $rowx3 = $result3->fetch_assoc();
                                echo "<tr class=\"yellow\">\n";
                                echo "<td><span>$num</span></th>\n";
                                echo "<td><span><img data-uk-tooltip title=\"{$rowx['name']}\" style=\"border-radius: 50%;\" width=\"24\" height=\"24\" src=\"images/champion/{$rowx['name']}46.png\" alt=\"\" /></span></th>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['pick'],2)."%</percentege> Pickrate In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['pick'],0) / 10)."%</rate> Pickrate In All Servers\">".round($row['pick'],2)."%</span></th>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['kda'],2)."</percentege> KDA Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['kda'],1) / 10)."%</rate> KDA Per Game In All Servers\">".round($row['kda'],2)."</span></th>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['ban'],2)."%</percentege> Ban Rate In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['ban'],0) / 10)."%</rate> Ban Rate In All Servers\">".round($row['ban'],2)."%</span></th>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['kills'],2)."</percentege> Kill Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['kills'],0) / 10)."</rate> Kill Per Game In All Servers\">".round($row['kills'],2)."</span></th>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['death'],2)."</percentege> Assist Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['death'],0) / 10)."</rate> Assist Per Game In All Servers\">".round($row['death'],2)."</span></th>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['assist'],2)."</percentege> Death Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['assist'],0) / 10)."</rate> Death Per Game In All Servers\">".round($row['assist'],2)."</span></th>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['fb'],2))."%</percentege> First Blood Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['fb'],2))."%</rate> First Blood Rate Per Game In All Servers\">".(round($row['fb'],2))."</span></th>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['dk'],2))."%</percentege> Double Kill Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['dk'],2))."%</rate> Double Kill Rate Per Game In All Servers\">".(round($row['dk'],2))."</span></th>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['tk'],2))."%</percentege> Triple Kill Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['tk'],2))."%</rate> Triple Kill Rate Per Game In All Servers\">".(round($row['tk'],2))."</span></th>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['qk'],2))."%</percentege> Quadra Kill Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['qk'],2))."%</rate> Quadra Kill Rate Per Game In All Servers\">".(round($row['qk'],2))."</span></th>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['pk'],2))."%</percentege> Penta Kill Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['pk'],2))."%</rate> Penta Kill Rate Per Game In All Servers\">".(round($row['pk'],2))."</span></th>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['ks'],2))."%</percentege> Killing Spree Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['ks'],2))."%</rate> Killing Spree Rate Per Game In All Servers\">".(round($row['ks'],2))."</span></th>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['cs'],2))."</percentege> Creeps Slain Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['cs'],2))."</rate> Creeps Slain Rate Per Game In All Servers\">".(round($row['cs'],2))."</span></th>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['towerdestroy'],2))."%</percentege> Tower Destroy Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['towerdestroy'],2))."%</rate> Tower Destroy Rate Per Game In All Servers\">".(round($row['towerdestroy'],2))."</span></th>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['wardplace'],2))."%</percentege> Ward Place Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['wardplace'],2))."%</rate> Ward Place Rate Per Game In All Servers\">".(round($row['wardplace'],2))."</span></th>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['wardkill'],2))."%</percentege> Ward Destroy Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['wardkill'],2))."%</rate> Ward Destroy Rate Per Game In All Servers\">".(round($row['wardkill'],2))."</span></th>\n";                               
                                 echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['truedmg'],0)."</percentege> True Damage Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['truedmg'],0) / 10)."</rate> True Damage Per Game In All Servers\">".round($row['truedmg'],0)."</span></th>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['phycdmg'],0)."</percentege> Physical Damage Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['phycdmg'],0) / 10)."</rate> Physical Damage Per Game In All Servers\">".round($row['phycdmg'],0)."</span></th>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['magicdmg'],0)."</percentege> Magaic Damage Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['magicdmg'],0) / 10)."</rate> Magaic Damage Per Game In All Servers\">".round($row['magicdmg'],0)."</span></th>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['totaldmg'],0)."</percentege> Total Damage Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['totaldmg'],0) / 10)."</rate> Total Damage Per Game In All Servers\">".round($row['totaldmg'],0)."</span></th>\n";
                                echo "                        </tr> ";
                            }
                            $num++;
                        }
                        ?>
                    </tbody>
                </table>             
                <script type="text/javascript">
                    $(function () {
                        $('#keywords').tablesorter();
                    });
                </script>            
            </div>
        </div>  
    </body>
</html>
