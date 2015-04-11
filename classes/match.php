<?php
include_once (dirname ( __FILE__ ) . "/cache.php");
include_once (dirname ( __FILE__ ) . "/items.php");
/**
 * Match class with some useful methods to read in the content of a match file WITH timeline.
 *
 * @author Tino Fuhrmann (SkilledGod)
 */
class Match {
	private $matchArray;
	private $cache;
	private $mysqli;
	
	/**
	 * Accepts a json object of a match and a mysqli object.
	 * 
	 * @param $fileContent json string of a match WITH timeline
	 * @param $mysqli  mysqli object
	 */
	function __construct($fileContent, $mysqli) {
		$arr = json_decode ( $fileContent, true );
		$this->matchArray = $arr;
		$this->cache = new Cache ();
		$this->mysqli = $mysqli;
	}
	
	/**
	 * Returns the duration of the match.
	 * 
	 * @return match duration
	 */
	public function getDuration() {
		return $this->matchArray ['matchDuration'];
	}
	
	/**
	 * Returns all Events whose eventType is included in the eventTypes array.
	 * Additionally includes a so called STAT_UPDATE containing data from all participants 
	 * (level, items, totalGold and minionKills).
	 * 
	 * @param $eventTypes array containing values allowed as eventType by the Riot Games match v2.2 api.
	 * @return Array of events
	 */
	public function getEvents($eventTypes) {
		sort ( $eventTypes );
		$events = $this->cache->getData ( "events" . implode ( "|", $eventTypes ) );
		if ($events === false) {
			$normalEventsCounter = 0;
			$events = array ();
			$participantItems = array ();
			for($i = 1; $i <= 10; $i ++) {
				$participantItems [$i] = new Inventory ( $this->mysqli );
			}
			
			foreach ( $this->matchArray ['timeline'] ['frames'] as $frameNumber => $frame ) {
				$statUpdateEvent = array ();
				$statUpdateEvent ["timestamp"] = $frame["timestamp"] > $this->getDuration()*1000 ? $this->getDuration()*1000 : $frame["timestamp"];
				$statUpdateEvent ["eventType"] = "STAT_UPDATE";
				$statUpdateEvent ["data"] = array ();
				foreach ( $frame ["participantFrames"] as $partFrame ) {
					$statUpdateEvent ["data"] [$partFrame ["participantId"]] ["totalGold"] = $partFrame ['totalGold'];
					$statUpdateEvent ["data"] [$partFrame ["participantId"]] ["minionsKilled"] = $partFrame ["minionsKilled"];
					$statUpdateEvent ["data"] [$partFrame ["participantId"]] ["level"] = $partFrame ["level"];
					$statUpdateEvent ["data"] [$partFrame ["participantId"]] ["items"] = array ();
				}
				
				if (array_key_exists ( 'events', $frame )) {
					for($i = 0; $i < count ( $frame ['events'] ); $i ++) {
						$event = $frame ['events'] [$i];
						if (in_array ( $event ['eventType'], $eventTypes )) {
							$normalEventsCounter ++;
							$events [] = $event;
						}
						
						if ($event ['eventType'] == "ITEM_PURCHASED") {
							$i ++; // next event
							$newItem = $event ["itemId"];
							$participantId = $event ['participantId'];
							$frameEvents = $frame ["events"];
							$itemsDestroyed = array ();
							while ( array_key_exists ( $i, $frameEvents ) && $frameEvents [$i] ["eventType"] == "ITEM_DESTROYED" && $frameEvents [$i] ["participantId"] == $participantId ) {
								$itemsDestroyed [] = $frameEvents [$i] ["itemId"];
								$i ++;
							}
							$i --; // previous event (because at end of loop $i++)
							$participantItems [$event ["participantId"]]->purchaseItem ( $newItem, $itemsDestroyed ); // purchase Item
						} else if ($event ["eventType"] == "ITEM_DESTROYED" && $event ["participantId"] != 0) { // whatever participantId 0 means in this context... (with items not even existing)
							$participantItems [$event ["participantId"]]->destroyItem ( $event ["itemId"] );
						} else if ($event ["eventType"] == "ITEM_SOLD" && $event ["participantId"] != 0) {
							$participantItems [$event ["participantId"]]->sellItem ( $event ["itemId"] );
						} else if ($event ["eventType"] == "ITEM_UNDO" && $event ["participantId"] != 0) {
							// Undo purchase
							if ($event ["itemAfter"] == 0 && $event ["itemBefore"] != 0) {
								$participantItems [$event ["participantId"]]->undo ( $event ["itemBefore"] );
							} else if ($event ["itemAfter"] != 0 && $event ["itemBefore"] == 0) {
								$participantItems [$event ["participantId"]]->undo ( $event ["itemAfter"] );
							}
						}
					}
				}
				// Add items
				foreach ( $statUpdateEvent ["data"] as $participantId => $partValue ) {
					$statUpdateEvent ["data"] [$participantId] ["items"] = $participantItems [$participantId]->getItems ();
				}
				// add stat update
				$events [] = $statUpdateEvent;
			}
			usort ( $events, array (
					"Match",
					"compareEvents" 
			) );
			$this->cache->setData ( "events" . implode ( "|", $eventTypes ), $events );
		}
		return $events;
	}
	
