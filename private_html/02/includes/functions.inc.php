<?
function debug($data) {
	echo $data.'<br>';
}
function getHash($id, $username, $time) {
	global $PRIVATEKEY;
	$hash = md5($username.$time.$id.$PRIVATEKEY);
	return $hash;
}
function getPlayerdata($player_id) {
	global $table;
	$sql_playerdata = "SELECT * FROM $table[players] WHERE `id` = '$player_id'";
	$rec_playerdata = mysql_fetch_array(mysql_query($sql_playerdata));
	
	//$sql_xy = "SELECT `x`, `y` FROM $table[galaxy] WHERE `id` = '$rec_playerdata[galaxy_id]'";
	//$rec_xy = mysql_fetch_array(mysql_query($sql_xy));
	
	//$rec_playerdata['x'] = $rec_xy['x'];
	//$rec_playerdata['y'] = $rec_xy['y'];
	
	return $rec_playerdata;
}
function parseInteger($num) {
	/*$len = strlen($num);
	$rest = $len % 3;
	$newstr = substr($num,0,$rest);
	for ($i = $rest; $i <= $len; $i += 3) {
	if ($i != 0) { $splitchar = '.'; }
	else { unset($splitchar); }
	$newstr .= $splitchar.substr($num,$i,3);
	}
	$newstr = substr($newstr,0,strlen($newstr) - 1);
	*/
	$newnum = number_format($num, 0, ',', '.');
	return $newnum;
}

function getDuration($tseconds) {
	if ($tseconds > 3600) {
		$hrs = floor($tseconds / 3600);
		$tseconds -= ($hrs * 3600);
		$min = floor($tseconds / 60);
		$tseconds -= ($min * 60);
		$sec = $tseconds;
		$returnstr = $hrs.'h '.$min.'m '.$sec.'s';
	}
	elseif ($tseconds > 60) {
		$min = floor($tseconds / 60);
		$tseconds -= ($min * 60);
		$sec = $tseconds;
		$returnstr = $min.'m '.$sec.'s';
	}
	elseif ($tseconds <= 60) {
		$returnstr = $tseconds.'s';
	}
	return $returnstr;
}
function getPlayerRank($player_id) {
	global $table;
	$sql = "SELECT `id` FROM $table[players] ORDER BY `score` DESC";
	$res = mysql_query($sql) or die(mysql_query($sql));
	if (@mysql_num_rows($res) > 0) {
		$data = array();
		while ($rec = mysql_fetch_assoc($res)) {
			array_push($data, $rec);
		}
		for ($i = 0; $i < count($data); $i++) {
			if ($data[$i]['id'] == $player_id)
			return $i+1;
		}
	}
}
function checkItem($player_id, $item_id)  {
	global $table;
	if ($item_id == 0) { return true; }
	$rec = mysql_query("SELECT `id` FROM $table[playeritem] WHERE `player_id` = '$player_id' AND `item_id` = '$item_id'");
	if (@mysql_num_rows($rec) > 0) { return true; }
	return false;
}

