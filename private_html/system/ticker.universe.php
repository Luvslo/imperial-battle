<?
/* Getting top 100 users. */
$sql_gettop = "SELECT `id`, `rulername`, `planetname`, `roid_steel` , `roid_crystal`, `roid_erbium`, `roid_unused`, `score` FROM $table[players] ORDER BY `score` DESC LIMIT 30";
$res_gettop = mysql_query($sql_gettop);
mysql_query("TRUNCATE TABLE $table[universe]");
while ($rec_gettop = mysql_fetch_array($res_gettop)) {
	$player_id = $rec_gettop['id'];
	$score = $rec_gettop['score'];
	$rulername = $rec_gettop['rulername'];
	$planetname = $rec_gettop['planetname'];
	$tag = getAllianceTag($rec_gettop['id']);
	$asteroids = $rec_gettop['roid_steel'] + $rec_gettop['roid_crystal'] + $rec_gettop['roid_erbium'] + $rec_gettop['roid_unused'];
	$sql_newtop = "INSERT INTO $table[universe] (`player_id`, `rulername`, `planetname`, `tag`, `score`, `asteroids`) VALUES ('$player_id', '$rulername', '$planetname', '$tag', '$score', '$asteroids')";
	mysql_query($sql_newtop);
}




/* Getting top 10 galaxies. */
$galaxytop = array(array());
$sql_topgalaxy = "SELECT `id` FROM $table[galaxy]";
$res_topgalaxy = mysql_query($sql_topgalaxy);
$i = 0;
while ($rec_topgalaxy = mysql_fetch_array($res_topgalaxy)) {
	$galaxytop[$i]['id'] = 0;
	$galaxytop[$i]['score'] = 0;
	$galaxytop[$i]['asteroids'] = 0;
	$sql_galaxyplayers = "SELECT `roid_steel`, `roid_crystal`, `roid_erbium`, `roid_unused`, `score` FROM $table[players] WHERE `galaxy_id` = '$rec_topgalaxy[id]'";
	$res_galaxyplayers = mysql_query($sql_galaxyplayers);
	while ($rec_galaxyplayers = mysql_fetch_array($res_galaxyplayers)) {
		$galaxytop[$i]['id'] = $rec_topgalaxy['id'];
		$galaxytop[$i]['total_members'] = mysql_num_rows($res_galaxyplayers);
		$galaxytop[$i]['score'] += $rec_galaxyplayers['score'];
		$galaxytop[$i]['asteroids'] += $rec_galaxyplayers['roid_steel'] + $rec_galaxyplayers['roid_crystal'] + $rec_galaxyplayers['roid_erbium'] + $rec_galaxyplayers['roid_unused'];
	}
	$i++;
}
$score = array();
foreach ($galaxytop as $key => $val) {
	$score[$key] = $val['score'];
}
array_multisort($score, SORT_DESC, $galaxytop);

if (count($galaxytop) <= 10) { $max_count = count($galaxytop); }
else { $max_count = 10; }
mysql_query("TRUNCATE TABLE $table[universe_galaxy]");
for ($j = 0; $j < $max_count; $j++) {
	$galaxy_id = $galaxytop[$j]['id'];
	$total_members = $galaxytop[$j]['total_members'];
	$score = $galaxytop[$j]['score'];
	$asteroids = $galaxytop[$j]['asteroids'];
	$gal_xy = getXY($galaxy_id);
	$topic = getGalaxyTopic($galaxy_id);
	$sql_newgalaxytop = "INSERT INTO $table[universe_galaxy] (`galaxy_id`, `x`, `y`, `topic`, `total_members`, `score`, `asteroids`) 
						VALUES ('$galaxy_id', '$gal_xy[x]', '$gal_xy[y]', '$topic', '$total_members', '$score', '$asteroids')";
	mysql_query($sql_newgalaxytop) or die(mysql_error());
}





/* Getting top 10 alliances. */
$alliancetop = array(array());
$sql_topalliance = "SELECT `id` FROM $table[alliance]";
$res_topalliance = mysql_query($sql_topalliance);
$i = 0;
while ($rec_topalliance = mysql_fetch_array($res_topalliance)) {
	$alliancetop[$i]['id'] = 0;
	$alliancetop[$i]['score'] = 0;
	$alliancetop[$i]['asteroids'] = 0;
	$sql_allianceplayers = "SELECT `roid_steel`, `roid_crystal`, `roid_erbium`, `roid_unused`, `score` FROM $table[players] WHERE `alliance_id` = '$rec_topalliance[id]'";
	$res_allianceplayers = mysql_query($sql_allianceplayers);
	while ($rec_allianceplayers = mysql_fetch_array($res_allianceplayers)) {
		$alliancetop[$i]['id'] = $rec_topalliance['id'];
		$alliancetop[$i]['total_members'] = mysql_num_rows($res_allianceplayers);
		$alliancetop[$i]['score'] += $rec_allianceplayers['score'];
		$alliancetop[$i]['asteroids'] += $rec_allianceplayers['roid_steel'] + $rec_allianceplayers['roid_crystal'] + $rec_allianceplayers['roid_erbium'] + $rec_allianceplayers['roid_unused'];
	}
	$i++;
}
$score = array();
foreach ($alliancetop as $key => $val) {
	$score[$key] = $val['score'];
}
array_multisort($score, SORT_DESC, $alliancetop);

if (count($alliancetop) <= 10) { $max_count = count($alliancetop); }
else { $max_count = 10; }
mysql_query("TRUNCATE TABLE $table[universe_alliance]");
for ($j = 0; $j < $max_count; $j++) {
	if ($alliancetop[$j]['total_members'] == 0) { continue; }
	$name = getAllianceNameById($alliancetop[$j]['id']);
	$tag = getAllianceTageById($alliancetop[$j]['id']);
	$total_members = $alliancetop[$j]['total_members'];
	$score = $alliancetop[$j]['score'];
	$asteroids = $alliancetop[$j]['asteroids'];
	$sql_newalliancetop = "INSERT INTO $table[universe_alliance] (`name`, `tag`, `total_members`, `score`, `asteroids`) 
						VALUES ('$name', '$tag', '$total_members', '$score', '$asteroids')";
	mysql_query($sql_newalliancetop) or die(mysql_error());
}
?>
