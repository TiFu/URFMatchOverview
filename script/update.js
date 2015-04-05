			// Text store betweent textbox updates
			var $updateText = "";
			function appendTextBox(text, time) {
					$updateText += '<p><span class="chat_time">[' + secToMin(time/1000) + ']</span><span class="chat_info">' + text + '</span></p>';
			}
			
			function updateTextBox() {
				document.getElementById("comments").innerHTML += $updateText;
				$updateText = "";
                                  var elem = document.getElementById('comments');
                                    elem.scrollTop = elem.scrollHeight;
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

			function generateEventString($evt) {
					var type = $evt["eventType"];
					if (type == "BUILDING_KILL") {
						var killerId = $evt["killerId"];
						$string = teamSpan($evt['teamId']);
						var $killer = $participants[$evt["killerId"]];
						
						if ($evt['buildingType'] == "TOWER_BUILDING") {
							$string += makeFirstLetterCapital(transform($evt['laneType'])) + " " + transform($evt['towerType']) + "</span> Got Destroyed";
						} else { // inhib
							$string += makeFirstLetterCapital(transform($evt['laneType'])) + " Inhibitor</span> Got Destroyed";							
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
                                                $string = "<img data-uk-tooltip title=\"Minion\" src=\"images/champion/Minions.png\" /> ";
                                            } else {
                                                $killern = $champs[$participants[$evt["killerId"]]["championId"]];
                                                $string  = "<img data-uk-tooltip title=\"" + $killern + "\" + width=\"46\" height=\"46\" src=\"images/champion/" + $killern + ".png\" /> ";
                                            }
                                            $victimn = $champs[$participants[$evt["victimId"]]["championId"]];
                                          $string += " killed " + "<img data-uk-tooltip title=\"" + $victimn + "\" width=\"46\" height=\"46\" src=\"images/champion/" + $victimn + ".png\" /> "; 
                                        } else {
						$string = "";
					}

					return $string;
			}
			
			
			function teamSpan($teamId) {
				return '<span class="participant' + $team[$teamId] +'">';
			}