function tryProductionAdd($player_id, $type_id, $item_id) {
	global $table;
	$sql_chkproduction1 = "SELECT * FROM $table[productions] WHERE `player_id` = '$player_id' AND `type_id` = '$type_id'";
	$sql_chkproduction2 = "SELECT * FROM $table[productions] WHERE `player_id` = '$player_id' AND `item_id` = '$item_id'";
	$sql_getitemdata = "SELECT * FROM $table[items] WHERE `id` = '$item_id'";

	if (checkItem($player_id, $item_id)) { return 101; }
	if (mysql_num_rows(mysql_query($sql_chkproduction2)) > 0) { return 102; }
	if (mysql_num_rows(mysql_query($sql_chkproduction1)) > 0) { return 103; }

	$res = mysql_fetch_array(mysql_query($sql_getitemdata));
	if (!checkItem($player_id, $res['depends'])) { return 104; }
	$cost_steel = $res['cost_steel'];
	$cost_crystal = $res['cost_crystal'];
	$cost_erbium = $res['cost_erbium'];
	$cost_titanium = $res['cost_titanium'];
	if (checkSteelResource($player_id, $cost_steel) && checkCrystalResource($player_id, $cost_crystal) && checkErbiumResource($player_id, $cost_erbium) && checkTitaniumResource($player_id, $cost_titanium)) {
		$currenttick = getCurrentTick();
		$ready_tick = $currenttick + $res['eta'];
		$sql_addprod = "INSERT INTO `$table[productions]` (`id`, `player_id`, `type_id`, `item_id`, `ready_tick`, `amount`)
					VALUES ('', '$player_id', '$type_id', '$item_id', '$ready_tick', '1')";
		mysql_query($sql_addprod);
		decreaseCurrentResources($player_id, $res['cost_steel'], $res['cost_crystal'], $res['cost_erbium'], $res['cost_titanium']);
	} else {
		return 105; /* Not enough resources. */
	}
	return 1;
}
function tryShipDefenseProductionAdd($player_id, $type_id, $item_table, $item_id, $amount) {
	global $table;
	$sql_getitemdata = "SELECT * FROM $item_table WHERE `id` = '$item_id'";
	$res_getitemdata = mysql_fetch_array(mysql_query($sql_getitemdata));
	if (!checkItem($player_id, $res_getitemdata['depends'])) { return 101; } /* 1; Depencies are incorrect */
	$cost_steel = $amount * $res_getitemdata['cost_steel'];
	$cost_crystal = $amount * $res_getitemdata['cost_crystal'];
	$cost_erbium = $amount * $res_getitemdata['cost_erbium'];
	$cost_titanium = $amount * $res_getitemdata['cost_titanium'];
	if (checkSteelResource($player_id, $cost_steel) && checkCrystalResource($player_id, $cost_crystal) && checkErbiumResource($player_id, $cost_erbium) && checkTitaniumResource($player_id, $cost_titanium)) {
		$currenttick = getCurrentTick();
		$ready_tick = $currenttick + $res_getitemdata['eta'];
		$sql_addprod = "INSERT INTO `$table[productions]` (`id`, `player_id`, `type_id`, `item_id`, `ready_tick`, `amount`)
					VALUES ('', '$player_id', '$type_id', '$item_id', '$ready_tick', '$amount')";
		mysql_query($sql_addprod);
		decreaseCurrentResources($player_id, $cost_steel, $cost_crystal, $cost_erbium, $cost_titanium);
	} else {
		return 102; /* Not enough resources to build */
	}
	return 1; /* Succesfull executed! */
}
function decreaseCurrentResources($player_id, $steel, $crystal, $erbium, $titanium) {
	global $table, $playerdata;
	$new = $playerdata;
	$new['res_steel'] = $playerdata['res_steel'] - $steel;
	$new['res_crystal'] = $playerdata['res_crystal'] - $crystal;
	$new['res_erbium'] = $playerdata['res_erbium'] - $erbium;
	$new['res_titanium'] = $playerdata['res_titanium'] - $titanium;
	updatePlayerData($player_id, $new);
}
function updatePlayerData($id, $new) {
	global $table;
	$sql_update = "UPDATE $table[players] SET `galaxy_id` = '$new[galaxy_id]', `galaxy_spot` = '$new[galaxy_spot]',
				`alliance_id` = '$new[alliance_id]', `res_steel` = '$new[res_steel]',
				`res_crystal` = '$new[res_crystal]', `res_erbium` = '$new[res_erbium]', `res_titanium` = '$new[res_titanium]',
				`roid_steel` = '$new[roid_steel]', `roid_crystal` = '$new[roid_crystal]', `roid_erbium` = '$new[roid_erbium]',
				`roid_unused` = '$new[roid_unused]',`score` = '$new[score]' WHERE `id` = '$id'";
	mysql_query($sql_update) or die(mysql_error());
}
function checkSteelResource($player_id, $resource_amount) {
	global $playerdata;
	if ($playerdata['res_steel'] < $resource_amount) { return false; }
	return true;
}
function checkCrystalResource($player_id, $resource_amount) {
	global $playerdata;
	if ($playerdata['res_crystal'] < $resource_amount) { return false; }
	return true;
}
function checkErbiumResource($player_id, $resource_amount) {
	global $playerdata;
	if ($playerdata['res_erbium'] < $resource_amount) { return false; }
	return true;
}
function checkTitaniumResource($player_id, $resource_amount) {
	global $playerdata;
	if ($playerdata['res_titanium'] < $resource_amount) { return false; }
	return true;
}
function checkProduction($player_id, $type_id, $item_id) {
	global $table;
	$sql_chkproduction = "SELECT `id` FROM $table[productions] WHERE `player_id` = '$player_id' AND `type_id` = '$type_id' AND `item_id` = '$item_id'";
	$res_chkproduction = mysql_query($sql_chkproduction);
	if (@mysql_num_rows($res_chkproduction) > 0) { return true; }
	return false;
}
function getProductionEta($player_id, $type_id, $item_id) {
	global $table;
	$sql_productioneta = "SELECT `ready_tick` FROM $table[productions] WHERE `player_id` = '$player_id' AND `type_id` = '$type_id' AND `item_id` = '$item_id'";
	$res_productioneta = mysql_query($sql_productioneta);
	if (@mysql_num_rows($res_productioneta) > 0) {
		$rec_productioneta = mysql_fetch_array($res_productioneta);
		$eta = $rec_productioneta['ready_tick'] - IB_TICK_CURRENT;
		return $eta;
	}
	return false;
}
function getTickStartTime() {
	global $table;
	$sql_gettick = "SELECT `start` FROM $table[tick] WHERE `id` = '1'";
	$rec_gettick = mysql_query($sql_gettick);
	$res = mysql_fetch_array($rec_gettick);
	return $res['start'];
}
function getCurrentTick() {
	global $table;
	$sql_gettick = "SELECT `current` FROM $table[tick] WHERE `id` = '1'";
	$rec_gettick = mysql_query($sql_gettick);
	$res = mysql_fetch_array($rec_gettick);
	return $res[current];
}
function getNextTickTime() {
	global $table;
	$sql_gettick = "SELECT `time_next` FROM $table[tick] WHERE `id` = '1'";
	$rec_gettick = mysql_query($sql_gettick);
	$res = mysql_fetch_array($rec_gettick);
	return $res['time_next'] - time();
}
function getLastTick() {
	global $table;
	$sql_gettick = "SELECT `last` FROM $table[tick] WHERE `id` = '1'";
	$res = mysql_fetch_array(mysql_query($sql_gettick));
	return $res[last];
}
function putCurrentTick($current) {
	global $table, $TICKER_INTERVAL;
	$time_next = time() + $TICKER_INTERVAL;
	mysql_query("UPDATE $table[tick] SET `current` = '$current', `time_next` = '$time_next' WHERE `id` = '1' LIMIT 1");
}
function getRulernameById($id) {
	global $table;
	$sql_name = "SELECT `rulername` FROM $table[players] WHERE `id` = '$id'";
	$rec_name = mysql_fetch_array(mysql_query($sql_name));
	return stripslashes($rec_name['rulername']);
}
function getPlanetnameById($id) {
	global $table;
	$sql_name = "SELECT `planetname` FROM $table[players] WHERE `id` = '$id'";
	$rec_name = mysql_fetch_array(mysql_query($sql_name));
	return stripslashes($rec_name['planetname']);
}
function getIdByUsername($username) {
	global $table;
	$sql_id = "SELECT `id` FROM $table[players] WHERE `username` = '$username'";
	$res_id = mysql_query($sql_id);
	if (@mysql_num_rows($res_id) < 1) { return false; }
	$rec_id = mysql_fetch_array($res_id);
	return $rec_id['id'];
}
function getIdByRulername($rulername) {
	global $table;
	$sql_id = "SELECT `id` FROM $table[players] WHERE `rulername` = '$rulername'";
	$res_id = mysql_query($sql_id);
	if (@mysql_num_rows($res_id) < 1) { return false; }
	$rec_id = mysql_fetch_array($res_id);
	return $rec_id['id'];
}
function getIdByPlanetname($planetname) {
	global $table;
	$sql_id = "SELECT `id` FROM $table[players] WHERE `planetname` = '$planetname'";
	$res_id = mysql_query($sql_id);
	if (@mysql_num_rows($res_id) < 1) { return false; }
	$rec_id = mysql_fetch_array($res_id);
	return $rec_id['id'];
}
function getIdByEmail($email) {
	global $table;
	$sql_id = "SELECT `id` FROM $table[players] WHERE `email` = '$email'";
	$res_id = mysql_query($sql_id);
	if (@mysql_num_rows($res_id) < 1) { return false; }
	$rec_id = mysql_fetch_array($res_id);
	return $rec_id['id'];
}
function getAllianceCommander($alliance_id) {
	global $table;
	$sql_id = "SELECT `founder_id` FROM $table[alliance] WHERE `id` = '$alliance_id'";
	$res_id = mysql_query($sql_id);
	if (@mysql_num_rows($res_id) < 1) { return false; }
	$rec_id = mysql_fetch_array($res_id);
	return $rec_id['founder_id'];
}
function getAllianceSubCommander($alliance_id) {
	global $table;
	$sql_id = "SELECT `subcommander_id` FROM $table[alliance] WHERE `id` = '$alliance_id'";
	$res_id = mysql_query($sql_id);
	if (@mysql_num_rows($res_id) < 1) { return false; }
	$rec_id = mysql_fetch_array($res_id);
	return $rec_id['subcommander_id'];
}
function getAllianceStartdate($alliance_id) {
	global $table;
	$sql_id = "SELECT `startdate` FROM $table[alliance] WHERE `id` = '$alliance_id'";
	$res_id = mysql_query($sql_id);
	if (@mysql_num_rows($res_id) < 1) { return false; }
	$rec_id = mysql_fetch_array($res_id);
	return $rec_id['startdate'];
}
function isPlayerMinister($player_id) {
	global $table;
	$sql_checkpos = "SELECT `id`, `moc_id`, `mow_id`, `moe_id` FROM $table[galaxy] WHERE `moc_id` = '$player_id' OR `mow_id` = '$player_id' OR `moe_id` = '$player_id'";
	$res_checkpos = mysql_query($sql_checkpos);
	$num_checkpos = mysql_num_rows($res_checkpos);
	if ($num_checkpos < 1) { return false; }
	else {
		$rec_checkpos = mysql_fetch_array($res_checkpos);
		if ($player_id	== $rec_checkpos['moc_id']) { $returnval = 'moc_id'; }
		if ($player_id	== $rec_checkpos['mow_id']) { $returnval = 'mow_id'; }
		if ($player_id	== $rec_checkpos['moe_id']) { $returnval = 'moe_id'; }
		return $returnval;
	}
	return false;
}
function getGalaxyCommander($galaxy_id) {
	global $table;
	$sql_galcom = "SELECT `commander_id` FROM $table[galaxy] WHERE `id` = $galaxy_id";
	$res_galcom = mysql_query($sql_galcom);
	$num_galcom = mysql_num_rows($res_galcom);
	if ($num_galcom < 1) { return 0; }
	else {
		$rec_galcom = mysql_fetch_array($res_galcom);
		return $rec_galcom['commander_id'];
	}
	return 0;
}
function setCommander($galaxy_id) {
	global $table;
	$sql_galmembers = "SELECT `id` FROM $table[players] WHERE `galaxy_id` = '$galaxy_id' ORDER BY `galaxy_spot`";
	$res_galmembers = mysql_query($sql_galmembers);
	//$totalmembers = mysql_num_rows($res_galmembers);

	$sql_totalvotes = "SELECT `id` FROM $table[politics] WHERE `galaxy_id` = '$galaxy_id'";
	//echo $sql_totalvotes;
	$res_totalvotes = mysql_query($sql_totalvotes);
	$num_totalvotes = mysql_num_rows($res_totalvotes);

	$requiredvotes = ceil($num_totalvotes / 2);
	if (($num_totalvotes % 2) == 0) { $requiredvotes++; }

	$counter = 0;
	$votedata = array(array());
	while ($rec_galmembers = mysql_fetch_array($res_galmembers)) {

		$sql_getvote = "SELECT `voted_on` FROM $table[politics] WHERE `galaxy_id` = '$galaxy_id' AND `player_id` = '$rec_galmembers[id]'";
		$rec_getvote = mysql_fetch_array(mysql_query($sql_getvote));

		$votedata[$counter][0] = $rec_galmembers['id'];
		$votedata[$counter][1] = $rec_getvote['voted_on'];
		$votedata[$counter][2] = 0;

		$counter++;
	}
	for ($f = 0; $f < count($votedata); $f++) {
		for ($g = 0; $g < count($votedata); $g++) {
			if ($votedata[$g][1] == $votedata[$f][0]) { $votedata[$f][2]++; }
		}
	}
	$highest_votes = array();
	for ($h = 0; $h < count($votedata); $h++) {
		if ($votedata[$h][2] > $highest_votes[2]) { $highest_votes = $votedata[$h]; }
	}
	if ($highest_votes[2] >= $requiredvotes) {
		$sql_findcommander = "SELECT `commander_id` FROM $table[galaxy] WHERE `id` = '$galaxy_id'";
		$rec_findcommander = mysql_fetch_array(mysql_query($sql_findcommander));
		if ($rec_findcommander['commander_id'] == $highest_votes[0]) { return true; }
		$sql_newcommander = "UPDATE $table[galaxy] SET `commander_id` = '$highest_votes[0]', `moc_id` = '0', `mow_id` = '0', `moe_id` = '0' WHERE `id` = '$galaxy_id'";
		addNews($highest_votes[0], 'Election', 'New Galactic Commander position', 'You have been elected as Galactic Commander from your galaxy.<br>Congratulations.');
	} else {
		$sql_findcommander = "SELECT `commander_id` FROM $table[galaxy] WHERE `id` = '$galaxy_id'";
		$rec_findcommander = mysql_fetch_array(mysql_query($sql_findcommander));
		if ($rec_findcommander['commander_id'] == 0) { return true; }
		addNews($rec_findcommander['commander_id'], 'Election', 'Lost Galactic Commander position', 'You lost your position as Galactic Commander for your galaxy.');
		$sql_newcommander = "UPDATE $table[galaxy] SET `commander_id` = '0', `moc_id` = '0', `mow_id` = '0', `moe_id` = '0'  WHERE `id` = '$galaxy_id'";
	}
	mysql_query($sql_newcommander);
	return true;
}

