function Turret(lane, type, teamId) {
	this.lane = lane;
	this.type = type;
	this.teamId = teamId;
	
	var picPath = "images/gamepic/turret_";
	
	var getDesc = function() {
		return transform(lane) + " " + transform(type);
	}

	this.getTextboxImage = function() {
		var $img = $(getImage() + ' class="imageTextbox textbox_border_' + this.teamId + '">');
			setUpTipsy($img);
			return $img;
	}

	this.getMapImage = function() {
		return getImage() + ' class="imageMap map_border_' + this.teamId + '">';
	}
	
	var getImage = function() {
		return '<img src="' + picPath + teamId + ".png" + '" alt="'+ getDesc() + '" title="<span class=span_' + teamId + '>' + getDesc() + '</span>"';
	}

}

function Inhibitor(lane, teamId) {
	this.lane = lane;
	this.teamId = teamId;
	var picPath = "images/gamepic/inhibitor_";
	
	var getDesc = function() {
		return transform(lane) + " Inhibitor";
	}
	
	this.getTextboxImage = function() {
		var $img = $(getImage() + ' class="imageTextbox textbox_border_' + this.teamId + '">');
		setUpTipsy($img);
		return $img;
	}
	
	this.getMapImage = function() {
		return getImage() + ' class="imageMap map_border_' + this.teamId + '">';
	}
	
	var getImage = function() {
		return '<img src="' + picPath + teamId +'.png" alt="' + getDesc() + '" title="<span class=span_' + teamId +'>' + getDesc() + '</span>"';
	}
}

function Champion(champName, teamId) {
	this.champName = champName;
	this.teamId = teamId;
	var picPath = "images/champion/";

	this.getTextboxImage = function() {
			var $img = $(getImage() + ' class="imageTextbox textbox_border_' + this.teamId + '">');
			setUpTipsy($img);
			return $img;
	}

	this.getMapImage = function() {
		return getImage() + ' class="imageMap map_border_' + this.teamId + '">';
	}

	var getImage = function() {
		if (champName == "Minion") {
			var champNameReal = champName + "_" + teamId;
		} else {
			var champNameReal = champName;
		}
		$img =  '<img src="' + picPath + champNameReal + '.png" alt="' + champName + '" title="<span class=span_' + teamId + '>' + champName + '</span>"';
		return $img;
	}
}

function Monster(name) {
	this.name = name;
	var picPath ="images/gamepic/";
	
	var getDesc = function() {
		return transform(name);
	}

	this.getTextboxImage = function() {
		var $img = $(getImage() + ' class="imageTextbox" style="border:1px solid transparent">');
		setUpTipsy($img);
		return $img;		
	}
	
	this.getMapImage = function() {
		return getImage() + ' class="imageMap" style="border:2px solid transparent">';
	}
	
	var getImage = function() {
		return '<img src="' + picPath + name + '.png" alt="' + getDesc() + '" title="' + getDesc() + '"';
	}
}