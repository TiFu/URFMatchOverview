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
file_put_contents("log.log", $matchId ."\n", FILE_APPEND);

if (!is_int($matchId)) {
    // do error handling here
}
$match = new Match(file_get_contents("data/" . $matchId . ".json"), $mysqli);
$startEvents = $match->getEvents(array("CHAMPION_KILL", "BUILDING_KILL", "ELITE_MONSTER_KILL"));
$logEvents = array();
$animationDuration = $match->getDuration() / 600.0 * 45; // 45 secs per 10 min game time

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
		<script src="script/pablo.min.js"></script>
		<script src="script/jquery.tipsy.js"></script>
		<link rel="stylesheet" href="css/tipsy.css">
        <link rel="stylesheet" href="css/timeline.css">
        <script>
            window.drawn = false;

            $winner = '<?php echo $match->getWinner()['teamId']; ?>';
            $duration = '<?php echo $match->getDuration(); ?>';
            $textboxInterval = null;

			// team colors
            $team = new Array();
            $team['100'] = "blue";
            $team['200'] = "red";

			$teams = new Array();
			$teams["100"] = 
<?php
			echo json_encode($match->getTeam(100));
?>;
			$teams["200"] = 
<?php
	echo json_encode($match->getTeam(200));
?>;
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
						$string .= ((int) ($event['timestamp']/1000)) .',';
					}
					echo rtrim($string, ',');
				?>], showEvent: [<?php 
					$string = "";
					foreach ($logEvents as $event) {
						$string .= ($event['eventType'] != 'CHAMPION_KILL' && $event['eventType'] != 'STAT_UPDATE') .',';
					}
					echo rtrim($string, ',');
				?>], hoverText: [<?php 
					$string = "";
					foreach ($logEvents as $event) {
						$string .= "\"" .$match->createHoverText($event) ."\",";
					}
					echo rtrim($string, ',');
			?>], timeLength: <?php echo $match->getDuration() ?>, animationLength: <?php echo $animationDuration ?>});
				// Update textBox periodically (every 250ms)
				$textboxInterval = setInterval(updateTextBox, 250);
				$commentBox = $("#comments");
				for (i = 1; i <=  10; i++) {
					$participants[i]["field"] = $("#participant" + i);
					$participants[i]["field"]["items"] = $participants[i]["field"].find(".champbuild").find("img");
					$participants[i]["field"]["currentGold"] = $participants[i]["field"].find(".currentGold");
					$participants[i]["field"]["currentMinions"] = $participants[i]["field"].find(".currentMinions");
					$participants[i]["field"]["level"] = $participants[i]["field"].find(".level");
					for (z = 0; z < 7; z++) {
						$($participants[i]["field"]["items"][z]).tipsy({gravity:'s', title: function () {
							return $(this).attr("original-title");
						}, opacity:1});
					}
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
				// Draw map
				$svg = Pablo('#mapPicture');
				// init map
				$svg.append('<image xlink:href="images/map.jpg" x="0" y="0" width="530" height="512"></image>');

			});
        </script>
        <!-- timeline -->
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    </head>
    <body>
        <div class="main_info">
            <div class="part_map">  
                <div id="map">
					<svg id="mapPicture"></svg>
				</div>
            </div>
            <div class="part_chat">              
                <div class="highelight">Match Highlights</div>
                <div id="comments" class="highelight_comment">
                    <p><span class="chat_time">[0:00]</span><span class="chat_info">Welcome to Summoner's Rift !</span></p> 
                </div>
            </div>
            <div class="clear"></div>
            <div id="timeline" class="timeline">
            </div>
            <div class="clear"></div>
            <div class="statsteam">
                <div class="summary">
                    <div id="blueVictory" class="tblue">Victory</div>
                    <div id="blueGold" class="redgold">5.0K</div>
                    <div class="destroyred">Towers : <span id="towerCount100">0</span>  - Dragons : <span id="dragonCount100">0</span>  - Baron : <span id="baronCount100">0</span></div>                    
                    <div id="redVictory" class="tred">Defeat</div>
                    <div id="redGold" class="redgold">0.0K</div>
                    <div class="destroyred">Towers : <span id="towerCount200">0</span>  - Dragons : <span id="dragonCount200">0</span>  - Baron : <span id="baronCount200">0</span></div>                    
                    <div class="score"><span id="blueTeamKills">0</span>-<span id="redTeamKills">0</span></div>

                </div>
                <div class="blueteam">
					<div class="blueteamborder">
					<?php
						$summonerSpells = array();
						$summoners = $mysqli->query("SELECT * FROM " .SUMMONERS_TABLE);
						while ($summoner = $summoners->fetch_assoc()) {
							$summonerSpells[$summoner["id"]] = $summoner["name"];
						}
						
						$participants = $match->getParticipants();
						$search = array("{participantId}", "{champurl}", "{champname}", "{kills}", "{deaths}", "{assists}" ,"{item0}", "{item1}", "{item2}", "{item3}", "{item4}", "{item5}", "{trinket}", "{gold}", "{minions}", "{level}", "{sum1}", "{sum2}");
						foreach ($participants as $participant) {
							if ($participant["teamId"] == BLUE_SIDE_ID) {
								$replace = array();
								$replace[] = $participant["participantId"];
								$replace[] = str_replace(" ", "%20", $champions[$participant["championId"]]);
								$replace[] = $champions[$participant["championId"]];
								$replace[] = $participant["currentKills"];
								$replace[] = $participant["currentDeaths"];
								$replace[] = $participant["currentAssists"];
								$replace[] = 0;
								$replace[] = 0;
								$replace[] = 0;
								$replace[] = 0;
								$replace[] = 0;
								$replace[] = 0;
								$replace[] = 0;
								$replace[] = 475;
								$replace[] = 0;
								$replace[] = 1;
								$replace[] = $summonerSpells[$participant["spell1"]];
								$replace[] = $summonerSpells[$participant["spell2"]];
								echo str_replace($search, $replace, PARTICIPANT_TEMPLATE);
							}
						}						
					?>
					</div>					
                </div>               
                <div class="redteam">
					<div class="redteamborder">
					<?php
						foreach ($participants as $participant) {
							if ($participant["teamId"] == RED_SIDE_ID) {
								$replace = array();
								$replace[] = $participant["participantId"];
								$replace[] = str_replace(" ", "%20", $champions[$participant["championId"]]);
								$replace[] = $champions[$participant["championId"]];
								$replace[] = $participant["currentKills"];
								$replace[] = $participant["currentDeaths"];
								$replace[] = $participant["currentAssists"];
								$replace[] = 0;
								$replace[] = 0;
								$replace[] = 0;
								$replace[] = 0;
								$replace[] = 0;
								$replace[] = 0;
								$replace[] = 0;
								$replace[] = 475;
								$replace[] = 0;
								$replace[] = 1;
								$replace[] = $summonerSpells[$participant["spell1"]];
								$replace[] = $summonerSpells[$participant["spell2"]];

                                echo str_replace($search, $replace, PARTICIPANT_TEMPLATE);
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="clear"></div>       
            <div class="summary">
                <div class="urfdata">Ultra Rapid Fire Champions Statistics</div>

            </div>
            <div class="backurf"> 
                <table id="keywords">
                    <thead>
                        <tr>
                            <th><span data-uk-tooltip title="Champion">Champion</span></th>
                            <th><span data-uk-tooltip title="Champion Pick Rate">Pick</span></th>
                            <th><span data-uk-tooltip title="Champion Win Rate">Win</span></th>                                                                                    
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
                            <th><span data-uk-tooltip title="True Damage Rate (For Champions)">True Dmg</span></th>
                            <th><span data-uk-tooltip title="Physical Damage Rate (For Champions)">Physical Dmg</span></th>
                            <th><span data-uk-tooltip title="Magic Damage Rate (For Champions)">Magic Dmg</span></th>
                            <th><span data-uk-tooltip title="Total Damage Rate">Total Dmg</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
			$db = $mysqli;
                        $serv = strtolower($match->getRegion());
						
			$gaza3 = $match->getParticipants();
                        $num = 1;
                        for ($cmp = 0; $cmp < 10; $cmp++) {
                            $lastchamp = $gaza3[$cmp]['championId'];
                            if (!$result = $db->query("SELECT * FROM $serv WHERE id = $lastchamp ORDER BY 'numgames' DESC")) {
                                die('There was an error running the query [' . $db->error . ']');
                            } else {
                                $result2 = $db->query("SELECT * FROM champs WHERE id = $lastchamp");
                                $row = $result->fetch_assoc();
                                $rowx = $result2->fetch_assoc();
                                $result3 = $db->query("
                                   SELECT `id` , sum( `numgames` ) AS `numgames` , sum( `pick` ) AS `pick` , sum( `kda` ) AS `kda` , sum( `ban` ) AS `ban` , sum( `kills` ) AS `kills` , sum( `death` ) AS `death` , sum( `assist` ) AS `assist` , sum( `fb` ) AS `fb` , sum( `dk` ) AS `dk` , sum( `tk` ) AS `tk` , sum( `qk` ) AS `qk` , sum( `pk` ) AS `pk` , sum( `ks` ) AS `ks` , `cs` , sum( `cs` ) AS `cs` , sum( `towerdestroy` ) AS `towerdestroy` , sum( `wardplace` ) AS `wardplace` , sum( `winrate` ) AS `winrate` , sum( `truedmg` ) AS `truedmg` , sum( `phycdmg` ) AS `phycdmg` , sum( `magicdmg` ) AS `magicdmg` , sum( `totaldmg` ) AS `totaldmg`
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
								echo mysqli_error($db);
                                $rowx3 = $result3->fetch_assoc();
                                echo "<tr class=\"yellow\">\n";
                                echo "<td><span><img data-uk-tooltip title=\"{$rowx['name']}\" style=\"border-radius: 50%;\" width=\"24\" height=\"24\" src=\"images/champion/" .str_replace(" ", "%20",$rowx['name']) ."46.png\" alt=\"\" /></span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['pick'],2)."%</percentege> Pickrate In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['pick'],0) / 10)."%</rate> Pickrate In All Servers\">".round($row['pick'],2)."%</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . (round($row['winrate'], 2)) . "%</percentege> Win Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['winrate'], 0)/ 10) . "%</rate> Win Rate Per Game In All Servers\">" . (round($row['winrate'], 2)) . "%</span></td>\n";                                
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['kda'],2)."</percentege> KDA Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['kda'],1) / 10)."%</rate> KDA Per Game In All Servers\">".round($row['kda'],2)."</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['ban'],2)."%</percentege> Ban Rate In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['ban'],0) / 10)."%</rate> Ban Rate In All Servers\">".round($row['ban'],2)."%</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['kills'],2)."</percentege> Kill Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['kills'],0) / 10)."</rate> Kill Per Game In All Servers\">".round($row['kills'],2)."</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['death'],2)."</percentege> Assist Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['death'],0) / 10)."</rate> Assist Per Game In All Servers\">".round($row['death'],2)."</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".round($row['assist'],2)."</percentege> Death Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['assist'],0) / 10)."</rate> Death Per Game In All Servers\">".round($row['assist'],2)."</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['fb'],2))."%</percentege> First Blood Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['fb'],2))."%</rate> First Blood Rate Per Game In All Servers\">".(round($row['fb'],2))."</span></td>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['dk'],2))."%</percentege> Double Kill Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['dk'],2))."%</rate> Double Kill Rate Per Game In All Servers\">".(round($row['dk'],2))."</span></td>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['tk'],2))."%</percentege> Triple Kill Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['tk'],2))."%</rate> Triple Kill Rate Per Game In All Servers\">".(round($row['tk'],2))."</span></td>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['qk'],2))."%</percentege> Quadra Kill Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['qk'],2))."%</rate> Quadra Kill Rate Per Game In All Servers\">".(round($row['qk'],2))."</span></td>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['pk'],2))."%</percentege> Penta Kill Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['pk'],2))."%</rate> Penta Kill Rate Per Game In All Servers\">".(round($row['pk'],2))."</span></td>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['ks'],2))."%</percentege> Killing Spree Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['ks'],2))."%</rate> Killing Spree Rate Per Game In All Servers\">".(round($row['ks'],2))."</span></td>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['cs'],2))."</percentege> Creeps Slain Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['cs'],2))."</rate> Creeps Slain Rate Per Game In All Servers\">".(round($row['cs'],2))."</span></td>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['towerdestroy'],2))."%</percentege> Tower Destroy Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['towerdestroy'],2))."%</rate> Tower Destroy Rate Per Game In All Servers\">".(round($row['towerdestroy'],2))."</span></td>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".(round($row['wardplace'],2))."%</percentege> Ward Place Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>".(round($rowx3['wardplace'],2))."%</rate> Ward Place Rate Per Game In All Servers\">".(round($row['wardplace'],2))."</span></td>\n";                               
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".number_format(round($row['truedmg'],0))."</percentege> True Damage Per Game In $serv <br/>{$rowx['name']} Got <rate>".number_format((round($rowx3['truedmg'],0) / 10))."</rate> True Damage Per Game In All Servers\">".number_format(round($row['truedmg'],0))."</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".number_format(round($row['phycdmg'],0))."</percentege> Physical Damage Per Game In $serv <br/>{$rowx['name']} Got <rate>".number_format((round($rowx3['phycdmg'],0) / 10))."</rate> Physical Damage Per Game In All Servers\">".number_format(round($row['phycdmg'],0))."</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".number_format(round($row['magicdmg'],0))."</percentege> Magaic Damage Per Game In $serv <br/>{$rowx['name']} Got <rate>".number_format((round($rowx3['magicdmg'],0) / 10))."</rate> Magaic Damage Per Game In All Servers\">".number_format(round($row['magicdmg'],0))."</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".number_format(round($row['totaldmg'],0))."</percentege> Total Damage Per Game In $serv <br/>{$rowx['name']} Got <rate>".number_format((round($rowx3['totaldmg'],0) / 10))."</rate> Total Damage Per Game In All Servers\">".number_format(round($row['totaldmg'],0))."</span></td>\n";
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