/* Returns galaxy id if the galaxy exist, else return false */
function checkGalaxy($x, $y) {
	global $table;
	$sql_galaxy = "SELECT `id` FROM $table[galaxy] WHERE `x` = '$x' AND `y` = '$y'";
	$res_galaxy = mysql_query($sql_galaxy);
	$rec_galaxy = mysql_fetch_array($res_galaxy);
	if (@mysql_num_rows($res_galaxy) < 1) { return false; }
	else { return $rec_galaxy['id']; }
}

/* Find galaxies and return galaxy ID where players spots are left. */
function getRandomGalaxyId() {
	global $table, $MAX_CLUSTER, $MAX_PLAYERS;
	$galaxy_data = array(array());
	$lowest_playercount = array();
	$lowest_playercount['id'] = 0;
	$lowest_playercount['total_players'] = 0;
	$i = 0;
	$sql_galaxies = "SELECT `id`, `x`, `y` FROM $table[galaxy] WHERE `private` = '0'";
	$res_galaxies = mysql_query($sql_galaxies) or die(mysql_error());
	if (@mysql_num_rows($res_galaxies) == 0) { return createGalaxies(5); } /* create new galaxies, because there are only private galaxies */
	while ($rec_galaxies = mysql_fetch_array($res_galaxies)) {
		$gid = $rec_galaxies['id'];
		if ($gid == 1) { continue; }
		$sql_galplayers = "SELECT `id`, `galaxy_id`, `galaxy_spot` FROM $table[players] WHERE `galaxy_id` = '$gid'";
		$res_galplayers = mysql_query($sql_galplayers);
		$galaxy_data[$i]['id'] = $gid;
		$galaxy_data[$i]['total_players'] = mysql_num_rows($res_galplayers);
		$i++;
	}

	foreach ($galaxy_data as $key => $row) {
		$id[$key]  = $row['id'];
		$total_players[$key] = $row['total_players'];
	}
	array_multisort($total_players, SORT_ASC, $galaxy_data);
	if ($galaxy_data[0]['total_players'] < $MAX_PLAYERS) {
		return $galaxy_data[0]['id'];
	} else {
		return createGalaxies(5);
	}
}
/* Create $num new galaxy's and return the first created id*/
function createGalaxies($num) {
	global $table, $MAX_CLUSTER;
	//$sql_galaxies = "SELECT `id`, `x`, `y` FROM $table[galaxy] ORDER BY `id` DESC";
	//$rec_galaxies = mysql_fetch_array(mysql_query($sql_galaxies));
	$x = 1;
	$y = 1;
	$firsttime_checker = 0;
	/* We dont have empty galaxies left, so lets create 5 new ones :-) */
	for ($j = 1; $j <= $num; $j++) {
		if ($y < $MAX_CLUSTER) {
			$x = $x;
			$y++;
		} else {
			$x++;
			$y = 1;
		}
		if (checkGalaxy($x, $y)) { $j--; continue; }
		$sql_newgalaxy = "INSERT INTO $table[galaxy] (`id`, `x`, `y`, `topic`, `image_url`)
							VALUES ('', '$x', '$y', 'Imperial Battle', '')";
		mysql_query($sql_newgalaxy) or die(mysql_error());
		if ($firsttime_checker == 0) { $return = mysql_insert_id(); }
		$firsttime_checker++;
	}
	return $return;
}

