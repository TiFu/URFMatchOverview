			// Holds the current event pointer
			globalEventPointer = 0;
			/**
			 * Gets the last event pointer of a given time frame (keep track with a local eventPointer)
			 */
            function event_callback(eventPointer) {
				// first create string
				while (globalEventPointer <= eventPointer) {
					$string = generateEventString($evts[globalEventPointer]);
                    drawomap([$evts[globalEventPointer]['position']['x'],[$evts[globalEventPointer]['position']['y']]]) ;

        if ($string != "") {
            appendTextBox($string, $evts[globalEventPointer]['timestamp']);

        }
        globalEventPointer++;
    }
}

function complete_callback() {
    clearInterval($textboxInterval); // Stop the interval update. We are finished
    $winner = '<span class="' + ($winner == 100 ? 'participantblue>Blue' : 'participantred">Red') + " team</span> wins in " + secToMin($duration) + " !";
    appendTextBox($winner, $duration * 1000);
    updateTextBox();
}
			