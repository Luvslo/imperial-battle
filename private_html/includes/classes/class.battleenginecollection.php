<?
class Fleet {
	var $target_id;
	var $shipCollection;
	var $preRemoveCollection;

	function Fleet($target_id) {
		$this->target_id = $target_id;
		$this->shipCollection = array();
		$this->preRemoveCollection = array();
	}

	function addShips($unique_id, $player_id, $fleet_id, $ship_id, $amount, $armor, $firepower, $prim, $sec) {
		$ships = new Ships($unique_id, $player_id, $fleet_id, $ship_id, $armor, $firepower, $prim, $sec);
		$ships->addShip($amount);
		array_push($this->shipCollection, $ships);
	}
	function getTotalShipAmount($ship_id) {
		$_shipAmount = 0;
		for ($i = 0; $i < count($this->shipCollection); $i++) {
			if ($this->shipCollection[$i]->getId() == $ship_id) {
				$_shipAmount += $this->shipCollection[$i]->getAmount();
			}
		}
		return $_shipAmount;
	}
	function getTotalShipOldAmount($ship_id) {
		$_shipAmount = 0;
		for ($i = 0; $i < count($this->shipCollection); $i++) {
			if ($this->shipCollection[$i]->getId() == $ship_id) {
				if ($this->shipCollection[$i]->getLostShips() == 0) {
					$_shipAmount += $this->shipCollection[$i]->getAmount();
				} else {
					$_shipAmount += $this->shipCollection[$i]->getOldAmount();
				}
			}
		}
		return $_shipAmount;
	}
	function getTotalShipLosses($ship_id) {
		$_shipLosses = 0;
		for ($i = 0; $i < count($this->shipCollection); $i++) {
			if ($this->shipCollection[$i]->getId() == $ship_id) {
				$_shipLosses += $this->shipCollection[$i]->getLostShips();
			}
		}
		return $_shipLosses;
	}
	function getShipCollection($ship_id) {
		$_collection = array();
		for ($i = 0; $i < count($this->shipCollection); $i++) {
			if ($this->shipCollection[$i]->getId() == $ship_id) {
				array_push($_collection, $this->shipCollection[$i]);
			}
		}
		return $_collection;
	}

	/* ONLY USED FOR BATTLE REPORTS! */
	function getPlayerShipCollection($ship_id, $player_id) {
		$_tmp = array();

		for ($i = 0; $i < count($this->shipCollection); $i++) {
			if (($this->shipCollection[$i]->getId() == $ship_id) && ($this->shipCollection[$i]->getPlayerId() == $player_id)) {
				array_push($_tmp,$this->shipCollection[$i]);
			}
		}
		if (count($_tmp) > 0) {
			$_nShips = new Ships(0, $_tmp[0]->getPlayerId(), 0, $_tmp[0]->getId(), 0, 0, 0, 0);

			$amount = 0;
			$old_amount = 0;
			$lost_ships = 0;
			for ($i = 0; $i < count($_tmp); $i++) {
				$amount += $_tmp[$i]->getAmount();
				$old_amount += $_tmp[$i]->getOldAmount();
				$lost_ships += $_tmp[$i]->getLostShips();
			}
			$_nShips->setAmount($amount);
			$_nShips->setOldAmount($old_amount);
			$_nShips->setLostShips($lost_ships);

			return $_nShips;
		} else {
			return false;
		}
	}