function getFreeGalaxySpot($galaxy_id) {
	global $table;
	global $MAX_PLAYERS;
	for ($i = 1; $i <= $MAX_PLAYERS; $i++) {
		$sql_galplayers = "SELECT `id`, `galaxy_id`, `galaxy_spot` FROM $table[players] WHERE `galaxy_id` = '$galaxy_id' AND `galaxy_spot` = '$i'";
		$res_galplayers = mysql_query($sql_galplayers);
		if (@mysql_num_rows($res_galplayers) == 0) { return $i; }
	}
	/* should never happen, unless you supply a full galaxy :) */
	return false;
}

function getShipsOnFleet($player_id, $ship_id, $fleet) {
	global $table;
	$sql = "SELECT `$fleet` FROM $table[fleet] WHERE `player_id` = '$player_id' AND `ship_id` = '$ship_id'";
	$rec = mysql_fetch_array(mysql_query($sql));
	return $rec[$fleet];
}
function moveShips($player_id, $ship_id, $amount, $from, $to) {
	global $table;
	$from_current_amount = getShipsOnFleet($player_id, $ship_id, $from);
	$to_current_amount = getShipsOnFleet($player_id, $ship_id, $to);
	$from_new_amount = $from_current_amount - $amount;
	$to_new_amount = $to_current_amount + $amount;
	$sql_updfleet = "UPDATE $table[fleet] SET `$from` = '$from_new_amount', `$to` = '$to_new_amount' WHERE `player_id` = '$player_id' AND `ship_id` = '$ship_id'";
	mysql_query($sql_updfleet);
}

