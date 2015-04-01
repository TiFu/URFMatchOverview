(function($){
	jQuery.fn.timeliner = function(options){
		// SET OPTIOSN
		var o = $.extend({
			events: [1, 10, 15], // Event times in seconds. Callback function will notify about events
			timelineHeight: 5,
			innerLineHeight: 5,
			timeLineWidth: 500, // width in px
			timeLength: 30, // time length
			animationLength:15 // animation length
		});
		// define some variables
		var eventPointer = 0;
		var currentTime = 0;
		// set up timeline
		$body = $(this).show();
		$timeline = $('<div class="timeline"></div>').prependTo($body);
		$timeline.css({width:o.timeLineWidth, height:o.timelineHeight});
		//$timer = $('<div class="timer"></div>').prependTo($timeline);
		$innerTimeline = $('<div class="innerTimeline">&nbsp;</div>').prependTo($timeline);
		$innerTimeline.css({width:0, height:o.innerLineHeight});
	
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
				
				if (currentTime >= o.timeLength) {
					timer.stop();
				}
		}		

		function animate(time) {
				$innerTimeline.animate({
					width:o.timeLineWidth,
				},time*1000, 'linear', function() {});
		}
		
		function secToMin(sec) {
			min  = Math.floor(sec / 60);
			return min + ":" + (sec - min*60);
		}
	
	// Public functions (pause, resume etc)
		$.fn.timeliner.pause = function() {
			timer.pause();
			$innerTimeline.pause();
		}
		
		$.fn.timeliner.play = function() {
			timer.play();
			$innerTimeline.resume();
		}	
		// gets the ingame time
		$.fn.timeliner.setTime = function(time) {
			$innerTimeline.stop(); // stop animation
			timer.pause();
			newWidth = o.timeLineWidth / o.timeLength * time;
			$innerTimeline.css({width:newWidth});
			animate((o.timeLength - time) / o.timeLength * o.animationLength);

			// set timer correctly
			currentTime = time; // set current time
			timer.play();
		}	
		
}
})(jQuery);