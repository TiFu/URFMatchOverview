(function($){
	jQuery.fn.timeliner = function(options){
		// SET OPTIOSN
		var o = $.extend({
			events: [1, 1, 10, 15, 20], // Event times in seconds. Callback function will notify about events
			showEvent: [true, true, true, true, true], // Should the event be shown in the bar?
			hoverText: ["First event", "double", "Second Event", "Third Event", "Last event"], // Hover text
			timelineHeight: 5,
			innerLineHeight: 5,
			timeLineWidth: 500, // width in px
			timeLength: 30, // time length
			animationLength:15, // animation length
			barIcon: "./images/circle.png", // icon shown on the bar
			iconHeight: 8,
			iconWidth: 8
		});
		// define some variables
		var eventPointer = 0;
		var currentTime = 0;
		var pauseTime = -1;
		var cluster = new Array(Math.floor(o.timeLineWidth / o.iconWidth));
		for(i = 0; i < o.timeLineWidth / o.iconWidth; i++) {
			cluster[i] = new Array();
		}
		
		// set up timeline
		$body = $(this).show();
		$timeline = $('<div class="timeline"></div>').prependTo($body);
		$timeline.css({width:o.timeLineWidth, height:o.timelineHeight});
		$timer = $('<div class="timer"></div>').css({paddingBottom:5, width:o.timeLineWidth}).prependTo($body);
		$innerTimeline = $('<div class="innerTimeline">&nbsp;</div>').appendTo($timeline);
		$innerTimeline.css({width:0, height:o.innerLineHeight});
		$timer.html(secToMin(currentTime));
		$clickableOverlay = $('<div class="overlay"></div>').css({width: o.timeLineWidth, height: o.timelineHeight}).prependTo($body);
		$clickableOverlay.click(function(e) {
			var offset = $(this).offset();
			var x = e.clientX - offset.left;
			var y = e.clientY - offset.top;
			// calculate time
			var secPerPix = o.timeLength / o.timeLineWidth;
			sec = x * secPerPix ;
			$.fn.timeliner.setTime(sec);			
		});
		
		// TODO: create div which gets layered OVER the img divs! and make it clickable :P
		
		// add event nodes to the bar. How? no idea :P
		createClusters(o.events); // create the clusters
		for (i = 0; i < cluster.length; i++) {
			div = $('<div id="img" width="' + o.iconWidth + '" height="' + o.iconHeight + 'px">&nbsp;</div>').appendTo($body);
			if (cluster[i].length > 0) {
				var spanText = "";
					for (z = 0; z < cluster[i].length; z++) {
						if (o.showEvent[cluster[i][z]]) {
							spanText += o.hoverText[cluster[i][z]] + '\n';
						}
					}
				if (spanText != "") {
					$img = $('<span title="' + spanText + '"><img id="circle" src="' + o.barIcon + '" width="' + o.iconWidth + 'px" height="' + o.iconHeight + 'px"></span>').appendTo(div);
				}
			}
		}
		
		// inform every ingame second
		var timer = $.timer(increaseTime, o.animationLength / o.timeLength * 1000, true);
		// auto start
		animate(o.animationLength);
		
		// Private functions
		function triggerEvent() {
				if(typeof listenEvent == 'function'){
						listenEvent(currentTime);
				}
		}

		function increaseTime() {
				currentTime++;
				if (currentTime >= o.events[eventPointer]) {
					// Callback function for event
					triggerEvent();
					eventPointer++;
				}
				$timer.html(secToMin(currentTime));
				if (currentTime >= o.timeLength) {
					timer.stop();
				}
		}		

		function showCluster(arr) {
				if (o.showEvent[arr[0]]) {
					return true;
				}
			return false;
		}
		function animate(time) {
				$innerTimeline.animate({
					width:o.timeLineWidth,
				},time*1000, 'linear', function() {});
		}
		
		function secToMin(sec) {
			min  = Math.floor(sec / 60);
			secs = sec - min*60;
			secs = Math.floor(secs);
			if (secs < 10) {
				secs = 0 + "" + secs;
			}
			return min + ":" + secs;
		}
		
		function createClusters(arr) {
			border = o.timeLength * o.iconWidth / o.timeLineWidth;	
			for (i = 0; i < arr.length; i++) {
				clust = Math.floor(arr[i] / border);
				cluster[clust].push(i); // store index of element in that cluster
			}
		}
	
	// Public functions (pause, resume etc)
		$.fn.timeliner.pause = function() {
			timer.pause();
			$innerTimeline.pause();
		}
		
		$.fn.timeliner.play = function() {
			timer.play();
			if (pauseTime == -1) {
				$innerTimeline.resume();				
			} else {
				animate(pauseTime);
				pauseTime = -1;
			}
		}	
		// gets the ingame time
		$.fn.timeliner.setTime = function(time) {
			$innerTimeline.stop(); // stop animation
			timer.pause();
			newWidth = o.timeLineWidth / o.timeLength * time;
			$innerTimeline.css({width:newWidth});
			pauseTime = (o.timeLength - time) / o.timeLength * o.animationLength;
			// set timer correctly
			currentTime = time; // set current time
			$timer.html(secToMin(time));
		}	
		
}
})(jQuery);