			function makeFirstLetterCapital(stringVal) {
				return stringVal.charAt(0).toUpperCase() + stringVal.slice(1);
			}
			function transform(stringVal) {
				output = "";
				var arr = stringVal.split("_");
				for (var i  = 0; i < arr.length; i++) {
					output += arr[i].toLowerCase() + ' ';
				}
				return output.substring(0, output.length-1);
			}