			// Text store betweent textbox updates
			$updateText = "";
			

			function appendTextBox(text, time) {
					$updateText += '<p><span class="xd"><span class="chat_time">[' + secToMin(time/1000) + ']</span><span class="chat_info">' + text + '</span></span></p>';
			}
			
			function updateTextBox() {
				if ($updateText != "") {
					$($updateText).appendTo($commentBox);
					$commentBox.scrollTop($commentBox[0].scrollHeight);
					$updateText = "";
				}
			}
			function secToMin(sec) {
				var min  = Math.floor(sec / 60);
				var secs = sec - min*60;
				secs = Math.floor(secs);
				if (secs < 10) {
					secs = 0 + "" + secs;
				}
				return min + ":" + secs;
			}
			$lanes = [];
			$lanes["BOT_LANE"] = makeFirstLetterCapital(transform("BOT_LANE"));
			$lanes["MID_LANE"] = makeFirstLetterCapital(transform("MID_LANE"));
			$lanes["TOP_LANE"] = makeFirstLetterCapital(transform("TOP_LANE"));
			
			$towerTypes = [];
			$towerTypes["BASE_TURRET"] = transform("BASE_TURRET");
			$towerTypes["INNER_TURRET"] = transform("INNER_TURRET");
			$towerTypes["NEXUS_TURRET"] = transform("NEXUS_TURRET");
			$towerTypes["OUTER_TURRET"] = transform("OUTER_TURRET");
			
			function generateEventString($evt) {
					var type = $evt["eventType"];
					if (type == "BUILDING_KILL") {
						var killerId = $evt["killerId"];
						var $string = teamSpan($evt['teamId']);
						var $killer = $participants[$evt["killerId"]];
						
						if ($evt['buildingType'] == "TOWER_BUILDING") {
                                                    if($evt['teamId'] == 100){
							$string += "<img alt=\"" + $lanes[$evt['laneType']] + " " + $towerTypes[$evt['towerType']] + "\" class=\"champImageSmall\" data-uk-tooltip title=\""+ $lanes[$evt['laneType']] + " " + $towerTypes[$evt['towerType']] + "\" src=\"images/gamepic/t1.png\" />";
							updateTowerCount(($evt["teamId"] == 100 ? 200 : 100)); // reverse teams (blue team turret destroyed -> redTeamCounter++)
                                                    }
                                                    else{
                                                        $string += "<img alt=\"" + $lanes[$evt['laneType']] + " " + $towerTypes[$evt['towerType']] + "\" class=\"champImageSmall\" data-uk-tooltip title=\""+ $lanes[$evt['laneType']] + " " + $towerTypes[$evt['towerType']] + "\" src=\"images/gamepic/t.png\" />";
                                                        updateTowerCount(($evt["teamId"] == 100 ? 200 : 100));
                                                    }
                                                    
						} else { // inhib
                                                     if($evt['teamId'] == 100){
                                                        $string += "<img alt=\"" + $lanes[$evt['laneType']] + "Inhibitor\" class=\"champImageSmall\" data-uk-tooltip title=\"" + $lanes[$evt['laneType']] + " Inhibitor\" src=\"images/gamepic/b2.png\" />";
							updateTowerCount(($evt["teamId"] == 100 ? 200 : 100)); // reverse teams (blue team turret destroyed -> redTeamCounter++)
                                                    }
                                                    else{
                                                        $string += "<img alt=\"" + $lanes[$evt['laneType']] + "Inhibitor\" class=\"champImageSmall\" data-uk-tooltip title=\"" + $lanes[$evt['laneType']] + " Inhibitor\" src=\"images/gamepic/r2.png\" />";
                                                        updateTowerCount(($evt["teamId"] == 100 ? 200 : 100));
                                                    }                                                             
						}
						
						if (killerId != 0) {
							$string += ' Has been destroyed By ' + "<img data-uk-tooltip title=\"" + $champs[$killer['championId']] + " (" + kda($evt["killerId"]) + ")\" style=\"border: 1px solid " + $team[$participants[$evt["killerId"]]["teamId"]]+ "\" src=\"images/champion/"+ $champs[$killer['championId']] + "46.png\" alt=\"" + $champs[$killer['championId']] + "\" />";
						} else {
							$string += " Has been destroyed By <img alt=\"Minion\" class=\"champImageSmall\" data-uk-tooltip title=\"Minion\" src=\"images/champion/Minions.png\" />";
						}
					} else if (type == "ELITE_MONSTER_KILL" && ($evt["monsterType"] == "BARON_NASHOR" || $evt["monsterType"] == "DRAGON")) {
						var killerId = $evt["killerId"];
						var $killer = $participants[$evt["killerId"]];
						$string = "<img data-uk-tooltip src=\"images/gamepic/" + makeFirstLetterCapital(transform($evt["monsterType"])) + ".png\" alt=\"" + makeFirstLetterCapital(transform($evt["monsterType"])) + "\" title=\"" + makeFirstLetterCapital(transform($evt["monsterType"])) + "\" />" + ' Has been Slain By ' + "<img data-uk-tooltip style=\"border: 1px solid " + $team[$participants[$evt["killerId"]]["teamId"]]+ "\" src=\"images/champion/"+ $champs[$killer['championId']] + "46.png\" alt=\"" + $champs[$killer['championId']] + "\" />";
							if ($evt["monsterType"] == "DRAGON") {
								updateDragonCount($killer['teamId']);
							} else if ($evt["monsterType"] == "BARON_NASHOR") {
								updateBaronCount($killer["teamId"]);
							}
					} else if (type == "CHAMPION_KILL") {
                                            if ($evt["killerId"] == 0) {
                                                var $string = "<img alt=\"Minion\" class=\"champImageSmall\" data-uk-tooltip title=\"Minion\" src=\"images/champion/Minions.png\" /> ";
                                            } else {
												updateCurrentKDA($evt["killerId"], $evt["victimId"], $evt["assistingParticipantIds"]);
												$killern = $champs[$participants[$evt["killerId"]]["championId"]];
                                                var $string  = "<img alt=\"" + $killern + "\" class=\"champImageSmall\" style=\"border: 1px solid " + $team[$participants[$evt["killerId"]]["teamId"]]+ "\" data-uk-tooltip title=\"" + $killern + " (" + kda($evt["killerId"]) + ")\" src=\"images/champion/" + $killern.replace(" ", "%20") + "46.png\" /> ";
                                            }
                                            $victimn = $champs[$participants[$evt["victimId"]]["championId"]];
                                          $string += " killed " + "<img alt=\"" + $victimn + "\" class=\"champImageSmall\" style=\"border: 1px solid " + $team[$participants[$evt["victimId"]]["teamId"]]+ "\" data-uk-tooltip title=\"" + $victimn + " (" + kda($evt["victimId"]) +")\" width=\"46\" height=\"46\" src=\"images/champion/" + $victimn.replace(" ", "%20") + "46.png\" /> "; 
                    } else if (type == "STAT_UPDATE") {
						updateStats($evt['data']);
						var $string = "";
					} else {
						var $string = "";
					} 
					return $string;
			}

			function kda($id) {
				var $part = $participants[$id];
				var $string = "" + $part["currentKills"] + " - " + $part["currentDeaths"] + " - " + $part["currentAssists"] + "";
				return $string;
			}

			// Map from partId -> items
			function updateStats($stats) {
				var blueSum = 0;
				var redSum = 0;
				for (var i = 1; i <= 10; i++) { // iterate over all participants
					if ($participants[i]["teamId"] == 100) {
						blueSum += $stats[i]["totalGold"];
					} else if ($participants[i]["teamId"] == 200) {
						redSum += $stats[i]["totalGold"];
					}
					// items first
					$itemsIMG = $participants[i]["field"]["items"];
					for (var k = 0; k < 7; k++){
						$item = $($itemsIMG[k]);
						$id = $stats[i]["items"][k]["itemId"];
						var $elem = $item.parent().find("span"); // TODO propably save it 
						if ($stats[i]["items"][k]["stock"] > 1) {
							$elem.html($stats[i]["items"][k]["stock"]);
							$elem.show();
						} else {
							$elem.hide();
						}
						$item.attr("src", $item.attr("src").replace(/(.*)\/.*(\.png$)/i, '$1/' + $id + '$2'));
						$item.attr("title", $items[$id]);
						$item.attr("alt", $items[$id]);
						// TODO add stack counter wenn stack != 1
					}
					$participants[i]["field"]["currentGold"].html($stats[i]["totalGold"]);
					$participants[i]["field"]["currentMinions"].html($stats[i]["minionsKilled"]);
					$participants[i]["field"]["level"].html($stats[i]["level"]);
				}
				// Update gold counts
				$goldCountField[100].html(Math.round(blueSum / 100.0) / 10.0 + "K")
				$goldCountField[200].html(Math.round(redSum / 100.0) / 10.0 + "K")
			}
			function updateDragonCount($killerTeam) {
				var $value = $dragonCountField[$killerTeam].html();
				$value++;
				$dragonCountField[$killerTeam].html($value);
			}
			
			function updateBaronCount($killerTeam) {
				var $value = $baronCountField[$killerTeam].html();
				$value++;
				$baronCountField[$killerTeam].html($value);
			}
			
			function updateTowerCount($killerTeam) {
				var $valu = $towerCountField[$killerTeam].html();
				$valu++;
				$towerCountField[$killerTeam].html($valu);
			}
			function updateCurrentKDA($killer, $victim, $assists) {
				// Update team kill count
				if ($participants[$killer]["teamId"] == 100) {
					var $value = $blueTeamKillsField.html();
					$value++;
					$blueTeamKillsField.html($value);
				} else if ($participants[$killer]["teamId"] == 200) {
					var $value = $redTeamKillsField.html();
					$value++;
					$redTeamKillsField.html($value);
				}
				$participants[$killer]["currentKills"]++;
				$participants[$killer]["field"].find(".kda").html(kda($killer));
				$participants[$victim]["currentDeaths"]++;
				$participants[$victim]["field"].find(".kda").html(kda($victim));
				if (typeof $assists != 'undefined') {
					for(i = 0; i < $assists.length; i++) {
						$participants[$assists[i]]["currentAssists"]++;
						$participants[$assists[i]]["field"].find(".kda").html(kda($assists[i]));
					}
				}
			}
			function teamSpan($teamId) {
				return '<span class="participant' + $team[$teamId] +'">';
			}
			
			var idCounter = 0;
			var timers = new Array(); // contains all timers
            function drawomap($event) {
						var domain = {
                               min: {x: -1000, y: -570},
                               max: {x: 14800, y: 14800}
                        },

                        color = d3.scale.linear()
                                .domain([0, 3])
                                .range(["white", "steelblue"])
                                .interpolate(d3.interpolateLab);

                        xScale = d3.scale.linear()
                                .domain([domain.min.x, domain.max.x])
                                .range([0, 512]);

                        yScale = d3.scale.linear()
                                .domain([domain.min.y, domain.max.y])
                                .range([520, 0]);
						var circle = Pablo('<circle id="circle' + idCounter + '" class="kills" r="5" cx="' + xScale($event["position"]["x"]) + '" cy="' + yScale($event["position"]["y"]) + '" fill="' + getEventColor($event) + '"></circle>');
						$svg.append(circle);

						var $newCircle = $('#circle' + idCounter);
						$newCircle.tipsy({gravity:'s', html:true, title: function () {
							return getEventTooltip($event);
						}, opacity:1});

						if ($event["eventType"] != "BUILDING_KILL") {
							var timer = new Timer(removeCircle, 5000, circle);
							timers[timers.length] = timer;
						}
						idCounter++;
                        return true;
                    }