/* Returns the player_id accodrding to the X, Y and Z coordinates given. */
function getPlayerId($x, $y, $z) {
	global $table;
	$sql_playerid = "SELECT $table[players].id FROM $table[players] LEFT JOIN $table[galaxy] ON $table[players].galaxy_id = $table[galaxy].id WHERE $table[galaxy].x = '$x' AND $table[galaxy].y = '$y' AND $table[players].galaxy_spot = '$z'";
	$rec_playerid = mysql_query($sql_playerid);
	$num_playerid = mysql_num_rows($rec_playerid);
	if ($num_playerid > 0) {
		$res_playerid = mysql_fetch_assoc($rec_playerid);
		return $res_playerid['id'];
	} else {
		return 0;
	}
	return 0;
}

/* Returns an array with the X, Y and Z coordinates of the player_id given. */
function getXYZ($player_id) {
	global $table;
	$coords = array();
	$sql_xyz = "SELECT $table[galaxy].x AS x, $table[galaxy].y AS y, $table[players].galaxy_spot AS z
				FROM `$table[galaxy]`
				INNER JOIN `$table[players]` ON $table[galaxy].id = $table[players].galaxy_id
				WHERE $table[players].id = '$player_id'";
	$res_xyz = mysql_query($sql_xyz);
	if (@mysql_num_rows($res_xyz) == 0) { return false; }
	$rec_xyz = mysql_fetch_array($res_xyz);
	$coords[0] = $rec_xyz['x'];
	$coords[1] = $rec_xyz['y'];
	$coords[2] = $rec_xyz['z'];
	return $coords;
}

function getXY($galaxy_id) {
	global $table;
	$sql = "SELECT `x`, `y` FROM $table[galaxy] WHERE `id` = '$galaxy_id'";
	$res = mysql_query($sql);
	$num = @mysql_num_rows($res);
	if ($num > 0) {
		return mysql_fetch_assoc($res);
	} else { 
		return false;
	}
}
function getGalaxyTopic($galaxy_id) {
	global $table;
	$sql = "SELECT `topic` FROM $table[galaxy] WHERE `id` = '$galaxy_id'";
	$res = mysql_query($sql);
	$num = @mysql_num_rows($res);
	if ($num > 0) {
		$rec = mysql_fetch_assoc($res);
		return $rec['topic'];
	} else { 
		return false;
	}
}

function addNews($player_id, $category, $subject, $text) {
	global $table;
	$date = time();
	$sql_addnews = "INSERT INTO $table[playernews] (`player_id` , `subject` , `text` , `category` , `date`)
					VALUES ('$player_id', '$subject', '$text', '$category', '$date')";
	mysql_query($sql_addnews);
}

function getAllianceTag($player_id) {
	global $table;
	$sql_alliance = "SELECT $table[alliance].tag FROM $table[alliance] INNER JOIN $table[players] WHERE $table[players].alliance_id = $table[alliance].id AND $table[players].id = $player_id";
	$rec_alliance = mysql_fetch_array(mysql_query($sql_alliance));
	return $rec_alliance['tag'];
}
function getAllianceName($player_id) {
	global $table;
	$sql_alliance = "SELECT $table[alliance].name FROM $table[alliance] INNER JOIN $table[players] WHERE $table[players].alliance_id = $table[alliance].id AND $table[players].id = $player_id";
	$rec_alliance = mysql_fetch_array(mysql_query($sql_alliance));
	return $rec_alliance['name'];
}
function getAllianceTageById($alliance_id) {
	global $table;
	$sql_alliance = "SELECT tag FROM $table[alliance] WHERE id = $alliance_id";
	$rec_alliance = mysql_fetch_array(mysql_query($sql_alliance));
	return $rec_alliance['tag'];
}
function getAllianceNameById($alliance_id) {
	global $table;
	$sql_alliance = "SELECT name FROM $table[alliance] WHERE id = $alliance_id";
	$rec_alliance = mysql_fetch_array(mysql_query($sql_alliance));
	return $rec_alliance['name'];
}

function getTitaniumFactoryId($player_id) {
	global $table;
	$sql_factory = "SELECT `id` FROM $table[titanium_factory] WHERE `player_id` = '$player_id'";
	$res_factory = mysql_query($sql_factory);
	$num_factory = mysql_num_rows($res_factory);
	if ($num_factory > 0) {
		$rec_factory = mysql_fetch_array($res_factory);
		return $rec_factory['id'];
	} else {
		return false;
	}
	return false;
}

