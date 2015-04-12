			function makeFirstLetterCapital(stringVal) {
				return stringVal.charAt(0).toUpperCase() + stringVal.slice(1);
			}
			function transform(stringVal) {
				output = "";
				var arr = stringVal.split("_");
				for (var i  = 0; i < arr.length; i++) {
					output += makeFirstLetterCapital(arr[i].toLowerCase()) + ' ';
				}
				return output.substring(0, output.length-1);
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

			function setUpTipsy ($img) {
					$img.tipsy({gravity:'s', html:true, opacity:1});
			}