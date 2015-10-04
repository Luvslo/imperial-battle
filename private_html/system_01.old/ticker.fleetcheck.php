<?
/* find fleets who are not at home yet.. */
$sql_findhomefleets = "SELECT `id`, `player_id`, `target_id`, `action`, `action_start`, `action_time`, `sent_tick`
						FROM `$table[playerfleet]` 
						WHERE (`target_id` = `player_id` OR `target_id` = '0') AND `action` = 'home' 
							AND ('$current' >= (`action_start` + `action_time`)
						ORDER BY `target_id` ASC";
$res_findhomefleets = mysql_query($sql_findhomefleets);
$num_findhomefleets = @mysql_num_rows($res_findhomefleets);
if ($num_findhomefleets > 0) {
	while($rec_findhomefleets = mysql_fetch_array($res_findhomefleets)) {
		$sql_puthomefleets = "UPDATE $table[playerfleet]
								SET `target_id` = '0', `action` = 'home', `action_start` = '0', `action_time` = '0', `sent_tick` = '0'
								WHERE `id` = '$rec_findhomefleets[id]'";
		mysql_query($sql_puthomefleets) or die(mysql_error());
		appendPlayerLog($rec_findhomefleets['player_id'], 'Found fleet for home ('.$rec_findhomefleets['id'].'). The fleet is now set as home fleet.', '');
	}
}

/* find empty fleets, and delete them.. */
$sql_emptyfleets = "SELECT $table[playerfleet].id, $table[playerfleet].action, $table[playerfleet].action_start, $table[playerfleet].player_id,
						SUM($table[playerfleet_ships].amount) AS amount
						FROM $table[playerfleet]
						INNER JOIN $table[playerfleet_ships] ON $table[playerfleet].id = $table[playerfleet_ships].fleet_id
						WHERE ($table[playerfleet].action = 'attack' OR $table[playerfleet].action = 'defend')
							OR ($table[playerfleet].action = 'home' AND $table[playerfleet].action_start > '0')
						GROUP BY $table[playerfleet].id";

$res_emptyfleets = mysql_query($sql_emptyfleets);
$num_emptyfleets = @mysql_num_rows($res_emptyfleets);
if ($num_emptyfleets > 0) {
	while ($rec_emptyfleets = mysql_fetch_array($res_emptyfleets)) {
		if (($rec_emptyfleets['amount'] == 0)) {
			$sql_delfleet = "DELETE FROM $table[playerfleet] WHERE `id` = '$rec_emptyfleets[id]'";
			$sql_delfleetships = "DELETE FROM $table[playerfleet_ships] WHERE `fleet_id` = '$rec_emptyfleets[id]'";
			mysql_query($sql_delfleet) or die(mysql_error());
			mysql_query($sql_delfleetships) or die(mysql_error());
			addNews($rec_emptyfleets['player_id'], 'Combat', 'Lost fleet',
			'Staff from the intelligence center reports a lost fleet.<br />
			The fleets identification number was '.$rec_emptyfleets['id'].'.');
			appendPlayerLog($rec_emptyfleets['player_id'], 'Found empty fleet ('.$rec_emptyfleets['id'].'), and removed it.', '');
		}
	}
}

/* find empty fleetship entrys, and delete them.. */
$sql_fleetshipe = "DELETE FROM $table[playerfleet_ships] WHERE `amount` <= '0'";
mysql_query($sql_fleetshipe);

/* find fleets which should already be returning, but aint doing it.. */
$slowest_traveltime = 0;

$sql_findreturning = "SELECT `id`, `player_id`, `target_id`, `action`, `action_start`, `action_time`, `sent_tick`
						FROM `$table[playerfleet]` 
						WHERE `action` != 'home' 
							AND '$current' >= (`action_start` + `action_time`)
						ORDER BY `target_id` ASC";
$res_findreturning = mysql_query($sql_findreturning) or die(mysql_error());
if (@mysql_num_rows($res_findreturning) > 0) {
	while ($rec_findreturning = mysql_fetch_assoc($res_findreturning)) {
		$sql_findtraveltime = "
					SELECT $table[ships].id, $table[ships].traveltime, $table[playerfleet_ships].id AS unique_id,
						$table[playerfleet_ships].player_id, $table[playerfleet_ships].fleet_id, $table[playerfleet].target_id
						FROM `$table[ships]` 
						INNER JOIN $table[playerfleet_ships] ON $table[ships].id = $table[playerfleet_ships].ship_id
						INNER JOIN $table[playerfleet] ON $table[playerfleet].id = $table[playerfleet_ships].fleet_id
						WHERE $table[playerfleet_ships].fleet_id = '$rec_findreturning[id]'
						ORDER BY $table[ships].traveltime DESC";
		$res_findtraveltime = mysql_query($sql_findtraveltime);
		$num_findtraveltime = @mysql_num_rows($res_findtraveltime);
		if ($num_findtraveltime > 0) {
			$rec_findtraveltime = mysql_fetch_assoc($res_findtraveltime);
			$slowest_traveltime = $rec_findtraveltime['traveltime'];
		}
		if ($slowest_traveltime > 0) {
			if (($current - $rec_findreturning['sent_tick']) > $slowest_traveltime) { $eta = $slowest_traveltime; }
			else { $eta = $current - $rec_findreturning['sent_tick']; }
		} else {
			$eta = $current - $rec_findreturning['sent_tick'];
		}
		$target_xyz = getXYZ($rec_findtraveltime['target_id']);
		$player_xyz = getXYZ($rec_findtraveltime['player_id']);
		$eta_bonus = 0;
		if (($target_xyz[0] == $player_xyz[0]) && ($target_xyz[1] == $player_xyz[1])) { $eta_bonus = 10; }
		elseif ($target_xyz[0] == $player_xyz[0]) { $eta_bonus = 5; }
		$eta -= $eta_bonus;
		$action_start = $current + $eta;
		$sql_fleethome = "UPDATE $table[playerfleet] SET `target_id` = '$rec_findreturning[player_id]', `action` = 'home', `action_start` = '$action_start', `action_time` = '0' WHERE `id` = '$rec_findreturning[id]'";
		mysql_query($sql_fleethome) or die(mysql_error());
		appendPlayerLog($rec_findreturning['player_id'], 'Found fleet which should already be returning home (id: '.$rec_findreturning['id'].'). Sending it home now..', '');
	}
}
?>