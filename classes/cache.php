<?php
class Cache {
	private $cache = array();
	
	public function setData($tag, $value) {
	$this->cache[$tag] = $value;
	}
	
	public function getData($tag) {
		 if (array_key_exists($tag, $this->cache)) {
			 return $cache[$tag];
		 } else {
			 return false;
		 }
	}
}
?>