/* Secures the data, since we dont trust our users :) */
function secureData($data) {
	$data = addslashes($data);
	$data = strip_tags($data);
	$data = htmlspecialchars($data);
	return $data;
}
function updatePlayerLoginData($player_id, $ip, $hostname) {
	global $table;
	$time = time();
	$sql_updlogintime = "UPDATE $table[players] SET `ip` = '$ip', `hostname` = '$hostname', `lastlogin` = '$time' WHERE `id` = '$player_id'";
	mysql_query($sql_updlogintime) or die(mysql_error());
}
function getShipProperty($ship_id, $property) {
	global $table;
	$sql = "SELECT `$property` FROM $table[ships] WHERE `id` = '$ship_id'";
	$rec = mysql_fetch_assoc(mysql_query($sql));
	if ($rec[$property]) { return $rec[$property]; }
	else { return 'n/a'; }
}
function getFleetShipProperty($ship_id, $property) {
	global $table;
	$sql = "SELECT `$property` FROM $table[playerfleet_ships] WHERE `ship_id` = '$ship_id'";
	$rec = mysql_fetch_assoc(mysql_query($sql));
	return $rec[$property];
}
function inFleet($fleet_id, $ship_id) {
	global  $table;
	$sql = "SELECT `id` FROM $table[playerfleet_ships] WHERE `fleet_id` = '$fleet_id' AND `ship_id` = '$ship_id'";
	$rec = mysql_query($sql);
	$num = mysql_num_rows($rec);
	if ($num > 0) { return true; }
	return false;
}
function inFleetShipCheck($fleet_id, $current_ship, $selected_ship) {
	if ($current_ship == $selected_ship) {
		return inFleet($fleet_id, $selected_ship);
	} else {
		return false;
	}
}
function getBaseShips($player_id, $ship_id) {
	global $table;
	$baseships = 0;
	$sql_baseships = "SELECT `amount` FROM $table[playerunit] WHERE `player_id` = '$player_id' AND `unit_id` = '$ship_id'";
	$rec_baseships = mysql_query($sql_baseships);
	$num_baseships = mysql_num_rows($rec_baseships);
	if ($num_baseships > 0) {
		while ($res_baseships = mysql_fetch_assoc($rec_baseships)) {
			$baseships += $res_baseships['amount'];
		}
	}
	$baseships -= getAllFleetships($player_id, $ship_id);
	return $baseships;
}
function getAllFleetships($player_id, $ship_id) {
	global $table;
	$allfleetships = 0;
	$sql_allfleetships = "SELECT `amount` FROM $table[playerfleet_ships] WHERE `player_id` = '$player_id' AND `ship_id` = '$ship_id'";
	$rec_allfleetships = mysql_query($sql_allfleetships);
	$num_allfleetships = mysql_num_rows($rec_allfleetships);
	if ($num_allfleetships > 0) {
		while ($res_allfleetships = mysql_fetch_assoc($rec_allfleetships)) {
			$allfleetships += $res_allfleetships['amount'];
		}
	}
	return $allfleetships;
}
function getFleetShips($player_id, $fleet_id, $ship_id) {
	global $table;
	$fleetships = 0;
	$sql_fleetships = "SELECT `amount` FROM $table[playerfleet_ships] WHERE `player_id` = '$player_id' AND `fleet_id` = '$fleet_id' AND `ship_id` = '$ship_id'";
	$rec_fleetships = mysql_query($sql_fleetships);
	$num_fleetships = mysql_num_rows($rec_fleetships);
	if ($num_fleetships > 0) {
		while ($res_fleetships = mysql_fetch_assoc($rec_fleetships)) {
			$fleetships += $res_fleetships['amount'];
		}
	}
	return $fleetships;
}
function getFleetShipAmount($player_id, $fleet_id) {
	global $table;
	$fleetships = 0;
	$sql_fleetships = "SELECT `amount` FROM $table[playerfleet_ships] WHERE `player_id` = '$player_id' AND `fleet_id` = '$fleet_id'";
	$rec_fleetships = mysql_query($sql_fleetships);
	$num_fleetships = mysql_num_rows($rec_fleetships);
	if ($num_fleetships > 0) {
		while ($res_fleetships = mysql_fetch_assoc($rec_fleetships)) {
			$fleetships += $res_fleetships['amount'];
		}
	}
	return $fleetships;
}
function getFleetAmount($player_id) {
	global $table;
	$sql = "SELECT `id` FROM $table[playerfleet] WHERE `player_id` = '$player_id'";
	$rec = mysql_query($sql);
	$num = mysql_num_rows($rec);
	return $num;
}
function isFleetHome($player_id, $fleet_id) {
	global $table;
	$sql = "SELECT `id`, `action`, `target_id`, `action_start`, `action_time` FROM $table[playerfleet] WHERE `player_id` = '$player_id' AND `id` = '$fleet_id'";
	$rec = mysql_query($sql);
	$num = mysql_num_rows($rec);
	if ($num > 0) {
		$rec = mysql_fetch_assoc($rec);
		if (($rec['action'] == 'home') && ($rec['target_id'] == 0) && (($rec['action_start'] == 0) || ($rec['action_start'] == getCurrentTick()))) {
			return true;
		} else {
			return false;
		}
	}
	else { return false; }
}
function getFleetActionStart($fleet_id) {
	global $table;
	$sql = "SELECT `action_start` FROM $table[playerfleet] WHERE `id` = '$fleet_id'";
	$res = mysql_query($sql);
	$num = mysql_num_rows($res);
	if ($num > 0) {
		$rec = mysql_fetch_assoc($res);
		return $rec['action_start'];
	} else {
		return 0;
	}
}


/*function getCurrentTick() {
global $table;
$sql_gettick = "SELECT `current` FROM $table[tick] WHERE `id` = '1'";
$rec_gettick = mysql_query($sql_gettick);
$res = mysql_fetch_array($rec_gettick);
return $res['current'];
}
function getLastTick() {
global $table;
$sql_gettick = "SELECT `last` FROM $table[tick] WHERE `id` = '1'";
$res = mysql_fetch_array(mysql_query($sql_gettick));
return $res['last'];
}
*/
/*************************************************
PLAYER DATA FUNCTIONS
*************************************************/

