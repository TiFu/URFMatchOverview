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
						$string = teamSpan($evt['teamId']);
						var $killer = $participants[$evt["killerId"]];
						
						if ($evt['buildingType'] == "TOWER_BUILDING") {
							$string +=$lanes[$evt['laneType']] + " " + $towerTypes[$evt['towerType']] + "</span> Got Destroyed";
							updateTowerCount(($evt["teamId"] == 100 ? 200 : 100)); // reverse teams (blue team turret destroyed -> redTeamCounter++)
						} else { // inhib
							$string += $lanes[$evt['laneType']] + " Inhibitor</span> Got Destroyed";							
						}
						
						if (killerId != 0) {
							$string += ' By ' + teamSpan($killer['teamId']) + $champs[$killer['championId']] + '</span> !';
						} else {
							$string += ' By a Minion!';
						}
					} else if (type == "ELITE_MONSTER_KILL" && ($evt["monsterType"] == "BARON_NASHOR" || $evt["monsterType"] == "DRAGON")) {
						var killerId = $evt["killerId"];
						var $killer = $participants[$evt["killerId"]];
						$string = makeFirstLetterCapital(transform($evt["monsterType"])) + ' Was Slain By ' + teamSpan($killer['teamId']) + $champs[$killer['championId']] + '</span> !';
							if ($evt["monsterType"] == "DRAGON") {
								updateDragonCount($killer['teamId']);
							} else if ($evt["monsterType"] == "BARON_NASHOR") {
								updateBaronCount($killer["teamId"]);
							}
					} else if (type == "CHAMPION_KILL") {
                                            if ($evt["killerId"] == 0) {
                                                $string = "<img alt=\"Minion\" class=\"champImageSmall\" data-uk-tooltip title=\"Minion\" src=\"images/champion/Minions.png\" /> ";
                                            } else {
												updateCurrentKDA($evt["killerId"], $evt["victimId"], $evt["assistingParticipantIds"]);
												$killern = $champs[$participants[$evt["killerId"]]["championId"]];
                                                $string  = "<img alt=\"" + $killern + "\" class=\"champImageSmall\" style=\"border: 1px solid " + $team[$participants[$evt["killerId"]]["teamId"]]+ "\" data-uk-tooltip title=\"" + $killern + " (" + kda($evt["killerId"]) + ")\" src=\"images/champion/" + $killern.replace(" ", "%20") + "46.png\" /> ";
                                            }
                                            $victimn = $champs[$participants[$evt["victimId"]]["championId"]];
                                          $string += " killed " + "<img alt=\"" + $victimn + "\" class=\"champImageSmall\" style=\"border: 1px solid " + $team[$participants[$evt["victimId"]]["teamId"]]+ "\" data-uk-tooltip title=\"" + $victimn + " (" + kda($evt["victimId"]) +")\" width=\"46\" height=\"46\" src=\"images/champion/" + $victimn.replace(" ", "%20") + "46.png\" /> "; 
                    } else if (type == "STAT_UPDATE") {
						updateStats($evt['data']);
						$string = "";
					} else {
						$string = "";
					} 
					return $string;
			}

			function kda($id) {
				$part = $participants[$id];
				$string = "" + $part["currentKills"] + " - " + $part["currentDeaths"] + " - " + $part["currentAssists"] + "";
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