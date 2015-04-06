			// Text store betweent textbox updates
			$updateText = "";
			

			function appendTextBox(text, time) {
					$updateText += '<p><span class="xd"><span class="chat_time">[' + secToMin(time/1000) + ']</span><span class="chat_info">' + text + '</span></span></p>';
			}
			
			function updateTextBox() {
				$($updateText).appendTo($commentBox);
				$updateText = "";
				$commentBox.scrollTop($commentBox[0].scrollHeight);
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
					} else if (type == "CHAMPION_KILL") {
                                            if ($evt["killerId"] == 0) {
                                                $string = "<img class=\"champImageSmall\" data-uk-tooltip title=\"Minion\" src=\"images/champion/Minions.png\" /> ";
                                            } else {
												updateCurrentKDA($evt["killerId"], $evt["victimId"], $evt["assistingParticipantIds"]);
												$killern = $champs[$participants[$evt["killerId"]]["championId"]];
                                                $string  = "<img class=\"champImageSmall\" style=\"border: 1px solid " + $team[$participants[$evt["killerId"]]["teamId"]]+ "\" data-uk-tooltip title=\"" + $killern + " " + kda($evt["killerId"]) + "\" src=\"images/champion/" + $killern + "46.png\" /> ";
                                            }
                                            $victimn = $champs[$participants[$evt["victimId"]]["championId"]];
                                          $string += " killed " + "<img class=\"champImageSmall\" style=\"border: 1px solid " + $team[$participants[$evt["victimId"]]["teamId"]]+ "\" data-uk-tooltip title=\"" + $victimn + " " + kda($evt["victimId"]) +"\" width=\"46\" height=\"46\" src=\"images/champion/" + $victimn + "46.png\" /> "; 
                                        } else {
						$string = "";
					}
					return $string;
			}

			function kda($id) {
				$part = $participants[$id];
				$string = "(" + $part["currentKills"] + "-" + $part["currentDeaths"] + "-" + $part["currentAssists"] + ")";
				return $string;
			}

			function updateCurrentKDA($killer, $victim, $assists) {
				$participants[$killer]["currentKills"]++;
				$participants[$victim]["currentDeaths"]++;
				if (typeof $assists != 'undefined') {
					for(i = 0; i < $assists.length; i++) {
						$participants[$assists[i]]["currentAssists"]++;
					}
				}
			}
			function teamSpan($teamId) {
				return '<span class="participant' + $team[$teamId] +'">';
			}