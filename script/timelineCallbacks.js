			// Holds the current event pointer
			globalEventPointer = 0;
			/**
			 * Gets the last event pointer of a given time frame (keep track with a local eventPointer)
			 */
function event_callback(eventPointer) {
			// first create string
	while (globalEventPointer <= eventPointer) {
		$string = generateEventString($evts[globalEventPointer]);
		if ( typeof $evts[globalEventPointer]["position"] !== "undefined") { // if position exists
			drawomap($evts[globalEventPointer]);
		}
		if ($string != "") {
			appendTextBox($string, $evts[globalEventPointer]['timestamp']);
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
    $winner = '<span class="' + ($winner == 100 ? 'participantblue>Blue' : 'participantred">Red') + " team</span> wins in " + secToMin($duration) + " !";
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
			