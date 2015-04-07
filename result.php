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
$match = new Match(file_get_contents("data/".$matchId.".json"), $mysqli);
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
        <link id="data-uikit-theme" rel="stylesheet" href="tip/uikit.docs.min.css">
        <script src="tip/uikit.min.js"></script>
        <script src="tip/tooltip.js"></script>
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
				foreach($match->getParticipants() as $participant) {
					echo '$participants[' .$participant['participantId'] .'] = ' .json_encode($participant) .';';
				}
			?>
			$items = new Array();
			$items[0] = "Empty";
			<?php
				$items = $mysqli->query("SELECT id, name FROM " .ITEM_TABLE);
				while ($item = $items->fetch_assoc()){
					echo '$items[' .$item['id'] .'] = '.json_encode($item["name"]) .";";
				}
			?>
			$champs = new Array();
			<?php
				$champions[] = array();
				$champs = $mysqli->query("SELECT * FROM " .CHAMP_TABLE);
				while ($champ = $champs->fetch_assoc()) {
					$champions[$champ["id"]] = $champ["name"];
					// JS champs
					// TODO: only save the champs needed! (propably change the select)
					echo '$champs[' .$champ['id'] .'] = "' .$champ['name'] .'";';
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
			?>], timeLength: <?php echo $match->getDuration() ?>});
				// Update textBox periodically (every 250ms)
				$textboxInterval = setInterval(updateTextBox, 250);
				$commentBox = $("#comments");
				for (i = 1; i <=  10; i++) {
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
            <div class="part_chat">              
                <div class="highelight">Highlight Match </div>
                <div id="comments" class="highelight_comment">
                    <p><span class="chat_time">[0:00]</span><span class="chat_info">Welcome to Summoner's Rift !</span></p> 
                </div>
            </div>
            <div class="part_map">  
                <div id="map"></div>
                <script>

                    function drawomap(cord) {
                        var cords = [cord
                        ],
                                domain = {
                                    min: {x: -1000, y: -570},
                                    max: {x: 14800, y: 14800}
                                },
                        width = 512,
                                height = 512,
                                bg = "images/map.jpg",
                                xScale, yScale, svg;

                        color = d3.scale.linear()
                                .domain([0, 3])
                                .range(["white", "steelblue"])
                                .interpolate(d3.interpolateLab);

                        xScale = d3.scale.linear()
                                .domain([domain.min.x, domain.max.x])
                                .range([0, width]);

                        yScale = d3.scale.linear()
                                .domain([domain.min.y, domain.max.y])
                                .range([height, 0]);

                        if (drawn != true) {
                            this.svg = d3.select("#map").append("svg:svg")
                                    .attr("width", width)
                                    .attr("height", height);

                            this.svg.append('image')
                                    .attr('xlink:href', bg)
                                    .attr('x', '0')
                                    .attr('y', '0')
                                    .attr('width', '530')
                                    .attr('height', height);
                            drawn = true;
                        }
                        this.svg.append('svg:g').selectAll("circle")
                                .data(cords)
                                .enter().append("svg:circle")
                                .attr('cx', function (d) {
                                    return xScale(d[0]);
                                })
                                .attr('cy', function (d) {
                                    return yScale(d[1]);
                                })
                                .attr('r', 5)
                                .attr('class', 'kills');
                        return true;
                    }
                    drawomap();
                </script>
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
						$search = array("{participantId}", "{champname}", "{kills}", "{deaths}", "{assists}" ,"{item0}", "{item1}", "{item2}", "{item3}", "{item4}", "{item5}", "{trinket}", "{gold}", "{minions}", "{level}", "{sum1}", "{sum2}");
						foreach ($participants as $participant) {
							if ($participant["teamId"] == BLUE_SIDE_ID) {
								$replace = array();
								$replace[] = $participant["participantId"];
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
                <div class="urfdata">Ultra Rapid Fire Champions Statistical</div>

            </div>
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
                        <tr class="yellow">
                            <td><span data-uk-tooltip title="Annie Got Rank 1 On All Champions" >50</span></th>
                            <td><span><img data-uk-tooltip title="Description Of Champion" style="border-radius: 50%;" width="24" height="24" src="images/champion/Annie46.png" alt="" /></span></th>
                            <td><span>0.48%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>100%</span></th>
                            <td><span>100%</span></th>
                            <td><span>100%</span></th>
                            <td><span>100%</span></th>
                            <td><span>55.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>20.20%</span></th>
                            <td><span>10.20%</span></th>
                        </tr>
                        <tr class="yellow">
                            <td><span>14</span></th>
                            <td><span><img style="border-radius: 50%;" width="24" height="24" src="images/champion/Alistar46.png" alt="" /></span></th>
                            <td><span>0.48%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>100%</span></th>
                            <td><span>100%</span></th>
                            <td><span>100%</span></th>
                            <td><span>100%</span></th>
                            <td><span>55.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>20.20%</span></th>
                            <td><span>10.20%</span></th>
                        </tr> 
                        <tr class="red">
                            <td><span>1</span></th>
                            <td><span><img style="border-radius: 50%;" width="24" height="24" src="images/champion/Alistar46.png" alt="" /></span></th>
                            <td><span>0.48%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>100%</span></th>
                            <td><span>100%</span></th>
                            <td><span>100%</span></th>
                            <td><span>100%</span></th>
                            <td><span>55.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>0.20%</span></th>
                            <td><span>20.20%</span></th>
                            <td><span>10.20%</span></th>
                        </tr>                        
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