	/**
	 * Compares to events.
	 * $a < $b <=> $a["timestamp"] < $b["timestamp"]
	 * Analogue relation for > and =
	 */
	private static function compareEvents($a, $b) {
		if ($a ['timestamp'] == $b ['timestamp']) {
			return 0;
		}
		
		return ($a ['timestamp'] < $b ['timestamp']) ? - 1 : 1;
	}

	/**
	 * Returns the region of a match. (e. g. euw for EU West)
	 * @return region
	 */
	public function getRegion() {
		return $this->matchArray ['region'];
	}
	
	/**
	 * Returns the data for the team specified by $teamId.
	 * 
	 * @param $teamId either 100 (blue side) or 200 (red side)
	 * @return team data or an empty array if the team is not present
	 */
	public function getTeam($teamId) {
		$info = $this->cache->getData ( "team" . $teamId );
		if ($info === false) {
			$team = null;
			$info = array ();
			foreach ( $this->matchArray['teams'] as $team ) {
				if ($team ['teamId'] == $teamId) {
					$info ['teamId'] = $team ['teamId'];
					$info ['dragonKills'] = $team ['dragonKills'];
					$info ['baronKills'] = $team ['baronKills'];
					$info ['winner'] = $team ['winner'];
				}
			}
			$info["participants"] = array(); // map from participantId -> stats
			foreach ($this->matchArray["participants"] as $participant) {
				if ($participant["teamId"] == $teamId) {
					$stats = $participant["stats"];
					$info["participants"][$participant["participantId"]] = array();
					$part = array();
					$part["assists"] = $stats["assists"];
					$part["kills"] = $stats["kills"];
					$part["deaths"] = $stats["deaths"];
					$part["totalGold"] = $stats["goldEarned"];
					$part["items"] = array();
					$part["minionsKilled"] = $stats["minionsKilled"];
					for ($i = 0; $i < 7; $i++) {
						$part["items"][$i]["itemId"] = $stats["item" .$i];
						$part["items"][$i]["stock"] = 1;
					}
					$info["participants"][$participant["participantId"]] = $part;
				}
			}
			$this->cache->setData ( "team" . $teamId, $info );
		}
		return $info;
	}
	
	/**
	 * Returns the winning team id.
	 * @return winning team id. 100 for blue side. 200 for red side
	 */
	public function getWinner() {
		$id = 0;
		foreach ( $this->matchArray ['teams'] as $team ) {
			if ($team ['winner']) {
				$id = $team ['teamId'];
			}
		}
		return $this->getTeam ( $id );
	}
	
	/**
	 * Returns an map[participantId,data array]. 
	 * 
	 * @return map from participantId to the data associated with that participant.
	 */
	public function getParticipants() {
		$info = $this->cache->getData ( "participants" );
		if ($info === false) {
			$info = array ();
			foreach ( $this->matchArray ['participantIdentities'] as $identity ) {
				$inf = $this->getParticipant ( $identity ['participantId'] );
				$info [] = $inf;
			}
			$this->cache->setData ( "participants", $info );
		}
		return $info;
	}
	