function checkProductions($player_id, $currenttick) {
	global $table;
	$sql_proddata = "SELECT `id`, `player_id`, `type_id`, `item_id`, `amount`, `ready_tick` FROM $table[productions] WHERE `player_id` = '$player_id'";
	$res_proddata = mysql_query($sql_proddata);
	if (@mysql_num_rows($res_proddata) > 0) {
		while ($rec_proddata = mysql_fetch_array($res_proddata)) {
			if ($rec_proddata['ready_tick'] <= $currenttick) {
				$sql_proddone = "DELETE FROM $table[productions] WHERE `id` = '$rec_proddata[id]'";
				mysql_query($sql_proddone);
				if (($rec_proddata['type_id'] == 1) || ($rec_proddata['type_id'] == 2)) {
					$sql_upditem = "INSERT INTO $table[playeritem] (`id`, `player_id`, `type_id`, `item_id`, `amount`) VALUES ('', '$player_id', '$rec_proddata[type_id]', '$rec_proddata[item_id]', '1')";
					$sql_itemname = "SELECT `name` FROM $table[items] WHERE `id` = '$rec_proddata[item_id]'";
					$rec_itemname = mysql_fetch_array(mysql_query($sql_itemname));
					if ($rec_proddata['type_id'] == 1) {
						$category = 'Construction';
						$subject = 'Construction ready.';
						$text = 'The construction crew informed you about the '.$rec_itemname['name'].' construction being ready.';
					}
					if ($rec_proddata['type_id'] == 2) {
						$category = 'Research';
						$subject = 'Research done.';
						$text = 'The engineers from your planet informed you about new research results.<br>The research about '.$rec_itemname['name'].' has been completed succesfully';
					}
					addnews($player_id, $category, $subject, $text);
					mysql_query($sql_upditem);
				} elseif (($rec_proddata['type_id'] == 3) || ($rec_proddata['type_id'] == 4)) {
					if (checkUnit($player_id, $rec_proddata['item_id'])) {
						$sql_amountitem = "SELECT `id`, `amount` FROM $table[playerunit] WHERE `unit_id` = '$rec_proddata[item_id]' AND `player_id` = '$player_id'";
						$rec_amountitem = mysql_fetch_array(mysql_query($sql_amountitem));
						$new_amountitem = $rec_amountitem['amount'] + $rec_proddata['amount'];
						$sql_upditem = "UPDATE $table[playerunit] SET `amount` = '$new_amountitem' WHERE `id` = '$rec_amountitem[id]'";

						mysql_query($sql_upditem);
					} else {
						$sql_upditem = "INSERT INTO $table[playerunit] ( `id` , `player_id` , `type_id` , `unit_id` , `amount` )
									VALUES ('', '$player_id', '$rec_proddata[type_id]', '$rec_proddata[item_id]', '$rec_proddata[amount]')";
						$sql_addfleet = "INSERT INTO $table[fleet] ( `id` , `player_id` , `ship_id` , `base_cloacked` , `base_uncloacked` , `fleet_1` , `fleet_2` , `fleet_3` )
										VALUES ('', '$player_id', '$rec_proddata[item_id]', '$rec_proddata[amount]', '0', '0', '0', '0')";
						mysql_query($sql_upditem);
						mysql_query($sql_addfleet);
					}
				}
			}
		}
	}
}
function checkUnit($player_id, $unit_id) {
	global $table;
	$sql = "SELECT `id` FROM $table[playerunit] WHERE `unit_id` = '$unit_id' AND `player_id` = '$player_id'";
	$res = mysql_query($sql);
	$num = @mysql_num_rows($res);
	if ($num > 0) { return true; }
	else { return false; }
	return false;
}
function getPlayerProperty($player_id, $property) {
	global $table;
	$sql = "SELECT $property FROM $table[players] WHERE `id` = '$player_id'";
	$res = mysql_query($sql);
	$num = @mysql_num_rows($res);
	if ($num > 0) {
		$rec = mysql_fetch_assoc($res);
		return $rec[$property];
	} else {
		return false;
	}
}
function getMOC($galaxy_id) {
	global $table;
	$sql = "SELECT `moc_id` FROM `$table[galaxy]` WHERE `id` = '$galaxy_id'";
	$res = mysql_query($sql);
	if (@mysql_num_rows($res) > 0) {
		$rec = mysql_fetch_assoc($res);
		return $rec['moc_id'];
	}
	return false;
}
function getMOW($galaxy_id) {
	global $table;
	$sql = "SELECT `mow_id` FROM `$table[galaxy]` WHERE `id` = '$galaxy_id'";
	$res = mysql_query($sql);
	if (@mysql_num_rows($res) > 0) {
		$rec = mysql_fetch_assoc($res);
		return $rec['mow_id'];
	}
	return false;
}
function getMOE($galaxy_id) {
	global $table;
	$sql = "SELECT `moe_id` FROM `$table[galaxy]` WHERE `id` = '$galaxy_id'";
	$res = mysql_query($sql);
	if (@mysql_num_rows($res) > 0) {
		$rec = mysql_fetch_assoc($res);
		return $rec['moe_id'];
	}
	return false;
}
function generateBattleReports($target_id, $shipdata, $af, $df) {
	$af_pids = $af->getPlayerIds();
	foreach ($af_pids as $pid) {
		$br_a = "";
		$br_a = getBRHtmlStart($target_id, 'Attackers', true);
		$br_d = "";
		$br_d = getBRHtmlStart($target_id, 'Defenders', false);
		for ($i = 0; $i < count($shipdata); $i++) {
			$total_amount = $af->getTotalShipOldAmount($shipdata[$i]['id']);
			$total_losses = $af->getTotalShipLosses($shipdata[$i]['id']);

			$shipcol = $af->getPlayerShipCollection($shipdata[$i]['id'], $pid);
			if ($shipcol) {
				if ($shipcol->getLostShips() == 0) {
					$your_amount  = $shipcol->getAmount();
				} else {
					$your_amount  = $shipcol->getOldAmount();
				}
				$your_losses = $shipcol->getLostShips();
			} else {
				$your_amount  = 0;
				$your_losses = 0;
			}
			$br_a .= getBRHtml($shipdata[$i]['name'], true, $total_amount, $total_losses, $your_amount, $your_losses);
			if ($df) {
				$total_amount = $df->getTotalShipOldAmount($shipdata[$i]['id']);
				$total_losses = $df->getTotalShipLosses($shipdata[$i]['id']);
				$br_d .= getBRHtml($shipdata[$i]['name'], false, $total_amount, $total_losses, 0, 0);
			}
		}
		$br_a .= getBRHtmlEnd();
		$br_d .= getBRHtmlEnd();
		$complete_battlereport = $br_a.'<br />'.$br_d;
		addNews($pid, 'Combat', 'Battle report', $complete_battlereport);
	}


	if ($df) {
		$df_pids = $df->getPlayerIds();
		if (!in_array($target_id, $df_pids)) {
			array_push($df_pids, $target_id);
		}
		foreach ($df_pids as $pid) {
			$br_a = "";
			$br_a = getBRHtmlStart($target_id, 'Attackers', false);
			$br_d = "";
			$br_d = getBRHtmlStart($target_id, 'Defenders', true);
			for ($i = 0; $i < count($shipdata); $i++) {
				$total_amount = $df->getTotalShipOldAmount($shipdata[$i]['id']);
				$total_losses = $df->getTotalShipLosses($shipdata[$i]['id']);

				$shipcol = $df->getPlayerShipCollection($shipdata[$i]['id'], $pid);
				if ($shipcol) {
					if ($shipcol->getLostShips() == 0) {
						$your_amount  = $shipcol->getAmount();
					} else {
						$your_amount  = $shipcol->getOldAmount();
					}
					$your_losses = $shipcol->getLostShips();
				} else {
					$your_amount  = 0;
					$your_losses = 0;
				}
				$br_d .= getBRHtml($shipdata[$i]['name'], true, $total_amount, $total_losses, $your_amount, $your_losses);
				if ($af) {
					$total_amount = $af->getTotalShipOldAmount($shipdata[$i]['id']);
					$total_losses = $af->getTotalShipLosses($shipdata[$i]['id']);
					$br_a .= getBRHtml($shipdata[$i]['name'], false, $total_amount, $total_losses, 0, 0);
				}
			}
			$br_a .= getBRHtmlEnd();
			$br_d .= getBRHtmlEnd();
			$complete_battlereport = $br_d.'<br />'.$br_a;
			addNews($pid, 'Combat', 'Battle report', $complete_battlereport);
		}
	}
}

