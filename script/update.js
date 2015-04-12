			// Text store betweent textbox updates
			$updateText = null;

			function appendTextBox(text, time) {
					if (text == null) {
						return;
					}
					var $p = $('<p><span class="xd"></span></p>');
					var $time = $('<span class="chat_time">[' + secToMin(time/1000) + ']</span>').appendTo($p);
					var $info = $('<span class="chat_info"></span>').appendTo($p);
					text.appendTo($info);
					if ($updateText == null) {
						$updateText = $p;
					} else {
						$updateText = $updateText.add($p);
					}
			}
			
			function updateTextBox() {
				if ($updateText != null) {
					$updateText.appendTo($commentBox);
					$commentBox.scrollTop($commentBox[0].scrollHeight);
					$updateText = null;
				}
			}
			
			function generateEventString($evt) {
					var type = $evt["eventType"];
					if (type == "BUILDING_KILL") {

						if ($evt['buildingType'] == "TOWER_BUILDING") {
							var $victim = new Turret($evt["laneType"], $evt["towerType"], $evt["teamId"]);
						} else if ($evt["buildingType"] == "INHIBITOR_BUILDING"){ 
							var $victim = new Inhibitor($evt["laneType"], $evt["teamId"]);
						}
						
						var $champName = $evt["killerId"] != 0 ? $champs[$participants[$evt["killerId"]]["championId"]] : "Minion";
						var $killerTeam = $evt["teamId"] == 100 ? 200 : 100;
						var $killer = new Champion($champName, $killerTeam);

						$string = $killer.getTextboxImage().add($($templates["highlights"]["BUILDING_KILL"])).add($victim.getTextboxImage());
					} else if (type == "ELITE_MONSTER_KILL" && ($evt["monsterType"] == "BARON_NASHOR" || $evt["monsterType"] == "DRAGON")) {
						var $monster = new Monster($evt["monsterType"]);
						var $killerPart = $participants[$evt["killerId"]];
						var $killer = new Champion($champs[$killerPart["championId"]], $killerPart["teamId"]);
						
						$string = $killer.getTextboxImage().add($($templates["highlights"]["ELITE_MONSTER_KILL"])).add($monster.getTextboxImage());
					} else if (type == "CHAMPION_KILL") {
						var $victimChamp = $champs[$participants[$evt["victimId"]]["championId"]];
						var $victimTeam = $participants[$evt["victimId"]]["teamId"];
						var $victim = new Champion($victimChamp, $victimTeam);

						var $killerChamp = $evt["killerId"] != 0 ? $champs[$participants[$evt["killerId"]]["championId"]] : "Minion";
						var $killerTeam = $victimTeam == 100 ? 200 : 100; // opposite team got the killer
						var $killer = new Champion($killerChamp, $killerTeam);
						
						$string = $killer.getTextboxImage().add($($templates["highlights"]["CHAMPION_KILL"])).add($victim.getTextboxImage());
                    } else {
						var $string = null;
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
						// Replace? 
						$item.attr("src", $item.attr("src").replace(/(.*)\/.*(\.png$)/i, '$1/' + $id + '$2'));
						$item.attr("original-title", $items[$id]);
						$item.attr("alt", $items[$id]);	
					}
					$participants[i]["field"]["currentGold"].html(formatMoney($stats[i]["totalGold"]));
					$participants[i]["field"]["currentMinions"].html($stats[i]["minionsKilled"]);
					$participants[i]["field"]["level"].html($stats[i]["level"]);
				}
				// Update gold counts
				$goldCountField[100].html(Math.round(blueSum / 100.0) / 10.0 + "K")
				$goldCountField[200].html(Math.round(redSum / 100.0) / 10.0 + "K")
			}
			
			function formatMoney(money) {
				money = "" + money;
				if (money.length > 3) {
				return [money.slice(0,money.length-3), ".", money.slice(money.length-3, money.length)].join("");
				} else {
					return money;
				}
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
				if ($participants[$victim]["teamId"] == 200) {
					var $value = $blueTeamKillsField.html();
					$value++;
					$blueTeamKillsField.html($value);
				} else if ($participants[$victim]["teamId"] == 100) {
					var $value = $redTeamKillsField.html();
					$value++;
					$redTeamKillsField.html($value);
				}
				if ($killer != 0) {
					$participants[$killer]["currentKills"]++;
					$participants[$killer]["field"].find(".kda").html(kda($killer));
				}
					$participants[$victim]["currentDeaths"]++;
					$participants[$victim]["field"].find(".kda").html(kda($victim));
				if (typeof $assists != 'undefined') {
					for(i = 0; i < $assists.length; i++) {
						$participants[$assists[i]]["currentAssists"]++;
						$participants[$assists[i]]["field"].find(".kda").html(kda($assists[i]));
					}
				}
			}
			
			idCounter = 0;
			timers = new Array(); // contains all timers
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

			function getEventTooltip($event) {
				var $string = $templates["map"]["KILL"];
				if ($event["eventType"] == "BUILDING_KILL") {
					var $killerChamp = $event["killerId"] == 0 ? "Minion" : $champs[$participants[$event["killerId"]]["championId"]];
					var $killerTeam = $event["teamId"] == 100 ? 200 : 100;
					var $killer = new Champion($killerChamp, $killerTeam);

					if ($event["buildingType"] == "TOWER_BUILDING") {
						var $victim = new Tower($event["laneType"], $event["towerType"], $event["teamId"]);
					} else if ($event["buildingType"] == "INHIBITOR_BUILDING") {
						var $victim = new Inhibitor($event["laneType"], $event["teamId"]);
					}
					$string = $string.replace("{victim}", $victim.getMapImage()).replace("{killer}", $killer.getMapImage());	
				} else if ($event["eventType"] == "ELITE_MONSTER_KILL") {
					var $teamId = $participants[$event["killerId"]]["teamId"];
					var $killerChamp = $champs[$participants[$event["killerId"]]["championId"]];
					var $killer = new Champion($killerChamp, $teamId);
					
					var $victim = new Monster($event["monsterType"]);
					$string = $string.replace("{victim}", $victim.getMapImage()).replace("{killer}", $killer.getMapImage());
				} else if ($event["eventType"] == "CHAMPION_KILL") {
					var $victimTeam = $participants[$event["victimId"]]["teamId"];
					var $victimChamp = $champs[$participants[$event["victimId"]]["championId"]];
					var $victim = new Champion($victimChamp, $victimTeam);

					var $killerChamp = $event["killerId"] == 0 ? "Minion" : $champs[$participants[$event["killerId"]]["championId"]];
					var $killerTeam = $victimTeam == 100 ? 200 : 100;
					var $killer = new Champion($killerChamp, $killerTeam);
					var $string = $string.replace("{victim}", $victim.getMapImage()).replace("{killer}", $killer.getMapImage());
				} else { // nothing needed. clear string
					var $string = "";
				}
				return $string;
			}
