(function($){
	jQuery.fn.timeliner = function(options){
		// SET OPTIOSN
		return $(this).each(function(){
			var o = $.extend({
				events: [1, 1, 10, 15, 1700], // Event times in seconds. Callback function will notify about events
				showEvent: [true, true, true, true, true], // Should the event be shown in the bar?
				hoverText: ["First event", "double", "Second Event", "Third Event", "Last event"], // Hover text
				timeLineHeight: 10,
				innerLineHeight: 10,
				timeLineWidth: "97%", // width in px
				timeLength: 3600, // time length
				animationLength:60, // animation length
				updateTime: 250 // number of ms between to event calls (don't set it too low -> performance issues!!!)
			}, options);

			// Declare variables
			var $currentTime = 0.0;
			var $pause = false;
			var $id = $(this).attr("id");
			var $eventPointer = 0;
			// Cache elements
			var $wrapper = $(this).show();
			var $container = $('<div style="margin:0px auto"></div>').css({width:o.timeLineWidth}).appendTo($wrapper);
			var $timerDiv = $('<div class="timer"></div>').css({width:o.timeLineWidth}).appendTo($container);
			var $outerLineDiv = $('<div class="timelength"></div>').css({width:o.timeLineWidth}).appendTo($container);
			var $innerLineDiv = $('<div class="loadingtime"></div>').appendTo($outerLineDiv);
			var $width = $outerLineDiv.width();
			// one timer every 5 minutes.
			var end = 0;
			// Space to pause listener
			$(document).keypress(function(e) {
				   if(e.which === 32) {
						e.preventDefault();
						$($id).timeliner.pauseplay();
						return false;  
					}
			});
			$('<img alt="Pause/Play (spacebar)" src="images/play.png" data-uk-tooltip title="Play/Stop (Spacebar)" class="playButton" onClick="$(' + $id + ').timeliner.pauseplay()">').appendTo($timerDiv);
			for (var i = 300; i <= o.timeLength; i += 300) {
				i = Math.min(o.timeLength, i);
				$('<span class="time">'  + secToMin(i) + '</span>').css({position:"absolute", left: Math.max(i / o.timeLength * $width - 5, 0)}).appendTo($timerDiv);
				end = i;
			}
			if (end !== o.timeLength) {
				$('<span class="time">'  + secToMin(o.timeLength) + '</span>').css({position:"absolute", left:$width}).appendTo($timerDiv);				
			}

			// Create clusters for events
			var cluster = new Array(Math.floor($width / 10));
				for(i = 0; i < $width / 10+1; i++) {
					cluster[i] = new Array();
				}

			createClusters(o.events);
			// now set the content of the pointDivs with the index inside the clusters
			for (i = 0; i < cluster.length; i++) {
					if (cluster[i].length > 0) {
						var spanText = "";
						var elements = 0;
						for (z = 0; z < cluster[i].length; z++) {
							if (o.showEvent[cluster[i][z]]) {
								spanText += o.hoverText[cluster[i][z]] + '<br>';
								elements++;
							}
						}
						if (elements > 0) {
							$('<div class="eventsgame" data-uk-tooltip title="' + spanText + '"></div></div>').css({position:"absolute", left:(i*10)}).appendTo($outerLineDiv);
						}
					}
			}

			animate(o.animationLength);
			var $lastCall = 0;
			// Private helper functions
			function animate(time) {
				$innerLineDiv.animate({
						width:$width,
					},{ duration:time*1000, step: function(currentWidth) {
							
							var newTime = new Date().getTime();
							if (newTime - $lastCall < o.updateTime) {
								return;
							}
							var incr = false;
							while (o.events[$eventPointer] < currentWidth  / $width * o.timeLength) {
								incr = true;
								$eventPointer++;
							}
							if (incr) {
								$lastCall = newTime;
								the_event_callback($eventPointer-1);
							}
					}, complete: function (){
						the_complete_callback();
					}});
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

			function pauseLocal() {
				$innerLineDiv.pause();
			}
			
			function playLocal() {
				$innerLineDiv.resume();				
			}
			
			function createClusters(arr) {
				border = o.timeLength * 10 / $width;	
				for (i = 0; i < arr.length; i++) {
					clust = Math.floor(arr[i] / border);
					cluster[clust].push(i); // store index of element in that cluster
				}
			}

			// Public functions
			$.fn.timeliner.pauseplay = function() {
				if ($pause) {
					playLocal();
					$pause = false;
					the_resume_callback();
				} else {
					$pause = true;
					pauseLocal();
					the_pause_callback();
				}
			}
			
			// Callback
			function the_pause_callback() {
				if (typeof pause_callback === 'function') {
					pause_callback();
				}
			}
			
			function the_resume_callback() {
				if (typeof resume_callback === 'function') {
					resume_callback();
				}
			}
			function the_event_callback(lastEvent) {
				if(typeof event_callback === 'function'){
					// call with current time
					event_callback(lastEvent);
				}
			}
			
			function the_complete_callback() {
				if(typeof complete_callback === 'function'){
					// call with current time
					complete_callback();
				}				
			}
		});
	};
})(jQuery);