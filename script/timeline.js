(function($){
	jQuery.fn.timeliner = function(options){
		// SET OPTIOSN
		return $(this).each(function(){
			var o = $.extend({
				events: [1, 1, 10, 15, 900, 1800], // Event times in seconds. Callback function will notify about events
				showEvent: [true, true, true, true, true], // Should the event be shown in the bar?
				hoverText: ["Start of game", "double", "Second Event", "Third Event", "Last event", "End of game"], // Hover text
				timeLineHeight: 10,
				innerLineHeight: 10,
				timeLineWidth: 1000, // width in px
				timeLength: 1800, // time length
				animationLength:10 // animation length
			}, options);

			// Declare variables
			var $currentTime = 0;
			var $pause = false;
			var $id = $(this).attr("id");
			var $eventPointer = 0;
			
			// Cache elements
			var $container = $(this).show();
			// Timer and button container
			$timerButton = $('<div></div>').css({width:o.timeLineWidth}).prependTo($container);
			// Timer & Buttons div. TODO: add Button divs
			var $timerDiv = $('<div class="timer"></div>').prependTo($timerButton);
			// Add Button div
			var $button = $('<div class="button"><img data-uk-tooltip title="Play/Pasue" class="pauseplay" src="images/play.png" onClick="$(\'' + $id + '\').timeliner.pauseplay()"></div>').prependTo($timerButton);
			// Line div
			var $lineContainer = $('<div class="lineContainer"></div>').css({width:o.timeLineWidth+16, height:o.timeLineHeight}).appendTo($container);
			var $outerLineDiv = $('<div class="outerLine"></div>').css({width:o.timeLineWidth, height:o.timeLineHeight}).appendTo($lineContainer);
			// innterLineDiv
			var $innerLineDiv  = $('<div class="innerLine">&nbsp;</div>').css({width:0, height:o.timeLineHeight}).appendTo($outerLineDiv);
			// Place Divs inside the innerLineDiv (length / 7)
			var $pointDiv = $('<div class="circle"></div>').css({width:o.timeLineWidth}).appendTo($lineContainer);
			// Create clusters for events
			var cluster = new Array(Math.floor(o.timeLineWidth / 13));
				for(i = 0; i < o.timeLineWidth / 13+1; i++) {
					cluster[i] = new Array();
				}
			createClusters(o.events);
			// now set the content of the pointDivs with the index inside the clusters
			for (i = 0; i < cluster.length; i++) {
					if (cluster[i].length > 0) {
						var spanText = "";
						var sum = 0;
						for (z = 0; z < cluster[i].length; z++) {
							if (o.showEvent[cluster[i][z]]) {
								sum += o.events[cluster[i][z]];
								spanText += o.hoverText[cluster[i][z]] + '\n';
							}
						}

						if (spanText != "") {
							var avg = sum / cluster[i].length;
							console.log("average" + avg);	
							console.log("Position: " + avg/o.timeLength * o.timeLineWidth);
							$('<div><span data-uk-tooltip title="' + spanText + '"><img class="icon" src="images/circle.png"></span></div>').css({position:"absolute", left:avg / o.timeLength * o.timeLineWidth}).appendTo($pointDiv);
						}
					}
			}
			
			// setInterval for timerUpdate (intervall min = 30ms)
			var $factor = Math.ceil(30 / (o.animationLength * 1000 / o.timeLength));
			if ($factor == 0) {
				$factor = 1;
			}
			var  $timer = $.timer($(this).attr("id"), function() {incrementTime($factor);}, $factor*o.animationLength*1000 / o.timeLength);
			$timer.start();
			
			// set up the timer
			updateTimer();
			// start animation for width
			animate(o.animationLength);
		
			// Private helper functions
			function animate(time) {
				$innerLineDiv.animate({
						width:o.timeLineWidth,
					},time*1000, 'linear', function() {});
			}
			
			function incrementTime(by) {
				$currentTime += by;
				while (o.events[$eventPointer] < $currentTime) {
					the_event_callback(o.events[$eventPointer]);
					$eventPointer++;
				}
				$currentTime = Math.min($currentTime, o.timeLength);
				updateTimer();
				if ($currentTime >= o.timeLength) {
					$timer.stop();
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
			
			function updateTimer() {
				$timerDiv.html(secToMin($currentTime));
			}

			function pauseLocal() {
				$timer.pause();
				$innerLineDiv.pause();
			}
			
			function playLocal() {
				$timer.resume();
				$innerLineDiv.resume();				
			}
			
			function createClusters(arr) {
				border = o.timeLength * 13 / o.timeLineWidth;	
				console.log("Border: " + border);
				for (i = 0; i < arr.length; i++) {
					clust = Math.floor(arr[i] / border);
					console.log("Element at " + i + " is in " + clust);
					cluster[clust].push(i); // store index of element in that cluster
				}
			}

			// Public functions
			$.fn.timeliner.pauseplay = function() {
				if ($pause) {
					playLocal();
					$pause = false;
				} else {
					$pause = true;
					pauseLocal();
				}
			}
			
			// Callbacks
			function the_event_callback(time) {
				if(typeof event_callback == 'function'){
					event_callback(time);
				}
			}
		});
	};
})(jQuery);