function Timer(callback, delay, arg1) {
    var timerId, start, remaining = delay;

    this.pause = function() {
        window.clearTimeout(timerId);
        remaining -= new Date() - start;
    };

    this.resume = function() {
        start = new Date();
        window.clearTimeout(timerId);
        timerId = window.setTimeout(callback, remaining, arg1);
    };

    this.resume();
}
					
			function removeCircle(circle) {
				circle.remove();
			}

			function getEventColor($event) {
				var $string = "";
				if ($event["eventType"] == "BUILDING_KILL") {
					$string = $event["teamId"] == 100 ? "red" : "blue";
				} else if ($event["eventType"] == "ELITE_MONSTER_KILL") {
					$string = $participants[$event["killerId"]]["teamId"] == 100 ? "blue" : "red";
				} else if ($event["eventType"] == "CHAMPION_KILL") {
					$string = $participants[$event["victimId"]]["teamId"] == 100 ? "red" : "blue";
				}
				return $string;
			}

			function getEventTooltip($event) {
				var $string = "";
				if ($event["eventType"] == "BUILDING_KILL") {
					if ($event["killerId"] != 0) {
						var $killerChamp = $champs[$participants[$event["killerId"]]["championId"]];
						$string = '<img style="border:2px solid ' + ($participants[$event["killerId"]]["teamId"] == 100 ? "blue" : "red") + '" class="tooltipMap" src="images/champion/' + $killerChamp + '.png" alt="' + $killerChamp + '">';
					} else {
						$string += '<img style="border: 2px solid ' + ($event["teamId"] == 100 ? "red" : "blue") + '" class="tooltipMap" src="images/' + ($event["teamId"] == 100 ? "minionRed.png" : "minionBlue.png") + '" alt="minion">';
					}
					$string += '<img src="images/kill_icon.png" style="margin-bottom:5px;" alt="kill"><img class="tooltipMap"  src="images/turret_' + $event["teamId"] + '.png" alt="turret">';
				} else if ($event["eventType"] == "ELITE_MONSTER_KILL") {
					var $teamId = $participants[$event["killerId"]]["teamId"];
					var $killerChamp = $champs[$participants[$event["killerId"]]["championId"]];
					$string = '<img  style="border:2px solid ' + ($participants[$event["killerId"]]["teamId"] == 100 ? "blue" : "red") + '"class="tooltipMap" src="images/champion/' + $killerChamp + '.png" alt="' + $killerChamp + '">';					
					$string += '<img src="images/kill_icon.png" style="margin-bottom:5px;" alt="kill"><img class="tooltipMap"  src="images/' + ($event["monsterType"] =="DRAGON" ? "dragon" : "baron_nashor") + '_' + $teamId + '.png" alt="turret">';
				} else if ($event["eventType"] == "CHAMPION_KILL") {
					$victimTeam = $participants[$event["victimId"]]["teamId"];
					$victimChamp = $champs[$participants[$event["victimId"]]["championId"]];
					if ($event["killerId"] != 0) {
						$killerTeam = $participants[$event["killerId"]]["teamId"];
						$killerChamp = $champs[$participants[$event["killerId"]]["championId"]];
						$string = '<img style="border:2px solid ' + ($participants[$event["killerId"]]["teamId"] == 100 ? "blue" : "red") + '" class="tooltipMap" src="images/champion/' + $killerChamp + '.png" alt="' + $killerChamp + '">';
					} else {
						$string += '<img style="border: 2px solid ' + ($event["teamId"] == 100 ? "red" : "blue") + '" class="tooltipMap" src="images/' + ($participants[$event["victimId"]]["teamId"] == 100 ? "minionRed.png" : "minionBlue.png") + '" alt="minion">';
					}		
					$string += '<img src="images/kill_icon.png" style="margin-bottom:5px;" alt="kill">';
					$string += '<img style="border:2px solid ' + ($participants[$event["victimId"]]["teamId"] == 100 ? "blue" : "red") +'"class="tooltipMap" src="images/champion/' + $victimChamp + '.png" alt="' + $victimChamp + '">';					
				}
				return $string;
			}
