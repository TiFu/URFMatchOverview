			// Text store betweent textbox updates
			var $updateText = "";
			function appendTextBox(text, time) {
					$updateText += '<p><span class="chat_time">[' + secToMin(time/1000) + ']</span><span class="chat_info">' + text + '</span></p>';
			}
			
			function updateTextBox() {
				document.getElementById("comments").innerHTML += $updateText;
				$updateText = "";
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
							$string += makeFirstLetterCapital(transform($evt['laneType'])) + " " + transform($evt['towerType']) + "</span> got destroyed";
						} else { // inhib
							$string += makeFirstLetterCapital(transform($evt['laneType'])) + " inhibitor</span> got destroyed";							
						}
						
						if (killerId != 0) {
							$string += ' by ' + teamSpan($killer['teamId']) +	$champs[$killer['championId']] + '</span>!';
						} else {
							$string += ' by a minion!';
						}
					} else if (type == "ELITE_MONSTER_KILL" && ($evt["monsterType"] == "BARON_NASHOR" || $evt["monsterType"] == "DRAGON")) {
						var killerId = $evt["killerId"];
						var $killer = $participants[$evt["killerId"]];
						$string = makeFirstLetterCapital(transform($evt["monsterType"])) + ' was slain by ' + teamSpan($killer['teamId']) + $champs[$killer['championId']] + '</span>!';
					} else {
						$string = "";
					}

					return $string;
			}
			
			
			function teamSpan($teamId) {
				return '<span class="participant' + $team[$teamId] +'">';
			}