	/**
	 * Returns the hover text for an event (@see Match::getEvents).
	 * 
	 * @param $event from Match::getEvents
	 * @return Hover text for $event event.
	 */
	function createHoverText($event) {
		if ($event ["eventType"] == "BUILDING_KILL") {
			if ($event ["buildingType"] == "TOWER_BUILDING") {
				// red and blue switched because teamid states who the building belonged to -> the other team destroyed it
				$string = "<span class=participant" . ($event ["teamId"] == 100 ? "red" : "blue") . ">" . transformTypeToText ( $event ["laneType"] ) . " " . transformTypeToText ( $event ['towerType'] ) . "</span>";
			} else if ($event ["buildingType"] == "INHIBITOR_BUILDING") {
				$string = "<span class=participant" . ($event ["teamId"] == 100 ? "red" : "blue") . ">" . transformTypeToText ( $event ["laneType"] ) . " inhibitor</span>";
			}
		} else if ($event ["eventType"] == "ELITE_MONSTER_KILL") {
			$string = "<span class=participant" . ($this->getParticipant ( $event ["killerId"] )["teamId"] == 100 ? "blue" : "red") . ">";
			if ($event ["monsterType"] == "BARON_NASHOR") {
				$string .= "Baron Nashor";
			} else if ($event ["monsterType"] == "DRAGON") {
				$string .= "Dragon";
			}
			$string .= "</span>";
		} else {
			$string = "";
		}
		return $string;
	}
	
	/**
	 * Returns an participant array with comprimated stats. (Flat map of the data).
	 * 
	 * @param $participantId id between 1 and 10 (inclusive).
	 * @return participant data for the participant with id $participantId. 
	 */
	public function getParticipant($participantId) {
		$info = $this->cache->getData ( "participant" . $participantId );
		
		if ($info === false) {
			$info = array ();
			$participant = null;
			foreach ( $this->matchArray ['participants'] as $currentParticipant ) {
				if ($currentParticipant ['participantId'] == $participantId) {
					$participant = $currentParticipant;
				}
			}
			if ($participant == null) {
				return $info;
			}
			$info ['currentKills'] = 0;
			$info ['currentDeaths'] = 0;
			$info ['currentAssists'] = 0;
			// Participant
			$info ['championId'] = $participant ['championId'];
			$info ['participantId'] = $participantId;
			$info ['spell1'] = $participant ['spell1Id'];
			$info ['spell2'] = $participant ['spell2Id'];
			$info ['teamId'] = $participant ['teamId'];
			// Participant stats
			$info ['kills'] = $participant ['stats'] ['kills'];
			$info ['deaths'] = $participant ['stats'] ['deaths'];
			$info ['assists'] = $participant ['stats'] ['assists'];
			$info ['minionsKilled'] = $participant ['stats'] ['minionsKilled'];
			$info ['champLevel'] = $participant ['stats'] ['champLevel'];
			$info ['goldEarned'] = $participant ['stats'] ['goldEarned'];
			for($i = 0; $i <= 6; $i ++) {
				$info ['item' . $i] = $participant ['stats'] ['item' . $i];
			}
			$info ['magicDamageDealtToChampions'] = $participant ['stats'] ['magicDamageDealtToChampions'];
			$info ['magicDamageTaken'] = $participant ['stats'] ['magicDamageTaken'];
			$info ['physicalDamageDealtToChampions'] = $participant ['stats'] ['physicalDamageDealtToChampions'];
			$info ['physicalDamageTaken'] = $participant ['stats'] ['physicalDamageTaken'];
			$info ['sightWardsBoughtInGame'] = $participant ['stats'] ['sightWardsBoughtInGame'];
			$info ['totalDamageDealtToChampions'] = $participant ['stats'] ['totalDamageDealtToChampions'];
			$info ['totalDamageTaken'] = $participant ['stats'] ['totalDamageTaken'];
			$info ['totalHeal'] = $participant ['stats'] ['totalHeal'];
			$info ['totalTimeCrowdControlDealt'] = $participant ['stats'] ['totalTimeCrowdControlDealt'];
			$info ['trueDamageDealtToChampions'] = $participant ['stats'] ['trueDamageDealtToChampions'];
			$info ['trueDamageTaken'] = $participant ['stats'] ['trueDamageTaken'];
			$info ['visionWardsBoughtInGame'] = $participant ['stats'] ['visionWardsBoughtInGame'];
			$info ['wardsKilled'] = $participant ['stats'] ['wardsKilled'];
			$info ['wardsPlaced'] = $participant ['stats'] ['wardsPlaced'];
			// Participant Timeline
			$info ['lane'] = $participant ['timeline'] ['lane'];
			
			$this->cache->setData ( "participant" . $participantId, $info );
		}
		return $info;
	}
}
?> 