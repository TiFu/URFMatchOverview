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
$match = new Match(file_get_contents(MATCH_PATH .$matchId .".json"), $mysqli);
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
		<script src="script/templates.js"></script>
		<script src="script/prototypes.js"></script>
		<script src="script/update.js"></script>
        <script src="script/timelineCallbacks.js"></script>
		<script src="script/pablo.min.js"></script>
		<script src="script/jquery.tipsy.js"></script>
		<link rel="stylesheet" href="css/tipsy.css">
        <link rel="stylesheet" href="css/timeline.css">
        <link rel="stylesheet" href="css/update.css">
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
				$commentBox = $("#comments");
				for (var i = 1; i <=  10; i++) {
					var $field = $("#participant" + i);
					$participants[i]["field"] = new Array();
					$participants[i]["field"]["items"] = $field.find(".champbuild").find("img");
					$participants[i]["field"]["currentGold"] = $field.find(".currentGold");
					$participants[i]["field"]["currentMinions"] = $field.find(".currentMinions");
					$participants[i]["field"]["level"] = $field.find(".level");
					$participants[i]["field"]["kda"] = $field.find(".kda");
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
				// Update textBox periodically (every 250ms)
				$textboxInterval = setInterval(updateTextBox, 250);
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
                    <p class="centerlyw"><span class="chat_time">[0:00]</span><span class="chat_info"><span class="moreinfp">Welcome to Summoner's Rift !</span></span></p> 
                </div>
            </div>
            <div class="clear"></div>
            <div id="timeline" class="timeline">
            </div>
            <div class="clear"></div>
            <div class="statsteam">
                <div class="summarymatch">
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
                <a href="champions.php"><div data-uk-tooltip title="Click Here To See All Champions Statistics" class="seeallchamp"></div></a>
                <a href="result.php"><div data-uk-tooltip title="Click Here To Random Another Match" class="anothermatch"></div></a>           
                <div class="urfdata" data-uk-tooltip title="This Statistics for this game">Ultra Rapid Fire Champions Statistics</div>
            </div>
                        <?php
                        $server = strtolower($match->getRegion());
						$servers = array("br", "eune", "euw", "kr", "lan", "las", "na", "oce", "ru", "tr");
						$serverValues = array();
						$allServersValues = array();
						$thisGame = array();
						foreach ($participants as $participant) {
								$participantId = $participant["participantId"];
								if (!isset($serverValues["champion"])) {
									$serverValues["champion"] = array();
									$allServersValues["champion"] = array();
								}
								$champId = $participant["championId"];
								$champName = $mysqli->query("SELECT name FROM " .CHAMP_TABLE . " WHERE id = " .$champId)->fetch_assoc()['name'];
								$serverValues["champion"][$participantId] = $champName;
								$allServersValues["champion"][$participantId] = $champName;
								$thisGame["champion"][$participantId] = $champName;

								$query = $mysqli->query("SELECT * FROM " .AVERAGE_TABLE . " WHERE championId = " .$champId ." AND region='" .$server ."'")->fetch_assoc();

								foreach ($query as $key => $value) {
									if ($key == "championId" || $key == "numberOfGames" || $key == "region") {
										continue;
									}
									if (!isset($serverValues[$key])) {
										$serverValues[$key] = array();
										$allServersValues[$key] = array();
										$thisGame[$key] = array();
									}
									if (!isset($serverValues[$key][$participantId])) {
										$allServersValues[$key][$participantId] = 0;
									}
									if ($key != "pickRate" && $key != "winRate" && $key != "banRate") {
										$thisGame[$key][$participantId] = $participants[$participantId-1][$key];
									} 
									$serverValues[$key][$participantId] = $value;
								}

								$totalNumberOfGames = 0;
								foreach ($servers as $currentServer) {
									$query = $mysqli->query("SELECT * FROM " .AVERAGE_TABLE . " WHERE championId = " .$champId ." AND region='" .$currentServer ."'")->fetch_assoc();
									$numGames = $query["numberOfGames"];
									$totalNumberOfGames += $numGames;
									foreach ($query as $key => $value) {
										if ($key == "name" || $key == "championId" || $key == "numberOfGames" || $key == "region") {
											continue;
										}
										$allServersValues[$key][$participantId] += $value * $numGames;
									}
								}	

								$totalNumberOfGames = max(1, $totalNumberOfGames);
								foreach ($allServersValues as $key => $ch) {
										$allServersValues[$key][$participantId] = $allServersValues[$key][$participantId] / $totalNumberOfGames;
								}
						}
	?>
            <div class="backurf"> 
                <table id="keywords">
                    <thead>
						<?php
						$body = false;
						foreach ($serverValues as $rowKey => $values) {
							if ($rowKey == "numgames") {
								continue;
							}
							if ($rowKey == "kills") {
								echo "<tr class=\"yellow\">";
										echo "<td style=\"font-weight: bold;border-right:1px solid #b8b8b8\">KDA</td>";
									foreach ($values as $participantId => $value) {
										echo "<td colspan=\"2\" style=\"color:#107360;\">";
										echo tableCell("kda", ($thisGame["kills"][$participantId] + $thisGame["assists"][$participantId])/max($thisGame["deaths"][$participantId], 1));
										echo "</td>";
										echo "<td colspan=\"2\" style=\"color:#C0392B;\">";
										echo tableCell("kda", ($serverValues["kills"][$participantId] + $serverValues["assists"][$participantId])/max(1, $serverValues["deaths"][$participantId]));
										echo "</td>";
										echo "<td colspan=\"2\"  style=\"color:#2980B9;border-right:1px solid #b8b8b8\">";
										echo tableCell("kda",($allServersValues["kills"][$participantId] + $allServersValues["assists"][$participantId])/max(1, $allServersValues["deaths"][$participantId]));
										echo "</td>";								
									}
								echo "</tr>";
							}

							echo $body ? "<tr class=\"yellow\">" : "<tr  style=\"border-bottom:1px solid #b8b8b8\">";
							echo $rowKey == "champion" ? "<th>" : "<td  style=\"font-weight: bold; border-right:1px solid #b8b8b8\">";
							echo $rowKey != "firstBloodKill" ? transformColumnNameToText($rowKey) : "First Blood";
							echo $rowKey == "champion" ? "</th>" : "</td>";
							foreach ($values as $participantId => $value) {
								if ($rowKey == "champion") {
									echo "<th colspan=\"6\" style=\"background-color:" .($participants[$participantId-1]["teamId"] == 100 ? "#C6E8F2" : "#F2C6C7") ."\">";
									echo tableCell($rowKey, $value);
									echo "</th>";
								} else {
									if ($rowKey == "pickRate" || $rowKey == "winRate" || $rowKey == "banRate") {
										echo "<td colspan=\"3\" style=\"color:#C0392B;\">";
										echo tableCell($rowKey, $value);
										echo "</td>";
										echo "<td colspan=\"3\" style=\"color:#2980B9;border-right:1px solid #b8b8b8\">";
										echo tableCell($rowKey, $allServersValues[$rowKey][$participantId]);
										echo "</td>";
									} else {
										echo "<td colspan=\"2\" style=\"color:#107360;\">";
										echo tableCell($rowKey, $thisGame[$rowKey][$participantId]);
										echo "</td>";
										echo "<td colspan=\"2\" style=\"color:#C0392B;\">";
										echo tableCell($rowKey, $value);
										echo "</td>";
										echo "<td colspan=\"2\"  style=\"color:#2980B9;border-right:1px solid #b8b8b8\">";
										echo tableCell($rowKey, $allServersValues[$rowKey][$participantId]);
										echo "</td>";
									}
								}
							}
							echo "</tr>";
							if ($rowKey == "champion") {
								echo "<tr>";
								echo "<td class=\"yellow\" style=\"font-weight: bold;border-right:1px solid #b8b8b8\"></td>";
								for ($k = 0; $k < 10; $k++) {
									echo "<td colspan=\"3\" style=\"font-weight: bold;font-size:10px;\">" .regionToServer($match->getRegion()) ."</td><td colspan=\"3\"   style=\"font-weight: bold;border-right:1px solid #b8b8b8\">All Servers</td>";
								}
								echo "</tr>";

							}
							if ($rowKey == "banRate") {
								echo "<tr>";
								echo "<td class=\"yellow\" style=\"font-weight: bold;border-right:1px solid #b8b8b8\"></td>";
								for ($k = 0; $k < 10; $k++) {
									echo "<td colspan=\"2\" style=\"font-weight: bold;font-size:10px;\">This Game</td><td colspan=\"2\" style=\"font-weight: bold;font-size:10px;\">" .regionToServer($match->getRegion()) ."</td><td colspan=\"2\" style=\"font-weight: bold;border-right:1px solid #b8b8b8\">All Servers</td>";
								}
								echo "</tr>";
							}
						echo $rowKey == "champion" ? "</thead><tbody>" : "";
							$body = $rowKey == "champion" ? true : $body;
						}
                        ?>
                    </tbody>
                </table>             
            </div>
        </div>  
    </body>
</html>
