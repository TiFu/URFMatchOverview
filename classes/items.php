<?php
abstract class ItemSlot {
	protected $itemId;
	protected $mysqli;
	
	public function __construct($mysqli) {
			$this->itemId = 0;
			$this->mysqli = $mysqli;
	}
	
	public function getItemId() {
		return $this->itemId;
	}
	
	abstract public function setItem($itemId);
	abstract public function destroyItem($itemId);
	
	public function isTrinket($itemId) {
		return strpos($this->mysqli->query("SELECT * FROM " .ITEM_TABLE ." WHERE id = " .$itemId)->fetch_assoc()["name"], "Trinket") !== false;
	}

}
class NormalItemSlot extends ItemSlot {
		private $stock;
				
		public function __construct($mysqli) {
			parent::__construct($mysqli);
			$this->stock = 0;
		}
		
		public function getStock() {
			return $this->stock;
		}
		
		public function setItem($itemId) {
			if ($this->itemId == 0 && !$this->isTrinket($itemId)) {
				$this->itemId = $itemId;
				$this->stock = 1;
				return true;
			} else if ($this->itemId == $itemId) { // if it is this item increase stock if possible
				if ($this->stock < $this->mysqli->query("SELECT * FROM " .ITEM_TABLE . " WHERE id = " .$itemId)->fetch_assoc()["stock"]){
					$this->stock++;	
					return true;
				}				
			}
			return false;
		}
		
		public function destroyItem($itemId) {
			if($this->itemId == $itemId) {
				$this->stock--;
				if ($this->stock == 0) {
					$this->itemId = 0;
				}
				return true;
			}
			return false;
		}
}


class Trinket extends ItemSlot {
	
	public function __construct($mysqli) {
		parent::__construct($mysqli);
	}
	
	public function setItem($itemId) {
			if ($this->itemId != 0 || !$this->isTrinket($itemId)) {
				return false;
			}
			$this->itemId = $itemId;
			return true;
	}
	
	public function destroyItem($itemId) {
		if ($itemId == $this->itemId) {
			$this->itemId = 0;
			return true;
		}
		return false;
	}
}


class Inventory {
	private $normalSlots = array();
	private $trinket;
	private $purchases = array();
	
	public function __construct($mysqli) {
		for ($i = 0; $i < 6; $i++) {
			$this->normalSlots[$i] = new NormalItemSlot($mysqli);
		}
		$this->trinket = new Trinket($mysqli);
	}

	public function purchaseItem($itemId, $destroyedItems) {
		$length = count($this->purchases);
		$this->purchases[$length] = array();
		$this->purchases[$length]["itemId"] = $itemId;
		$this->purchases[$length]["destroyedItems"] = $destroyedItems;
		foreach ($destroyedItems as $destroy) {
			$this->destroyItem($destroy);
		}
		$this->addItem($itemId);
	}
	
	public function sellItem($itemId) {
		$length = count($this->purchases);
		$this->purchases[$length]["itemId"]  = $itemId;
		$this->destroyItem($itemId);
	}
	
	public function undo($itemId) {
		$length = count($this->purchases);
		if ($this->purchases[$length-1]["itemId"] == $itemId) {
			// undo last purchase
			if (array_key_exists("destroyedItems", $this->purchases[$length-1])) { // undo purchase
				$this->destroyItem($itemId);
				foreach ($this->purchases[$length-1]["destroyedItems"] as $restore) {
					$this->addItem($restore);
				}
			} else { // undo sell
				$this->addItem($this->purchases[$length-1]["itemId"]);
			}
			unset($this->purchases[$length-1]); // unset old purchase
		}
	}
	
	public function destroyItem($itemId) {
		if ($this->trinket->destroyItem($itemId)) {
			return true;
		}
		foreach ($this->normalSlots as $slot) {
			if ($slot->destroyItem($itemId)) {
				return true;
			}
		}
		return false;
	}
	
	function getItems() {
			$ids = array();
			for ($i = 0; $i < 6; $i++) {
				$ids[$i] = array();
				$ids[$i]["itemId"] = $this->normalSlots[$i]->getItemId();
				$ids[$i]["stock"] = $this->normalSlots[$i]->getStock();
			}
			$ids[6] = array();
			$ids[6]["itemId"] = $this->trinket->getItemId();
			$ids[6]["stock"] = 1;
			return $ids;
	}
	
	public function addItem($itemId) {
		if ($this->trinket->setItem($itemId)) {
			return true;
		}
				
		// try to add Item everywhere
		foreach ($this->normalSlots as $slot) {
			if ($slot->setItem($itemId)) {
				return true;
			}
		}
		return false;
	}
}
?>