function getBRHtmlStart($player_id, $type, $mytype) {
	$c = getXYZ($player_id);
	$html = '<table border="0" width="100%" style="border: 1px solid #3C5762">
	<tr>
		<td width="595" colspan="5"><b>'.$type.' at '.$c[0].':'.$c[1].':'.$c[2].' ('.getPlayerProperty($player_id, 'rulername').' of '.getPlayerProperty($player_id, 'planetname').')</b></td>
	</tr>
	<tr>
		<td width="250">Ship name</td>
		<td width="85">Total amount</td>';
	if ($mytype) {
		$html .= '
				<td width="85">Total losses</td>
				<td width="85">Your amount</td>		
				<td width="85">Your losses</td>
			</tr>';
	} else {
		$html .= '
			<td width="255" colspan="3">Total losses</td>
		</tr>';
	}

	return $html;
}
function getBRHtml($ship, $mytype, $total_amount, $total_losses, $your_amount, $your_losses) {
	$html = '
	<tr>
		<td>'.$ship.'</td>
		<td>'.$total_amount.'</td>';
	if ($mytype) {
		$html .='
			<td>'.$total_losses.'</td>
			<td>'.$your_amount.'</td>	
			<td>'.$your_losses.'</td>
		</tr>';
	} else {
		$html .= '
			<td colspan="3">'.$total_losses.'</td>
		</tr>';
	}
	return $html;
}
function getBRHtmlEnd() {
	$html = '</table>';
	return $html;
}

function getARHtmlStart() {
	$html =
	'<table border="0" width="100%" style="border: 1px solid #3C5762">
			<tr>
				<td colspan="3"><b>Asteroid losses</b></td>
			</tr>
			<tr>
				<td>Asteroid resource type</td>
				<td>Old amount</td>
				<td>Lost amount</td>
			</tr>';
	return $html;
}
function getARHtml($type, $old, $new) {

	$html = '
		<tr>
			<td>'.$type.'</td>
			<td>'.$old.'</td>
			<td>'.$new.'</td>
		</tr>';
	return $html;
}
function getARHtmlEnd() {
	$html = '</table>';
	return $html;
}
function appendAdminLog($desc, $action) {
	global $table;
	if ($_SERVER['REMOTE_USER'] == '') {
		$user = 'anonymous';
	} else {
		$user = $_SERVER['REMOTE_USER'];
	}
	$ip = $_SERVER['REMOTE_ADDR'];
	$time = time();
	$sql_append = "INSERT INTO `$table[adminlog]` (`timestamp`, `username`, `ip`, `description`, `fulldesc`)
					VALUES ('$time', '$user', '$ip', '$desc', '$action')";
	mysql_query($sql_append) or die(mysql_error());
	return true;
}
function appendPlayerLog($player_id, $desc, $action = '') {
	/*
	DISABLED, is toch maar database vervuiling als er geen bugs zijn.

	global $table, $current;

	$time = time();
	$sql_append = "INSERT INTO `$table[playerlog]` (`timestamp`, `tick`, `player_id`, `description`, `fulldesc`)
	VALUES ('$time', '$current', '$player_id', '$desc', '$action')";

	mysql_query($sql_append) or die(mysql_error());
	*/
	return true;
}
?>