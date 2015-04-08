<?php
/**
 * A small array cache for match.php so the values don't have to be recalculated everytime.
 */
class Cache {
	private $cache = array();
	
	/**
	 * Sets the Data with tag and value
	 * @param tag cache tag
	 * @param value value for this tag
	 */
	public function setData($tag, $value) {
	$this->cache[$tag] = $value;
	}
	
	/**
	 * Returns the saved data for this tag.
	 * 
	 * @param $tag tag
	 * @return value passed by Cache::setData previously.
	 */
	public function getData($tag) {
		 if (array_key_exists($tag, $this->cache)) {
			 return $this->cache[$tag];
		 } else {
			 return false;
		 }
	}
}
?>