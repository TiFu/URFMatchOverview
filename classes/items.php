<?php
/**
 * Abstract ItemSlot.
 */
abstract class ItemSlot {
	protected $itemId;
	protected $mysqli;
	
	/**
	 * Initializes the item slot with itemId 0 (== no item) and the mysqli objbect.
	 */
	public function __construct($mysqli) {
			$this->itemId = 0;
			$this->mysqli = $mysqli;
	}
	
	/**
	 * Returns the item id.
	 * @returns itemId
	 */
	public function getItemId() {
		return $this->itemId;
	}
	
	/**
	 * sets the Item for this slot if possible.
	 * 
	 * @param $itemId itemId
	 * @return true if the item was set, false otherwise
	 */
	abstract public function setItem($itemId);
	
	/**
	 * Destroys the current item if $itemId matche $this->itemId. 
	 * 
	 * @param $itemId itemId
	 * @return true on success, false otherwise.
	 */
	abstract public function destroyItem($itemId);
	
	/**
	 * Checks if a item is a trinket.
	 * @param $itemId item to check
	 * @return true if and only if the item is a trinket (name contains trinket).
	 */
	public function isTrinket($itemId) {
		return strpos($this->mysqli->query("SELECT * FROM " .ITEM_TABLE ." WHERE id = " .$itemId)->fetch_assoc()["name"], "Trinket") !== false;
	}

}

/**
 * A normal item slot containing items like B. F. Sword (no trinkets!).
 */
class NormalItemSlot extends ItemSlot {
		private $stock;
				
		/**
		 * Initializes the slot with stock 0 (number of stacks of this item in this slot).
		 */
		public function __construct($mysqli) {
			parent::__construct($mysqli);
			$this->stock = 0;
		}
		
		/**
		 * Returns the item stock for this slot.
		 * 
		 * @return stock.
		 */
		public function getStock() {
			return $this->stock;
		}
		
		/**
		 * Increases the stock if $itemId equals $this->itemId OR 
		 * sets stock to 1 if and only if $this->itemId equals 0.
		 * 
		 * @param $itemId item to place
		 * @return true if the item was set, false otherwise
		 */
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
		
		/**
		 * Decreases the stock by 1 if this slot holds this item. 
		 * Sets the itemId to 0 if the stock is 0.
		 * 
		 * @param $itemId itemId
		 * @return true if the item stock was decreased. false otherwise
		 */
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


/**
 * Trinket Item Slot. Can only contain trinkets matched by Itemslot::isTrinket method.
 */
class Trinket extends ItemSlot {
	
	public function __construct($mysqli) {
		parent::__construct($mysqli);
	}

	/**
	 * Sets this->itemId to $itemId if $itemid is a trinket AND this->itemId == 0.
	 * @param $itemId trinket item id
	 * @return true if the itemId was set
	 */
	public function setItem($itemId) {
			if ($this->itemId != 0 || !$this->isTrinket($itemId)) {
				return false;
			}
			$this->itemId = $itemId;
			return true;
	}
	
	/**
	 * Destroys this trinket if and only if $this->itemId == $itemId.
	 * @param $itemId Trinket to destroy
	 * @return true if the itemId was destroyed. false otherwise
	 */
	public function destroyItem($itemId) {
		if ($itemId == $this->itemId) {
			$this->itemId = 0;
			return true;
		}
		return false;
	}
}

/**
 * Models a League of Legends Champion Inventory. Containing 6 Normal Item Slots and 1 Trinket Slot.
 */
class Inventory {
	private $normalSlots = array();
	private $trinket;
	private $purchases = array();
	
	/**
	 * Initializes the item slots and passes the mysqli object to the slots.
	 * 
	 * @param $mysqli mysqli object
	 */
	public function __construct($mysqli) {
		for ($i = 0; $i < 6; $i++) {
			$this->normalSlots[$i] = new NormalItemSlot($mysqli);
		}
		$this->trinket = new Trinket($mysqli);
	}

	/**
	 * Adds the bought item and destroyed Items to the history so it can be undone. Destroys all items in $destroyedItems 
	 * and adds $itemId to the first empty slot.
	 * 
	 * @param $itemId purchased item
	 * @param $destroyedItems array of destroyed Items. 
	 */
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
	
	/**
	 * Sells $itemId and adds this to the history so it can be undone.
	 * @param $itemId sold itemid
	 */
	public function sellItem($itemId) {
		$length = count($this->purchases);
		$this->purchases[$length]["itemId"]  = $itemId;
		$this->destroyItem($itemId);
	}
	
	/**
	 * Undos the purchase with this $itemId. Only does anything if $itemId matches the 
	 * last entry in the sell/purchase history.
	 * 
	 * @param $itemId itemId to be undone.
	 */
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

	/**
	 * Destroys the item with $itemId in the first slot containing this item.
	 * 
	 * @param $itemId itemId
	 */
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
	
	/**
	 * Returns an array of items (range: 0-6 with 6 being the trinket slot). Each Item is an array.
	 * The items with index < 6 have itemId and stock as index. The trinket only has itemId as index.
	 * 
	 * @returns array of items.
	 */
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
	
	/**
	 * Adds an Item to this inventory (first fitting spot).
	 * 
	 * @param $itemId itemId
	 */
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