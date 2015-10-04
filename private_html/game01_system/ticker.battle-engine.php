<?
$time_start = getmicrotime();
unset($sql_findhome, $res_findhome, $num_findhome, $rec_findhome, $sql_updfleet, $sql_fattack, $res_fattack, $num_fattack, $reC_fattack, $attackfleet, $fleet_id, $source_id);
unset($sql_findaships, $res_findaships, $num_findaships, $rec_findaships, $eta, $action_start, $sql_fleethome, $sql_fdefend, $res_fdefend, $num_fdefend, $rec_fdefend, $sql_finddships);
unset($res_finddships, $num_finddships, $rec_finddships, $sql_playerfleet, $res_playerfleet, $num_playerfleet, $rec_playerfleet, $sql_findpships, $res_findpships, $num_findpships);
unset($rec_findpships, $sql_allships, $defendfleet, $single_shipdata, $ship_id, $defend_shipcollection, $ship_amount, $hitted_armor, $attack_shipcollection);
unset($target_steelroids, $target_crystalroids, $target_erbiumroids, $target_unusedroids, $target_totalroids, $target_totalhittedarmor, $target_roidarmor);
unset($target_roidlost, $percent_steel, $percent_crystal, $percent_erbium, $percent_unused, $target_steellost, $target_crystallost, $target_erbiumlost);
unset($target_unusedlost, $total_roids, $piratefleet, $source_id, $sql_upd_roids_attacker, $sql_upd_roids_defender, $c, $playername, $sca, $scd, $current_ship_id);

// Set all home turning fleets at home, when it's their time :)
$sql_findhome = "SELECT `id`, `player_id`, `target_id`, `action`, `action_start`, `action_time`, `sent_tick`
					FROM `$table[playerfleet]` 
					WHERE `player_id` = '$id' AND `action` = 'home' AND '$current' >= `action_start`";
$res_findhome = mysql_query($sql_findhome);
$num_findhome = @mysql_num_rows($res_findhome);
if ($num_findhome > 0) {
	while ($rec_findhome = mysql_fetch_assoc($res_findhome)) {
		$sql_updfleet = "UPDATE $table[playerfleet] SET `target_id` = '0', `action_start` = '0', `sent_tick` = '0' WHERE `id` = '$rec_findhome[id]'";
		mysql_query($sql_updfleet);

	}
}

$sql_fattack = "SELECT `id`, `player_id`, `target_id`, `action`, `action_start`, `action_time`, `sent_tick`
					FROM `$table[playerfleet]` 
					WHERE `target_id` = '$id' AND `action` = 'attack' AND (
						'$current' < (`action_start` + `action_time`) 
						AND '$current' >= `action_start` 
					) ORDER BY `target_id` ASC";

$res_fattack = mysql_query($sql_fattack);
$num_fattack = @mysql_num_rows($res_fattack);