	function getAllShipCollection() {
		return $this->shipCollection;
	}
	function setAllShipCollection($collection) {
		unset($this->shipCollection);
		$this->shipCollection = array();
		array_push($this->shipCollection, $collection);
	}
	function preRemoveShips($prim, $sec, $hitted_armor) {
		$preremove = array();
		$preremove[0] = $prim;
		$preremove[1] = $sec;
		$preremove[2] = $hitted_armor;
		array_push($this->preRemoveCollection, $preremove);
	}
	function doRemoveShips() {
		$prc = $this->preRemoveCollection;
		for ($i = 0; $i < count($prc); $i++) {
			$preremove = $prc[$i];
			$this->removeShips($preremove[0], $preremove[1], $preremove[2]);
		}
	}
	function newRemoveShips($prim, $sec, $h_armor) {
		$_primShips = array();
		$_secShips = array();

		$_totalPrimArmor = 0;
		$_totalSecArmor = 0;

		$_primDamage = 0;

		for ($i = 0; $i < count($this->shipCollection); $i++) {
			if ($this->shipCollection[$i]->getId() == $prim) {
				array_push($_primShips, $i);
				$_totalPrimArmor += $this->shipCollection[$i]->getTotalArmor();
			}
			if ($this->shipCollection[$i]->getId() == $sec) {
				array_push($_secShips, $i);
				$_totalSecArmor += $this->shipCollection[$i]->getTotalArmor();
			}
		}
		if ($h_armor <= $_totalPrimArmor) {
			$primaryHittedArmor = $h_armor;
			$secundaryHittedArmor = 0;
		}
		elseif ($h_armor > $_totalPrimArmor) {
			$primaryHittedArmor = $_totalPrimArmor;
			if (($h_armor - $_totalPrimArmor) <= $_totalSecArmor) {
				$secundaryHittedArmor = ($h_armor - $_totalPrimArmor);
			}
			elseif (($h_armor - $_totalPrimArmor) > $_totalSecArmor) {
				$secundaryHittedArmor = $_totalSecArmor;
			}
		}
		for ($i = 0; $i < count($_primShips); $i++) {
			$fleetarmor = $this->shipCollection[$_primShips[$i]]->getTotalArmor();
			if ($fleetarmor > 0) {
				$fleetdamage = ($fleetarmor / $_totalPrimArmor)*$primaryHittedArmor;
				$this->shipCollection[$_primShips[$i]]->removeShipsByDamage($fleetdamage);
			}
		}
		for ($i = 0; $i < count($_secShips); $i++) {
			$fleetarmor = $this->shipCollection[$_secShips[$i]]->getTotalArmor();
			if ($fleetarmor > 0) {
				$fleetdamage = ($fleetarmor / $_totalSecArmor)*$secundaryHittedArmor;
				$this->shipCollection[$_secShips[$i]]->removeShipsByDamage($fleetdamage);
			}
		}
	}
	function removeShips($prim, $sec, $hitted_armor) {
		$armor_percent = 0;
		for ($i = 0; $i < count($this->shipCollection); $i++) {
			unset($total_shiparmor, $armor_percent);
			if ($this->shipCollection[$i]->getId() == $prim) {
				if ($this->shipCollection[$i]->getAmount() > 0) {
					$total_shiparmor = $this->shipCollection[$i]->getTotalArmor();
					if ($hitted_armor > $total_shiparmor) {
						$hitted_armor -= $total_shiparmor;
						$armor_percent = 100;
					}
					else {
						$armor_percent = $hitted_armor / ($total_shiparmor / 100);
						$hitted_armor -= (($hitted_armor/100) * $armor_percent);
					}
					$this->shipCollection[$i]->removeShip($armor_percent);
				}
			}
			if ($this->shipCollection[$i]->getId() == $sec) {
				if ($this->shipCollection[$i]->getAmount() > 0) {
					$total_shiparmor = $this->shipCollection[$i]->getTotalArmor();
					if ($hitted_armor > $total_shiparmor) {
						$hitted_armor -= $total_shiparmor;
						$armor_percent = 100;
					}
					else {
						$armor_percent = $hitted_armor / ($total_shiparmor / 100);
						$hitted_armor -= (($hitted_armor/100) * $armor_percent);
					}
					$this->shipCollection[$i]->removeShip($armor_percent);
				}
			}
		}
	}
	function captureAsteroids($ship_id, $total_captured, $p_steel, $p_crystal, $p_erbium, $p_unused) {
		$total_ships = $this->getTotalShipAmount($ship_id);
		$shipcol = $this->getShipCollection($ship_id);
		for ($i = 0; $i < count($shipcol); $i++) {
			$c_shipcol = $shipcol[$i];
			$p_ships = $c_shipcol->getAmount() / ($total_ships / 100);
		}
	}
	function getPlayerIds() {
		$_pid = array();
		for ($i = 0; $i < count($this->shipCollection); $i++) {
			$sc = $this->shipCollection[$i];
			$npid = $sc->getPlayerId();
			if (!in_array($npid, $_pid)) {
				array_push($_pid, $npid);
			}
		}
		return $_pid;
	}
	function updateShipsAmountByUniqueId($unique_id, $ships) {
		for ($i = 0; $i < count($this->shipCollection); $i++) {
			if ($this->shipCollection[$i]->getUnique() == $unique_id) {
				$this->shipCollection[$i]->decreaseShipAmount($this->shipCollection[$i]->getAmount() - $ships->getAmount());
			}
		}
	}
}

class Ships {
	var $unique_id;
	var $player_id;
	var $fleet_id;
	var $ship_id;
	var $amount;
	var $armor;
	var $firepower;
	var $prim;
	var $sec;

	var $lost_this_tick;
	var $old_amount;

	function Ships($unique_id, $player_id, $fleet_id, $ship_id, $armor, $firepower, $prim, $sec) {
		$this->unique_id = $unique_id;
		$this->player_id = $player_id;
		$this->fleet_id = $fleet_id;
		$this->ship_id = $ship_id;
		$this->armor = $armor;
		$this->firepower = $firepower;
		$this->prim = $prim;
		$this->sec = $sec;

		$this->lost_this_tick = 0;
		$this->old_amount = 0;
	}
	function getUnique() {
		return $this->unique_id;
	}
	function addShip($amount) {
		$this->amount += $amount;
	}
	function removeShipsByDamage($damage) {
		$shipnum = floor($damage / $this->armor);

		$this->decreaseShipAmount($shipnum);
	}
	function removeShip($percentage) {
		$total_armor = ($this->amount * $this->armor);
		$new_armor = $total_armor - (round(($total_armor / 100) * $percentage));
		$rem_ships = round($new_armor / $this->armor);
		$this->lost_this_tick = $rem_ships;
		$this->old_amount = $this->amount;
		$this->amount -= $rem_ships;
	}
	function decreaseShipAmount($num) {
		if ($this->old_amount == 0) {
			$this->old_amount = $this->amount;
		}
		$this->lost_this_tick += $num;
		$this->amount -= $num;
	}
	function setCappedRoids($steel, $crystal, $erbium, $unused) {
		$this->steel_roids = $steel;
		$this->crystal_roids = $crystal;
		$this->erbium_roids = $erbium;
		$this->unused_roids = $unused;
	}
	function getLostShips() {
		return $this->lost_this_tick;
	}
	function setLostShips($lost_ships) {
		$this->lost_this_tick = $lost_ships;
	}
	function getOldAmount() {
		return $this->old_amount;
	}
	function setOldAmount($old_amount) {
		$this->old_amount = $old_amount;
	}
	function getAmount() {
		return $this->amount;
	}
	function setAmount($amount) {
		$this->amount = $amount;
	}
	function getPlayerId() {
		return $this->player_id;
	}
	function getId() {
		return $this->ship_id;
	}
	function getArmor() {
		return $this->armor;
	}
	function getTotalArmor() {
		$totalarmor = $this->amount * $this->armor;
		return $totalarmor;
	}
	function getFirepower() {
		return $this->firepower;
	}
	function getPrim() {
		return $this->prim;
	}
	function getSec() {
		return $this->sec;
	}
}