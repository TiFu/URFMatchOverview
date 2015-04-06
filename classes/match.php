<?php
include_once(dirname(__FILE__) ."/cache.php");
class Match  {
	private $matchArray;
	private $cache;
	
	function __construct($fileContent) {
		$arr = json_decode($fileContent, true);
		$this->matchArray = $arr;
		$this->cache = new Cache();
	}
	
	public function getDuration() {
		return $this->matchArray['matchDuration'];
	}
	/**
	 *	Returns the events 
	 *
	 */
	public function getEvents($eventTypes) {
		sort($eventTypes);
		$events = $this->cache->getData("events" .implode("|", $eventTypes));
			
		if ($events === false) {
			$events = array();
			foreach($this->matchArray['timeline']['frames'] as $frame) {
				if (array_key_exists('events', $frame)) {
					foreach ($frame['events'] as $event) {
						if (in_array($event['eventType'], $eventTypes)) {
							$events[] = $event;
						}
					}
				}
			}
			usort($events, array("Match", "compareEvents"));
			$this->cache->setData("events" .implode("|", $eventTypes), $events);
		}
		return $events;
	}
	
	private static function compareEvents($a, $b) {
		if ($a['timestamp'] == $b['timestamp']) {
			return 0;
		}
		
		return ($a['timestamp'] < $b['timestamp']) ? -1 : 1;
	}
	
	public function getRegion() {
		return $this->matchArray['region'];
	}

	public function getTeam($teamId) {
		$info = $this->cache->getData("team" .$teamId);
		if ($info === false) {
			$team = null;
			$info = array();
			foreach ($this->matchArray['teams'] as $team) {
				if ($team['teamId'] == $teamId) {
					$info['teamId'] = $team['teamId'];
					$info['dragonKills'] = $team['dragonKills'];
					$info['baronKills'] = $team['baronKills'];
					$info['winner'] = $team['winner'];
				}
			}	
			$this->cache->setData("team" .$teamId, $info);
		}
		
		return $info;
	}
	
	public function getWinner() {
		$id = 0;
		foreach ($this->matchArray['teams'] as $team) {
			if ($team['winner']) {
				$id = $team['teamId'];
			}
		}
		return $this->getTeam($id);
	}
	
	public function getParticipants() {
		$info = $this->cache->getData("participants");
		if ($info === false) {
			$info = array();
			foreach ($this->matchArray['participantIdentities'] as $identity) {
				$inf = $this->getParticipant($identity['participantId']);
				$info[] = $inf;
			}
			$this->cache->setData("participants", $info);
		}
		return $info;
	}
	
	function createHoverText($event) {
	if ($event["eventType"] == "BUILDING_KILL") {
		if ($event["buildingType"] == "TOWER_BUILDING") {
			$string = "<span class=participant" .($event["teamId"] == 100 ? "blue" :"red") .">" .transformTypeToText($event["laneType"]) ." " .transformTypeToText($event['towerType']) ."</span>";
		} else if ($event["buildingType"] == "INHIBITOR_BUILDING") {
		$string  = "<span class=participant" .($event["teamId"] == 100 ? "blue" :"red") .">" .transformTypeToText($event["laneType"]) ." inhibitor</span>";
		}
	} else if ($event["eventType"] == "ELITE_MONSTER_KILL") {
			$string = "<span class=participant" .($this->getParticipant($event["killerId"])["teamId"] == 100 ? "blue" : "red") .">";
			if ($event["monsterType"] == "BARON_NASHOR") {
				$string .= "Baron Nashor";
			} else if ($event["monsterType"] == "DRAGON") {
				$string .= "Dragon";
			}
			$string .= "</span>";
	} else {
		$string = ""; 
	}
	return $string;
}

	/**
	 * Returns an participant array with comprimated stats.
	 *
	 */
	public function getParticipant($participantId) {
		$info = $this->cache->getData("participant" .$participantId);
		
		if ($info === false) {
			$info = array();
			$participant = null;
			foreach ($this->matchArray['participants'] as $currentParticipant) {
				if ($currentParticipant['participantId'] == $participantId) {
					$participant = $currentParticipant;
				}
			}
			if ($participant == null) {
				return $info;
			}
			$info['currentKills'] = 0;
			$info['currentDeaths'] = 0;
			$info['currentAssists'] = 0;
			// Participant
			$info['championId'] = $participant['championId'];
			$info['participantId'] = $participantId;
			$info['spell1'] = $participant['spell1Id'];
			$info['spell2'] = $participant['spell2Id'];
			$info['teamId'] = $participant['teamId'];
			// Participant stats
			$info['kills'] = $participant['stats']['kills'];
			$info['deaths'] = $participant['stats']['deaths'];
			$info['assists'] = $participant['stats']['assists'];
			$info['minionsKilled'] = $participant['stats']['minionsKilled'];
			$info['champLevel'] = $participant['stats']['champLevel'];
			$info['goldEarned'] = $participant['stats']['goldEarned'];
			for ($i = 0; $i <= 6; $i++) {
				$info['item' .$i] = $participant['stats']['item' .$i];
			}
			$info['magicDamageDealtToChampions'] = $participant['stats']['magicDamageDealtToChampions'];
			$info['magicDamageTaken'] = $participant['stats']['magicDamageTaken'];
			$info['physicalDamageDealtToChampions'] = $participant['stats']['physicalDamageDealtToChampions'];
			$info['physicalDamageTaken'] = $participant['stats']['physicalDamageTaken'];
			$info['sightWardsBoughtInGame'] = $participant['stats']['sightWardsBoughtInGame'];
			$info['totalDamageDealtToChampions'] = $participant['stats']['totalDamageDealtToChampions'];
			$info['totalDamageTaken'] = $participant['stats']['totalDamageTaken'];
			$info['totalHeal'] = $participant['stats']['totalHeal'];
			$info['totalTimeCrowdControlDealt'] = $participant['stats']['totalTimeCrowdControlDealt'];
			$info['trueDamageDealtToChampions'] = $participant['stats']['trueDamageDealtToChampions'];
			$info['trueDamageTaken'] = $participant['stats']['trueDamageTaken'];
			$info['visionWardsBoughtInGame'] = $participant['stats']['visionWardsBoughtInGame'];
			$info['wardsKilled'] = $participant['stats']['wardsKilled'];
			$info['wardsPlaced'] = $participant['stats']['wardsPlaced'];
			// Participant Timeline
			$info['lane'] = $participant['timeline']['lane'];
	
			$this->cache->setData("participant" .$participantId, $info);
		}
		return $info;
	}
}
?> 