if ($num_fattack > 0) {

	$attackfleet = new Fleet($id);
	while ($rec_fattack = mysql_fetch_assoc($res_fattack)) {
		$fleet_id = $rec_fattack['id'];
		$source_id = $rec_fattack['player_id'];

		$sql_findaships = "
				SELECT $table[ships].id, $table[ships].firepower, $table[ships].armor, $table[ships].traveltime, $table[playerfleet_ships].id AS unique_id,
						$table[playerfleet_ships].player_id, $table[playerfleet_ships].fleet_id, $table[playerfleet_ships].ship_id, $table[playerfleet_ships].type_id, 
						$table[playerfleet_ships].class_id,	$table[playerfleet_ships].amount, $table[playerfleet_ships].primary, $table[playerfleet_ships].secondary
						FROM `$table[ships]` 
						INNER JOIN $table[playerfleet_ships] ON $table[ships].id = $table[playerfleet_ships].ship_id
						WHERE
						$table[playerfleet_ships].player_id = '$source_id' AND 
						$table[playerfleet_ships].fleet_id = '$fleet_id'
						ORDER BY $table[ships].traveltime DESC";

		$res_findaships = mysql_query($sql_findaships) or die('SQL ERROR (finding attackships)\n'.mysql_error());
		$num_findaships = @mysql_num_rows($res_findaships);
		if ($num_findaships > 0) {
			$counter = 0;
			while ($rec_findaships = mysql_fetch_assoc($res_findaships)) {
				$attackfleet->addShips($rec_findaships['unique_id'], $source_id, $fleet_id, $rec_findaships['ship_id'], $rec_findaships['amount'], $rec_findaships['armor'], $rec_findaships['firepower'], $rec_findaships['primary'],$rec_findaships['secondary']);
				if ($counter == 0) { $slowest_traveltime = $rec_findaships['traveltime']; }
				$counter++;
			}
		}

		if ($current >= ($rec_fattack['action_start'] + $rec_fattack['action_time'])) {
			if (($current - $rec_fattack['sent_tick']) > $slowest_traveltime) { $eta = $slowest_traveltime; }
			else { $eta = $current - $rec_fattack['sent_tick']; }
			$target_xyz = getXYZ($id);
			$player_xyz = getXYZ($source_id);
			$eta_bonus = 0;
			if (($target_xyz[0] == $player_xyz[0]) && ($target_xyz[1] == $player_xyz[1])) { $eta_bonus = 10; }
			elseif ($target_xyz[0] == $player_xyz[0]) { $eta_bonus = 5; }
			$eta -= $eta_bonus;
			$action_start = $current + $eta;
			$sql_fleethome = "UPDATE $table[playerfleet] SET `target_id` = '$rec_fdefend[player_id]', `action` = 'home', `action_start` = '$action_start', `action_time` = '0' WHERE `id` = '$rec_fattack[id]'";
			mysql_query($sql_fleethome);
		}
	}


	$sql_fdefend = "SELECT `id`, `player_id`, `target_id`, `action`, `action_start`, `action_time`, `sent_tick`
					FROM `$table[playerfleet]` 
					WHERE `target_id` = '$id' AND `action` = 'defend' AND (
						'$current' < (`action_start` + `action_time`) 
						AND '$current' >= `action_start` 
					) ORDER BY `target_id` ASC";

	$res_fdefend = mysql_query($sql_fdefend);
	$num_fdefend = @mysql_num_rows($res_fdefend);
	if ($num_fdefend > 0) {
		$defendfleet = new Fleet($id);
		while ($rec_fdefend = mysql_fetch_assoc($res_fdefend)) {
			$fleet_id = $rec_fdefend['id'];
			$source_id = $rec_fdefend['player_id'];
			$sql_finddships = "
				SELECT $table[ships].id, $table[ships].firepower, $table[ships].armor, $table[ships].traveltime, $table[playerfleet_ships].id AS unique_id,
						$table[playerfleet_ships].player_id, $table[playerfleet_ships].fleet_id, $table[playerfleet_ships].ship_id, $table[playerfleet_ships].type_id, 
						$table[playerfleet_ships].class_id,	$table[playerfleet_ships].amount, $table[playerfleet_ships].primary, $table[playerfleet_ships].secondary
						FROM `$table[ships]` 
						INNER JOIN $table[playerfleet_ships] ON $table[ships].id = $table[playerfleet_ships].ship_id
						WHERE
						$table[playerfleet_ships].player_id = '$source_id' AND 
						$table[playerfleet_ships].fleet_id = '$fleet_id'
						ORDER BY $table[ships].traveltime DESC";

			$res_finddships = mysql_query($sql_finddships) or die('SQL ERROR (finding defendships)\n'.mysql_error());
			$num_finddships = @mysql_num_rows($res_finddships);
			if ($num_finddships > 0) {
				$counter = 0;
				while ($rec_finddships = mysql_fetch_assoc($res_finddships)) {
					$defendfleet->addShips($rec_finddships['unique_id'], $source_id, $fleet_id, $rec_finddships['ship_id'], $rec_finddships['amount'], $rec_finddships['armor'], $rec_finddships['firepower'], $rec_finddships['primary'], $rec_finddships['secondary']);
					if ($counter == 0) { $slowest_traveltime = $rec_findaships['traveltime']; }
					$counter++;
				}
			}

			if ($current >= ($rec_fdefend['action_start'] + $rec_fdefend['action_time'])) {
				if (($current - $rec_fdefend['sent_tick']) > $slowest_traveltime) { $eta = $slowest_traveltime; }
				else { $eta = $current - $rec_fdefend['sent_tick']; }
				$target_xyz = getXYZ($id);
				$player_xyz = getXYZ($source_id);
				$eta_bonus = 0;
				if (($target_xyz[0] == $player_xyz[0]) && ($target_xyz[1] == $player_xyz[1])) { $eta_bonus = 10; }
				elseif ($target_xyz[0] == $player_xyz[0]) { $eta_bonus = 5; }
				$eta -= $eta_bonus;
				$action_start = $current + $eta;
				$sql_fleethome = "UPDATE $table[playerfleet] SET `target_id` = '$rec_fdefend[player_id]', `action` = 'home', `action_start` = '$action_start', `action_time` = '0' WHERE `id` = '$rec_fdefend[id]'";
				mysql_query($sql_fleethome);
			}
		}
	}

	$sql_playerfleet = "SELECT `id`, `player_id`, `target_id`, `action`, `action_start`, `action_time`, `sent_tick`
					FROM `$table[playerfleet]` 
					WHERE `player_id` = '$id' AND `action` = 'home' AND `target_id` = '0' AND `action_start` = '0'";
	$res_playerfleet = mysql_query($sql_playerfleet);
	$num_playerfleet = @mysql_num_rows($res_playerfleet);
	if ($num_playerfleet > 0) {
		if (!$defendfleet) { $defendfleet = new Fleet($id); }
		while ($rec_playerfleet = mysql_fetch_assoc($res_playerfleet)) {
			$fleet_id = $rec_playerfleet['id'];
			$sql_findpships = "
				SELECT $table[ships].id, $table[ships].firepower, $table[ships].armor, $table[playerfleet_ships].id AS unique_id,
						$table[playerfleet_ships].player_id, $table[playerfleet_ships].fleet_id, $table[playerfleet_ships].ship_id, $table[playerfleet_ships].type_id, 
						$table[playerfleet_ships].class_id,	$table[playerfleet_ships].amount, $table[playerfleet_ships].primary, $table[playerfleet_ships].secondary
						FROM `$table[ships]` INNER JOIN $table[playerfleet_ships]
						ON $table[ships].id = $table[playerfleet_ships].ship_id					
						WHERE
						$table[playerfleet_ships].player_id = '$id' AND 
						$table[playerfleet_ships].fleet_id = '$fleet_id'
						";
			$res_findpships = mysql_query($sql_findpships) or die('SQL ERROR (finding player-defendships)\n'.mysql_error());
			$num_findpships = @mysql_num_rows($res_findpships);
			if ($num_findpships > 0) {
				while ($rec_findpships = mysql_fetch_assoc($res_findpships)) {
					$defendfleet->addShips($rec_findpships['unique_id'], $id, $fleet_id, $rec_findpships['ship_id'], $rec_findpships['amount'], $rec_findpships['armor'], $rec_findpships['firepower'], $rec_findpships['primary'], $rec_findpships['secondary']);
				}
			}
		}
	}

	for ($y = 0; $y < count($shipdata); $y++) {
		$single_shipdata = $shipdata[$y];
		$ship_id = $single_shipdata['id'];;
		unset($defend_shipcollection, $attack_shipcollection);

		if ($defendfleet) {
			$defend_shipcollection = $defendfleet->getShipCollection($ship_id);
			for ($i = 0; $i < count($defend_shipcollection); $i++) {
				if (($defend_shipcollection[$i]->getPrim() > 0) || ($defend_shipcollection[$i]->getSec() > 0)) {
					$ship_amount = $defend_shipcollection[$i]->getAmount();
					$hitted_armor = ($ship_amount * $single_shipdata['firepower']) * $single_shipdata['accurate'];

					$attackfleet->newRemoveShips($defend_shipcollection[$i]->getPrim(), $defend_shipcollection[$i]->getSec(), $hitted_armor);
					appendPlayerLog($defend_shipcollection[$i]->getPlayerId(), 'Removing ships for attacker. Prim target: '.$defend_shipcollection[$i]->getPrim().'. Sec target: '.$defend_shipcollection[$i]->getSec().'. Hitted armor: '.$hitted_armor, '');
				}
			}

			$attack_shipcollection = $attackfleet->getShipCollection($ship_id);

			for ($i = 0; $i < count($attack_shipcollection); $i++) {
				if (($attack_shipcollection[$i]->getPrim() > 0) || ($attack_shipcollection[$i]->getSec() > 0)) {
					$ship_amount = $attack_shipcollection[$i]->getAmount();
					$hitted_armor = ($ship_amount * $single_shipdata['firepower']) * $single_shipdata['accurate'];

					$defendfleet->newRemoveShips($attack_shipcollection[$i]->getPrim(), $attack_shipcollection[$i]->getSec(), $hitted_armor);
					appendPlayerLog($attack_shipcollection[$i]->getPlayerId(), 'Removing ships for defender. Prim target: '.$attack_shipcollection[$i]->getPrim().'. Sec target: '.$attack_shipcollection[$i]->getSec().'. Hitted armor: '.$hitted_armor, '');
				}
			}
		}
	}

	$target_id = $id;
	$target_steelroids = $new['roid_steel'];
	$target_crystalroids = $new['roid_crystal'];
	$target_erbiumroids = $new['roid_erbium'];
	$target_unusedroids = $new['roid_unused'];
	$target_totalroids = $target_steelroids + $target_crystalroids + $target_erbiumroids + $target_unusedroids;

	if ($target_totalroids > 0) {
		for ($i = 0; $i < count($shipdata); $i++) {
			$single_shipdata = $shipdata[$i];
			$ship_id = $single_shipdata['id'];
			$target_totalhittedarmor = 0;
			$target_roidarmor = $ASTEROID_ARMOR * $target_totalroids;
			if (($ship_id == $ASTEROID_PIRATE) || ($ship_id == $NG_ASTEROID_PIRATE)) {
				$piratefleet = $attackfleet->getShipCollection($ship_id);
				$ship_amount = $attackfleet->getTotalShipAmount($ship_id);
				if ($ship_amount > 0) {
					$hitted_armor = ($ship_amount * $single_shipdata['firepower']) * $single_shipdata['accurate'];
					if ($hitted_armor <= (0.10 * $target_roidarmor)) {
						$target_totalhittedarmor = $hitted_armor;
					}
					elseif ($hitted_armor > (0.10 * $target_roidarmor)) {
						$target_totalhittedarmor = (0.10 * $target_roidarmor);
					}
					$target_roidslost = ($target_totalhittedarmor / $ASTEROID_ARMOR);

					$target_steellost = floor($target_roidslost * ($target_steelroids / $target_totalroids));
					$target_crystallost = floor($target_roidslost * ($target_crystalroids / $target_totalroids));
					$target_erbiumlost = floor($target_roidslost * ($target_erbiumroids / $target_totalroids));
					$target_unusedlost = floor($target_roidslost * ($target_unusedroids / $target_totalroids));

					$total_roids = ($target_steellost + $target_crystallost + $target_erbiumlost + $target_unusedlost);

					for ($j = 0; $j < count($piratefleet); $j++) {
						$current_ship_amount = $piratefleet[$j]->getAmount();
						$class_damage = $current_ship_amount * ($single_shipdata['firepower'] * $single_shipdata['accurate']);

						$steel_roids_capped = floor($class_damage * ($target_steellost / $hitted_armor));
						$crystal_roids_capped = floor($class_damage * ($target_crystallost / $hitted_armor));
						$erbium_roids_capped = floor($class_damage * ($target_erbiumlost / $hitted_armor));
						$unused_roids_capped = floor($class_damage * ($target_unusedlost / $hitted_armor));
						$roids_capped = ($steel_roids_capped + $crystal_roids_capped + $erbium_roids_capped + $unused_roids_capped);

						appendPlayerLog($piratefleet[$j]->getPlayerId(), 'Roids stolen. S:'.$steel_roids_capped.' C:'.$crystal_roids_capped.' E:'.$erbium_roids_capped.' U:'.$unused_roids_capped.' T:'.$roids_capped, '');
						$piratefleet[$j]->setAmount(($piratefleet[$j]->getAmount() - $roids_capped));

						$attackfleet->updateShipsAmountByUniqueId($piratefleet[$j]->getUnique(), $piratefleet[$j]);
						appendPlayerLog($piratefleet[$j]->getPlayerId(), 'Updating pirate fleet (ship id: '.$piratefleet[$j]->getId().'). Decreasing with '.$roids_capped.', because they capped roids.', '');

						$source_id = $piratefleet[$j]->getPlayerId();
						$sql_upd_roids_attacker = "UPDATE `$table[players]` SET
												`roid_steel` = `roid_steel` + $steel_roids_capped,
												`roid_crystal` = `roid_crystal` + $crystal_roids_capped,
												`roid_erbium` = `roid_erbium` + $erbium_roids_capped,
												`roid_unused` = `roid_unused` + $unused_roids_capped
												WHERE `id` = $source_id";

						$new['roid_steel'] -= $steel_roids_capped;
						$new['roid_crystal'] -= $crystal_roids_capped;
						$new['roid_erbium'] -= $erbium_roids_capped;
						$new['roid_unused'] -= $unused_roids_capped;
						mysql_query($sql_upd_roids_attacker) or die(mysql_error());

						$c = getXYZ($target_id);
						$playername = getRulernameById($target_id).' of '.getPlanetnameById($target_id);
						addNews($source_id, 'Battle', 'Asteroid pirate report',
						'<table border="0" width="450" style="border: 1px solid #3C5762">
	<tr style="border: 1px solid #3C5762">
	<td colspan="2" width="450" >Asteroid pirate report from: <b>'.getShipProperty($ship_id, 'name').'</b></td>
	<tr style="border: 1px solid #3C5762">
	<td width="150"><b>Asteroid type</b></td>
	<td width="250"><b>Amount stolen from '.$c[0].':'.$c[1].':'.$c[2].' ('.$playername.')</b></td>
	</tr>
	<tr style="border: 1px solid #3C5762">
	<td>Steel</td>
	<td>'.$steel_roids_capped.'</td>
	</tr>
	<tr style="border: 1px solid #3C5762">
	<td>Crystal</td>
	<td>'.$crystal_roids_capped.'</td>
	</tr>
	<tr style="border: 1px solid #3C5762">
	<td>Erbium</td>
	<td>'.$erbium_roids_capped.'</td>
	</tr>
	<tr style="border: 1px solid #3C5762">
	<td>Unused</td>
	<td>'.$unused_roids_capped.'</td>
	</tr>
	<tr style="border: 1px solid #3C5762">
	<td>Total</td>
	<td>'.$roids_capped.'</td>
	</tr>
	</table>');
					}
				}
			}
		}
	}

	generateBattleReports($id, $shipdata, $attackfleet, $defendfleet);

	$sca = $attackfleet->getAllShipCollection();
	if ($defendfleet) {
		$scd = $defendfleet->getAllShipCollection();
	}

	for ($i = 0; $i < count($shipdata); $i++) {
		$current_ship_id = $shipdata[$i]['id'];
		for ($j = 0; $j < count($sca); $j++) {
			$ship_id = $sca[$j]->ship_id;
			$sql_updship1 = "";
			$sql_updship2 = "";
			if ($ship_id == $current_ship_id) {
				$player_id = $sca[$j]->player_id;
				$fleet_id = $sca[$j]->fleet_id;

				$new_amount = $sca[$j]->getAmount();
				$lost_ships = $sca[$j]->getLostShips();
				$old_amount = $sca[$j]->getOldAmount();

				if ($lost_ships > 0) {
					$sql_updship1 = "UPDATE $table[playerunit] SET `amount` = `amount` - '$lost_ships' WHERE `unit_id` = '$ship_id' AND `player_id` = '$player_id'";
					appendPlayerLog($player_id, 'Updating ship('.$ship_id.') total amount (dec. with: '.$lost_ships.'). Updated because they where lost in battle (as attacker).', '');
					mysql_query($sql_updship1) or die(mysql_error());
				}
				if ($new_amount <> $old_amount) {
					$sql_updship2 = "UPDATE $table[playerfleet_ships] SET `amount` = '$new_amount' WHERE `ship_id` = '$ship_id' AND `player_id` = '$player_id' AND `fleet_id` = '$fleet_id'";
					appendPlayerLog($player_id, 'Updating ship('.$ship_id.') fleet amount (new amount: '.$new_amount.'). Updated because they where lost in battle (as attacker).', '');
					mysql_query($sql_updship2) or die(mysql_error());
				}
			}
		}
		for ($j = 0; $j < count($scd); $j++) {
			$ship_id = $scd[$j]->ship_id;
			$sql_updship1 = "";
			$sql_updship2 = "";
			if ($ship_id == $current_ship_id) {
				$player_id = $scd[$j]->player_id;
				$fleet_id = $scd[$j]->fleet_id;

				$new_amount = $scd[$j]->getAmount();
				$lost_ships = $scd[$j]->getLostShips();
				$old_amount = $scd[$j]->getOldAmount();

				if ($lost_ships > 0) {
					$sql_updship1 = "UPDATE $table[playerunit] SET `amount` = `amount` - '$lost_ships' WHERE `unit_id` = '$ship_id' AND `player_id` = '$player_id'";
					appendPlayerLog($player_id, 'Updating ship('.$ship_id.') total amount (dec. with: '.$lost_ships.'). Updated because they where lost in battle (as defender).', '');
					mysql_query($sql_updship1) or die(mysql_error());
				}
				if ($new_amount <> $old_amount) {
					$sql_updship2 = "UPDATE $table[playerfleet_ships] SET `amount` = '$new_amount' WHERE `ship_id` = '$ship_id' AND `player_id` = '$player_id' AND `fleet_id` = '$fleet_id'";
					appendPlayerLog($player_id, 'Updating ship('.$ship_id.') fleet amount (new amount: '.$new_amount.'). Updated because they where lost in battle (as defender).', '');
					mysql_query($sql_updship2) or die(mysql_error());
				}
			}
		}
	}

	$asteroid_report = getARHtmlStart();

	$asteroid_report .= getARHtml('Steel', $p['roid_steel'], ($p['roid_steel'] - $new['roid_steel']));
	$asteroid_report .= getARHtml('Crystal', $p['roid_crystal'], ($p['roid_crystal'] - $new['roid_crystal']));
	$asteroid_report .= getARHtml('Erbium', $p['roid_erbium'], ($p['roid_erbium'] - $new['roid_erbium']));
	$asteroid_report .= getARHtml('Unused', $p['roid_unused'], ($p['roid_unused'] - $new['roid_unused']));
	$ar_total = $p['roid_steel'] + $p['roid_crystal'] + $p['roid_erbium'] + $p['roid_unused'];
	$ar_totalnew = $new['roid_steel'] + $new['roid_crystal'] + $new['roid_erbium'] + $new['roid_unused'];
	$asteroid_report .= getARHtml('Total', $ar_total, ($ar_total - $ar_totalnew));
	$asteroid_report .= getARHtmlEnd();
	addNews($id, 'Battle', 'Asteroid losses', $asteroid_report);

	$time_end = getmicrotime();
	$time = $time_end - $time_start;
	//debug('The battle ran for '.$time.' seconds.');
}
?>