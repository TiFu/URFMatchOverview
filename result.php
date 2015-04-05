<?php
error_reporting(E_ALL);
// Config and constants
include("config/config.php");
include("functions.php");
include("classes/match.php");
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
$match = new Match(file_get_contents("data/".$matchId.".json"));
$startEvents = $match->getEvents(array("CHAMPION_KILL", "BUILDING_KILL", "ELITE_MONSTER_KILL"));
$logEvents = array();

// Filter red and blue buff out of the events
foreach($startEvents as $event) {
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
        <title>TODO supply a title</title> 
        <meta charset="UTF-8" />
        <script src="script/d3.v3.min.js"></script>
        <!-- timeline -->
        <link id="data-uikit-theme" rel="stylesheet" href="tip/uikit.docs.min.css">
        <script src="tip/jquery.js"></script>
        <script src="tip/uikit.min.js"></script>
        <script src="tip/tooltip.js"></script>
        <script src='script/jquery-1.6.2.min.js'></script>
        <script src="script/jquery.pause.min.js"></script>
        <script src="script/jquery.timer.js"></script>
        <script src="script/timeline.js"></script>
 	<script src="script/functions.js"></script>
        <script src="script/update.js"></script>
        <script src="script/timelineCallbacks.js"></script>
	<link rel="stylesheet" href="css/timeline.css">
        <script>
                        window.drawn=false;
                        
			$winner = '<?php echo $match->getWinner()['teamId']; ?>';
			$duration = '<?php echo $match->getDuration(); ?>';
			$textboxInterval = null;

			$team = new Array();
			$team['100'] = "blue";
			$team['200'] = "red";

			$participants = new Array();
			<?php
				foreach($match->getParticipants() as $participant) {
					echo '$participants[' .$participant['participantId'] .'] = ' .json_encode($participant) .';';
				}
			?>

			$champs = new Array();
			<?php
				$champs = $mysqli->query("SELECT * FROM " .CHAMP_TABLE);
				while ($champ = $champs->fetch_assoc()) {
					echo '$champs[' .$champ['id'] .'] = "' .$champ['name'] .'";';
				}
			?>

			var $evts = 
			<?php
				$jsonEvents = json_encode($logEvents);
				echo $jsonEvents;
			?>;
		
			/**
			 *  Initialize the timeline with events, showEvent and hoverText (currently BS)
			 */
            $(document).ready(function () {
                $('#timeline').timeliner({events: [<?php 
					$string = "";
					foreach ($logEvents as $event) {
						$string .= ((int) ($event['timestamp']/1000)) .',';
					}
					echo rtrim($string, ',');
				?>], showEvent: [<?php 
					$string = "";
					foreach ($logEvents as $event) {
						$string .= ($event['eventType'] != 'CHAMPION_KILL') .',';
					}
					echo rtrim($string, ',');
				?>], hoverText: [<?php 
					$string = "";
					foreach ($logEvents as $event) {
						$string .= '"' .$event['eventType'] .'",';
					}
					echo rtrim($string, ',');
			?>], timeLength: <?php echo $match->getDuration() ?>});
				// Update textBox periodically (every 250ms)
				$textboxInterval = setInterval(updateTextBox, 250);
			});
        </script>
       <!-- timeline -->
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    </head>
    <body>
        <script>
 window.onkeydown = function(e) {
    if(e.keyCode === 32 && e.target === document.body) {
        e.preventDefault();
        $('timeline').timeliner.pauseplay();
        return false;  
    }
};
</script>
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
                   
                function drawomap(cord){
                    var cords = [ cord
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
                            
                            if(drawn!=true){
                             this.svg = d3.select("#map").append("svg:svg")
                            .attr("width", width)
                            .attr("height", height);

                    this.svg.append('image')
                            .attr('xlink:href', bg)
                            .attr('x', '0')
                            .attr('y', '0')
                            .attr('width', '530')
                            .attr('height', height);
                drawn=true;     
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
            <div id ="timeline" class="timeline">
            </div>
            <div class="clear"></div>
            <div class="statsteam">
                <div class="summary">
                    <div class="tred">Victory</div>
                    <div class="redgold">0.0K</div>
                    <div class="destroyred">Towers : 0  - Dragons : 0  - Baron : 0</div>                    
                    <div class="tblue">Defeat</div>
                    <div class="redgold">0.0K</div>
                    <div class="destroyred">Towers : 0  - Dragons : 0  - Baron : 0</div>    
                    <div class="score">10-15</div>

                </div>
                <div class="redteam">

                    <div class="champlloadin">
                        <div class="champpic">
                            <span class="level" data-uk-tooltip title="Level">18</span>
                            <img src="images/chmp.jpg" width="79" height="79" alt="championname" />
                            <div class="summonerspell">
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                            </div>                                
                        </div> 
                        <div class="summonerkda">
                            Twisted Fate <br />
                            <span class="kda" data-uk-tooltip title="Kill - Death - Assist">0 - 0 - 0</span>
                        </div>       
                        <div class="champbuild">
                            <table>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td class="trinket" colspan="3"><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                </tr>                              
                            </table>
                        </div>
                        <div class="goldminions">
                            <div class="gold">Gold</div>
                            <div class="countgold">12000</div>    
                        </div>
                        <div class="minion">
                            <div class="gold">Minions</div>
                            <div class="countgold">12000</div>    
                        </div>                        
                    </div>  
                    <div class="champlloadin">
                        <div class="champpic">
                            <span class="level" data-uk-tooltip title="Level">18</span>
                            <img src="images/chmp.jpg" width="79" height="79" alt="championname" />
                            <div class="summonerspell">
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                            </div>                                
                        </div> 
                        <div class="summonerkda">
                            Twisted Fate <br />
                            <span class="kda" data-uk-tooltip title="Kill - Death - Assist">0 - 0 - 0</span>
                        </div>       
                        <div class="champbuild">
                            <table>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td class="trinket" colspan="3"><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                </tr>                              
                            </table>
                        </div>
                        <div class="goldminions">
                            <div class="gold">Gold</div>
                            <div class="countgold">12000</div>    
                        </div>
                        <div class="minion">
                            <div class="gold">Minions</div>
                            <div class="countgold">12000</div>    
                        </div>                        
                    </div> 
                    <div class="champlloadin">
                        <div class="champpic">
                            <span class="level" data-uk-tooltip title="Level">18</span>
                            <img src="images/chmp.jpg" width="79" height="79" alt="championname" />
                            <div class="summonerspell">
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                            </div>                                
                        </div> 
                        <div class="summonerkda">
                            Twisted Fate <br />
                            <span class="kda" data-uk-tooltip title="Kill - Death - Assist">0 - 0 - 0</span>
                        </div>       
                        <div class="champbuild">
                            <table>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td class="trinket" colspan="3"><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                </tr>                              
                            </table>
                        </div>
                        <div class="goldminions">
                            <div class="gold">Gold</div>
                            <div class="countgold">12000</div>    
                        </div>
                        <div class="minion">
                            <div class="gold">Minions</div>
                            <div class="countgold">12000</div>    
                        </div>                        
                    </div> 
                    <div class="champlloadin">
                        <div class="champpic">
                            <span class="level" data-uk-tooltip title="Level">18</span>
                            <img src="images/chmp.jpg" width="79" height="79" alt="championname" />
                            <div class="summonerspell">
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                            </div>                                
                        </div> 
                        <div class="summonerkda">
                            Twisted Fate <br />
                            <span class="kda" data-uk-tooltip title="Kill - Death - Assist">0 - 0 - 0</span>
                        </div>       
                        <div class="champbuild">
                            <table>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td class="trinket" colspan="3"><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                </tr>                              
                            </table>
                        </div>
                        <div class="goldminions">
                            <div class="gold">Gold</div>
                            <div class="countgold">12000</div>    
                        </div>
                        <div class="minion">
                            <div class="gold">Minions</div>
                            <div class="countgold">12000</div>    
                        </div>                        
                    </div> 
                    <div class="champlloadin">
                        <div class="champpic">
                            <span class="level" data-uk-tooltip title="Level">18</span>
                            <img src="images/chmp.jpg" width="79" height="79" alt="championname" />
                            <div class="summonerspell">
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                            </div>                                
                        </div> 
                        <div class="summonerkda">
                            Twisted Fate <br />
                            <span class="kda" data-uk-tooltip title="Kill - Death - Assist">0 - 0 - 0</span>
                        </div>       
                        <div class="champbuild">
                            <table>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td class="trinket" colspan="3"><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                </tr>                              
                            </table>
                        </div>
                        <div class="goldminions">
                            <div class="gold">Gold</div>
                            <div class="countgold">12000</div>    
                        </div>
                        <div class="minion">
                            <div class="gold">Minions</div>
                            <div class="countgold">12000</div>    
                        </div>                        
                    </div> 
                </div>
                <div class="blueteam">
                    <div class="champlloadin">
                        <div class="champpic">
                            <span class="level" data-uk-tooltip title="Level">18</span>
                            <img src="images/chmp.jpg" width="79" height="79" alt="championname" />
                            <div class="summonerspell">
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                            </div>                                
                        </div> 
                        <div class="summonerkda">
                            Twisted Fate <br />
                            <span class="kda" data-uk-tooltip title="Kill - Death - Assist">0 - 0 - 0</span>
                        </div>       
                        <div class="champbuild">
                            <table>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td class="trinket" colspan="3"><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                </tr>                              
                            </table>
                        </div>
                        <div class="goldminions">
                            <div class="gold">Gold</div>
                            <div class="countgold">12000</div>    
                        </div>
                        <div class="minion">
                            <div class="gold">Minions</div>
                            <div class="countgold">12000</div>    
                        </div>                        
                    </div> 
                    <div class="champlloadin">
                        <div class="champpic">
                            <span class="level" data-uk-tooltip title="Level">18</span>
                            <img src="images/chmp.jpg" width="79" height="79" alt="championname" />
                            <div class="summonerspell">
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                            </div>                                
                        </div> 
                        <div class="summonerkda">
                            Twisted Fate <br />
                            <span class="kda" data-uk-tooltip title="Kill - Death - Assist">0 - 0 - 0</span>
                        </div>       
                        <div class="champbuild">
                            <table>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td class="trinket" colspan="3"><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                </tr>                              
                            </table>
                        </div>
                        <div class="goldminions">
                            <div class="gold">Gold</div>
                            <div class="countgold">12000</div>    
                        </div>
                        <div class="minion">
                            <div class="gold">Minions</div>
                            <div class="countgold">12000</div>    
                        </div>                        
                    </div> 
                    <div class="champlloadin">
                        <div class="champpic">
                            <span class="level" data-uk-tooltip title="Level">18</span>
                            <img src="images/chmp.jpg" width="79" height="79" alt="championname" />
                            <div class="summonerspell">
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                            </div>                                
                        </div> 
                        <div class="summonerkda">
                            Twisted Fate <br />
                            <span class="kda" data-uk-tooltip title="Kill - Death - Assist">0 - 0 - 0</span>
                        </div>       
                        <div class="champbuild">
                            <table>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td class="trinket" colspan="3"><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                </tr>                              
                            </table>
                        </div>
                        <div class="goldminions">
                            <div class="gold">Gold</div>
                            <div class="countgold">12000</div>    
                        </div>
                        <div class="minion">
                            <div class="gold">Minions</div>
                            <div class="countgold">12000</div>    
                        </div>                        
                    </div> 
                    <div class="champlloadin">
                        <div class="champpic">
                            <span class="level" data-uk-tooltip title="Level">18</span>
                            <img src="images/chmp.jpg" width="79" height="79" alt="championname" />
                            <div class="summonerspell">
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                            </div>                                
                        </div> 
                        <div class="summonerkda">
                            Twisted Fate <br />
                            <span class="kda" data-uk-tooltip title="Kill - Death - Assist">0 - 0 - 0</span>
                        </div>       
                        <div class="champbuild">
                            <table>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td class="trinket" colspan="3"><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                </tr>                              
                            </table>
                        </div>
                        <div class="goldminions">
                            <div class="gold">Gold</div>
                            <div class="countgold">12000</div>    
                        </div>
                        <div class="minion">
                            <div class="gold">Minions</div>
                            <div class="countgold">12000</div>    
                        </div>                        
                    </div> 
                    <div class="champlloadin">
                        <div class="champpic">
                            <span class="level" data-uk-tooltip title="Level">18</span>
                            <img src="images/chmp.jpg" width="79" height="79" alt="championname" />
                            <div class="summonerspell">
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                                <img src="images/flash.png" data-uk-tooltip title="Flash" width="37" height="37" alt="championname" />
                            </div>                                
                        </div> 
                        <div class="summonerkda">
                            Twisted Fate <br />
                            <span class="kda" data-uk-tooltip title="Kill - Death - Assist">0 - 0 - 0</span>
                        </div>       
                        <div class="champbuild">
                            <table>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                    <td class="trinket" colspan="3"><img src="images/item.png" data-uk-tooltip title="Flash" width="38" height="38" alt="championname" /></td>
                                </tr>
                                <tr>
                                </tr>                              
                            </table>
                        </div>
                        <div class="goldminions">
                            <div class="gold">Gold</div>
                            <div class="countgold">12000</div>    
                        </div>
                        <div class="minion">
                            <div class="gold">Minions</div>
                            <div class="countgold">12000</div>    
                        </div>                        
                    </div> 
                </div>               
            </div>

        </div>  
    </body>
</html>
