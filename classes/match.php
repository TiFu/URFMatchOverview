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
			$this->cache->setData("events" .implode("|", $eventTypes), $events);
		}
		return $events;
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
		echo "Winning team: " + $id ."<br>";
		return $this->getTeam($id);
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
				if ($participant['participantId'] == $participantId) {
					$participant = $currentParticipant;
				}
			}
			if ($participant == null) {
				return $info;
			}
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
	
		
			// Participant Identity	
			foreach($this->matchArray['participantIdentities'] as $currentIdentity) {
				if ($currentIdentity['participantId'] == $participantId) {
					$info['summonerName'] = $currentIdentity['player']['summonerName'];
					return;
				}
			}
			$this->cache->setData("participant" .$participantId, $info);
		}
		return $info;
	}
}
?> 