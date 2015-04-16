			// Holds the current event pointer
			globalEventPointer = 0;
			/**
			 * Gets the last event pointer of a given time frame (keep track with a local eventPointer)
			 */
function event_callback(eventPointer) {
			// first create string
	while (globalEventPointer <= eventPointer) {
		var $event = $evts[globalEventPointer];

		$string = generateEventString($event);
		if ($string != "") {
			appendTextBox($string, $event['timestamp']);
		}

		if (typeof $event["position"] !== "undefined") { // if position exists
			drawomap($event);
		}

		// Do some updates
		if ($event["eventType"] == "CHAMPION_KILL") {
			updateCurrentKDA($event["killerId"], $event["victimId"], $event["assistingParticipantIds"]);	
		} else if ($event["eventType"] == "BUILDING_KILL" && $event["buildingType"] == "TOWER_BUILDING") {
            updateTowerCount(($event["teamId"] == 100 ? 200 : 100));
		} else if ($event["eventType"] == "ELITE_MONSTER_KILL") {
			$killer = $participants[$event["killerId"]];
			if ($event["monsterType"] == "DRAGON") {
				updateDragonCount($killer['teamId']);
			} else if ($event["monsterType"] == "BARON_NASHOR") {
				updateBaronCount($killer["teamId"]);
			}
		} else if ($event["eventType"] == "STAT_UPDATE") {
			updateStats($event['data']);	
		}
		
        globalEventPointer++;
    }
}

function pause_callback() {
	for (i = 0; i < timers.length; i++) {
		timers[i].pause();
	}
}

function resume_callback() {
	for (i = 0; i < timers.length; i++) {
		timers[i].resume();
	}
}

function complete_callback() {
    clearInterval($textboxInterval); // Stop the interval update. We are finished
    $winner = $('<span class="moreinfp"><span class="' + ($winner == 100 ? 'participantblue">Blue' : 'participantred">Red') + " team</span> wins in " + secToMin($duration) + " !</span>");
    appendTextBox($winner, $duration * 1000);
    updateTextBox();
	// update Stats
	var $teamBlue = $teams["100"]["participants"];
	var $teamRed = $teams["200"]["participants"];
	var $concat = new Array();
	for (var i = 0; i <= 10; i++) {
		if (i <= 5) {
			$concat[i] = $teamBlue[i];
		} else {
			$concat[i] = $teamRed[i];
		}
	}
	updateStats($concat);
}
			