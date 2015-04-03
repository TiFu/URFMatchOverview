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
				timeLineWidth: 1050, // width in px
				timeLength: 1800, // time length
				animationLength:10 // animation length
			}, options);

			// Declare variables
			var $currentTime = 0.0;
			var $pause = false;
			var $id = $(this).attr("id");
		
			// Cache elements
			var $container = $(this).show();
			// Timer and button container
			$timerButton = $('<div></div>').css({width:o.timeLineWidth}).prependTo($container);
			// Timer & Buttons div. TODO: add Button divs
			var $timerDiv = $('<div class="timer"></div>').prependTo($timerButton);
			// Add Button div
			var $button = $('<div class="button"><img data-uk-tooltip title="Play/Pasue" class="pauseplay" src="images/play.png" onClick="$(\'' + $id + '\').timeliner.pauseplay()"></div>').prependTo($timerButton);
			// Line div
			var $lineContainer = $('<div class="lineContainer"></div>').css({width:o.timeLineWidth, height:o.timeLineHeight}).appendTo($container);
			var $outerLineDiv = $('<div class="outerLine"></div>').css({width:o.timeLineWidth, height:o.timeLineHeight}).appendTo($lineContainer);
			// innterLineDiv
			var $innerLineDiv  = $('<div class="innerLine">&nbsp;</div>').css({width:0, height:o.timeLineHeight}).appendTo($outerLineDiv);
			// Place Divs inside the innerLineDiv (length / 7)
			var $pointDiv = $('<div class="circle"></div>').css({width:o.timeLineWidth}).appendTo($lineContainer);
			// Create clusters for events
			var cluster = new Array(Math.floor(o.timeLineWidth / 13));
				for(i = 0; i < o.timeLineWidth / 13; i++) {
					cluster[i] = new Array();
				}
			createClusters(o.events);
			// now set the content of the pointDivs with the index inside the clusters
			for (i = 0; i < cluster.length; i++) {
					if (cluster[i].length > 0) {
						var sum = 0;
						var spanText = "";
						for (z = 0; z < cluster[i].length; z++) {
							if (o.showEvent[cluster[i][z]]) {
								sum += o.events[cluster[i][z]];
								spanText += o.hoverText[cluster[i][z]] + '\n';
							}
						}
						if (spanText != "") {
							var avg = sum / cluster[i].length;
							$('<div><span data-uk-tooltip title="' + spanText + '"><img class="icon" src="images/circle.png"></span></div>').css({position:"absolute", left:avg / o.timeLength * o.timeLineWidth}).appendTo($pointDiv);
						}
					}
			}
			
			// start animation for width
			updateTimer();
			animate(o.animationLength);
		
			// Private helper functions
			function animate(time) {
				$innerLineDiv.animate({
						width:o.timeLineWidth,
					},{ duration:time*1000	, step: function(currentWidth) {
							$currentTime = o.timeLength * currentWidth / o.timeLineWidth;
							updateTimer();
						}
					});
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
				$innerLineDiv.pause();
			}
			
			function playLocal() {
				$innerLineDiv.resume();				
			}
			
			function createClusters(arr) {
				border = o.timeLength * 13 / o.timeLineWidth;	
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
				} else {
					$pause = true;
					pauseLocal();
				}
			}
		});
	};
})(jQuery);