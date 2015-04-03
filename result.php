<?php
// Config and constants
include("config/config.php");
include("functions.php");
include("classes/match.php");
// Load match
if (isset($_GET['matchId'])) {
    $matchId = $_GET['matchId'];
} else {
    $matchId = getRandomMatchId();
}

if (!is_int($matchId)) {
    // do error handling here
}
$match = new Match(file_get_contents("data/" . $matchId . ".json"));
$events = $match->getEvents(array("CHAMPION_KILL"));
// Generate map from participantId -> champ name
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
                            <script type="text/javascript">
                        // set minutes
                        var mins = 44.10;

                        // calculate the seconds (don't change this! unless time progresses at a different speed for you...)
                        var secs = mins * 60;
                        function countdown() {
                            setTimeout('Decrement()', 1000);
                        }
                        function Decrement() {
                            if (document.getElementById) {
                                minutes = document.getElementById("minutes");
                                seconds = document.getElementById("seconds");
                                // if less than a minute remaining
                                if (seconds < 59) {
                                    seconds.value = secs;
                                } else {
                                    minutes.value = getminutes();
                                    seconds.value = getseconds();
                                }
                                secs--;
                                setTimeout('Decrement()', 1000);
                            }
                        }
                        function getminutes() {
                            // minutes is seconds divided by 60, rounded down
                            mins = Math.floor(secs / 60);
                            return mins;
                        }
                        function getseconds() {
                            // take mins remaining (as seconds) away from total seconds remaining
                            return secs - Math.round(mins * 60);
                        }
                    </script>
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
        <link rel="stylesheet" href="css/timeline.css">
        <script>
            $(document).ready(function () {
                $('#timeline').timeliner({events: [<?php
$string = "";
foreach ($events as $event) {
    $string .= ((int) ($event['timestamp'] / 1000)) . ',';
}
echo rtrim($string, ',');
?>], showEvent: [<?php
$string = "";
foreach ($events as $event) {
    $string .= 'true,';
}
echo rtrim($string, ',');
?>], hoverText: [<?php
$string = "";
foreach ($events as $event) {
    $string .= '"Test",';
}
echo rtrim($string, ',');
?>]});
            });
            function event_callback(time) {
                //console.log(time);
            }
        </script>
        <!-- timeline -->
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    </head>
    <body>
        <div class="main_info">
            <div class="part_chat">
                <div class="highelight">Highlight Match ..  <div id="timer">
                        <input id="minutes" size="1" type="text" disabled="">
                        <input id="seconds" size="1" type="text" disabled="">
                    </div>
                    <script>
                        countdown();
                    </script></div>
                <div id="comments" class="highelight_comment">
                    <?php
                    $content = json_decode(file_get_contents("data/mapinfo.json"), true);
                    $timer = $content['matchDuration'] /60;
                    echo $min = round($timer,2);
                    ?>
                    <p><span class="chat_time">[0:01 PM]</span><span class="chat_info">Welcome to Summoner's Rift!</span></p> 
                    <p><span class="chat_time">[1:25 PM]</span><span class="chat_info">Thirty seconds until minions spawn!</span></p>
                    <p><span class="chat_time">[1:55 PM]</span><span class="chat_info">Minions have spawned!</span></p>
                    <p><span class="chat_time">[2:25 PM]</span><span class="chat_info"><span class="participantblue">participantId (Thresh)</span> Has <span class="events">Level Up!</span></span></p>
                    <p><span class="chat_time">[3:01 PM]</span><span class="chat_info"><span class="participantred">participantId (Jinx)</span> Has <span class="events">ITEM PURCHASED</span> Named <span class="items_name">Doran Shiled</span> </span></p>
                    <p><span class="chat_time">[4:01 PM]</span><span class="chat_info"><span class="participantblue">creatorId (Sion)</span> Has <span class="events">WARD PLACED</span> With Type  <span class="items_name">YELLOW TRINKET</span> </span></p>
                    <p><span class="chat_time">[5:01 PM]</span><span class="chat_info"><span class="participantred">killerId (Jinx)</span> Has <span class="events">Kill</span> <span class="participantblue">victimId (Thresh)</span> For <span class="items_name">First Blood !</span> </span></p>
                    <p><span class="chat_time">[6:01 PM]</span><span class="chat_info"><span class="participantred">participantId (Jinx)</span> Has <span class="events">ITEM DESTROYED</span> Named <span class="items_name">Doran Shiled</span> </span></p>
                    <p><span class="chat_time">[7:01 PM]</span><span class="chat_info"><span class="participantred">participantId (Jinx)</span> Has <span class="events">Destroy</span> OUTER TURRET For <span class="participantblue">Blue Team</span> In MID LANE</span></p>
                    <p><span class="chat_time">[8:01 PM]</span><span class="chat_info"><span class="participantblue">participantId (Sion)</span> Has <span class="events">Kill</span> Monster <span class="monsters">RED LIZARD</span></span></p>
                    <p><span class="chat_time">[8:01 PM]</span><span class="chat_info"><span class="participantred">participantId (Jinx)</span> Has <span class="events">Kill</span> Monster <span class="monsters">Dragon</span></span></p>
                    <p><span class="chat_time">[2:25 PM]</span><span class="chat_info"><span class="participantblue">participantId (Sion)</span> Has <span class="events">Level Up!</span></span></p>
                    <p><span class="chat_time">[7:01 PM]</span><span class="chat_info"><span class="participantred">participantId (Jinx)</span> Has <span class="events">Destroy</span> INHIBITOR BUILDING For <span class="participantblue">Blue Team</span> In MID LANE</span></p>
                </div>
            </div>
            <div class="part_map">  
                <div id="map"></div>
                <script>
                    var cords = [
                        [4940, 13651], [8955, 8510], [7016, 10775], [11598, 11667], [13052, 12612], [10504, 1029], [12611, 13084]
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

                    svg = d3.select("#map").append("svg:svg")
                            .attr("width", width)
                            .attr("height", height);

                    svg.append('image')
                            .attr('xlink:href', bg)
                            .attr('x', '0')
                            .attr('y', '0')
                            .attr('width', '530')
                            .attr('height', height);
                    svg.append('svg:g').selectAll("circle")
                            .data(cords)
                            .enter().append("svg:circle")
                            .attr('cx', function (d) {
                                return xScale(d[0]);
                            })
                            .attr('cy', function (d) {
                                return yScale(d[1]);
                            })
                            .attr('r', 8)
                            .attr('class', 'kills');
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
