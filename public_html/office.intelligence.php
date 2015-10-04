<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}

if (!isset($do)) { $act = secureData($_GET['do']); }
if (!isset($do)) { $act = secureData($_POST['do']); }
$error = 0;

if ($do == 'asteroidscan') {
	$scans_amount = secureData(strip_tags($_POST['scans_amount']));
	if ($scans_amount <= 0) {
		$error = 100;
		$scans_amount = 0;
	}

	if (!checkItem($playerdata['id'], $BASIC_INTELLIGENCE)) {
		$error = 102;
	}
	if ($error < 99) {
		$crystal_amount = $scans_amount  * 2000;
		if ($playerdata['res_crystal'] < $crystal_amount) {
			$scans_amount = floor($playerdata['res_crystal'] / 2000);
			$crystal_amount = $scans_amount  * 2000;
		}
		$total_roids = $playerdata['roid_steel'] + $playerdata['roid_crystal'] + $playerdata['roid_erbium'] + $playerdata['roid_unused'];
		if ($total_roids < 1) { $find_chance = (60000/1); }
		else { $find_chance = (60000/$total_roids); }

		$found_roids = 0;
		for ($i = 1; $i <= $scans_amount; $i++) {
			$rand = rand(10,6000);
			if ($rand <= $find_chance) {
				$found_roids++;
				$total_roids++;
				$find_chance = (60000/$total_roids);
			}
		}
		$playerdata['roid_unused'] += $found_roids;
		$playerdata['res_crystal'] -= $crystal_amount;
		updatePlayerdata($playerdata['id'], $playerdata);

		if ($found_roids > 0) {
			$error = 0;
			$msg = 'The intelligence unit found <b>'.$found_roids.'</b> asteroids using '.$scans_amount.' scans for your planet.';
		} else {
			$error = 1;
			$msg_color = 'red';
			$msg = 'The intelligence unit failed while finding asteroids using '.$scans_amount.' scans for your planet.';
		}
	}
}
if ($do == 'globalscan') {
	$x = secureData($_POST['x']);
	$y = secureData($_POST['y']);
	$z = secureData($_POST['z']);
	$target_id = getPlayerId($x, $y, $z);
	if ($target_id <= 0) { $error = 103; }
	if ($playerdata['res_crystal'] < 2700) { $error = 104; }
	if (!checkItem($playerdata['id'], $ADVANCED_INTELLIGENCE)) { $error = 105; }
	if ($error < 100) {
		$playerdata['res_crystal'] -= 2700;
		updatePlayerData($playerdata['id'], $playerdata);
	}
}
if ($do == 'newsscan') {
	$x = secureData($_POST['x']);
	$y = secureData($_POST['y']);
	$z = secureData($_POST['z']);
	$target_id = getPlayerId($x, $y, $z);
	if ($target_id <= 0) { $error = 103; }
	if ($playerdata['res_crystal'] < 5000) { $error = 104; }
	if (!checkItem($playerdata['id'], $NG_INTELLIGENCE)) { $error = 106; }
	if ($error < 100) {
		$playerdata['res_crystal'] -= 5000;
		updatePlayerData($playerdata['id'], $playerdata);
	}
}
if ($do == 'uniscan') {
	$findcluster = secureData($_POST['findcluster']);
	$order = secureData($_POST['order']);
	$excl_galmem = secureData($_POST['excl_galmem']);
	$excl_allmem = secureData($_POST['excl_allmem']);
	$minscore = secureData($_POST['minscore']);
	$maxscore = secureData($_POST['maxscore']);
	$minasteroids = secureData($_POST['minasteroids']);
	$maxasteroids = secureData($_POST['maxasteroids']);

	if ($minasteroids > $maxasteroids) { $error = 112; }
	if ($mindscore > $maxscore) { $error = 113; }

	if (($maxscore <= 0) || (!is_numeric($maxscore))){ $error = 108; }
	if (($maxasteroids <= 0) || (!is_numeric($maxasteroids))){ $error = 109; }

	if (($minasteroids < 0) || (!is_numeric($minasteroids))){ $error = 110; }
	if (($minscore < 0) || (!is_numeric($minscore))){ $error = 111; }

	if ($playerdata['res_crystal'] < 15000) { $error = 104; }
	if (!checkItem($playerdata['id'], $UNI_INTELLIGENCE)) { $error = 107; }

	if ($error < 100) {
		$playerdata['res_crystal'] -= 15000;
		updatePlayerData($playerdata['id'], $playerdata);

		$sql_excl = '';
		$sql_order = '';
		$sql_cluster = '';

		if (!empty($excl_galmem) && empty($excl_allmem)) { $sql_excl = "AND (`galaxy_id` != '$playerdata[galaxy_id]')"; }
		if (empty($excl_galmem) && !empty($excl_allmem)) { $sql_excl = "AND (`alliance_id` != '$playerdata[alliance_id]')"; }
		if (!empty($excl_galmem) && !empty($excl_allmem)) { $sql_excl = "AND ((`galaxy_id` != '$playerdata[galaxy_id]') AND (`alliance_id` != '$playerdata[alliance_id]'))"; }

		switch ($order) {
			default:
			case 'score':
			$sql_order = "ORDER BY score DESC";
			break;
			case 'asteroid':
			$sql_order = "ORDER BY total_asteroids DESC";
			break;
			case 'xyz':
			$sql_order = "ORDER BY x ASC, y ASC, z ASC";
			break;
		}
		if ($findcluster > 0) {
			$sql_cluster = "AND ($table[galaxy].x = '$findcluster')";
		}
		$sql_scanuni = "
				SELECT 
					$table[players].id, $table[players].rulername, $table[players].planetname, $table[players].score,
					($table[players].roid_steel + $table[players].roid_crystal + $table[players].roid_erbium + $table[players].roid_unused) AS total_asteroids,
					$table[players].galaxy_id, $table[players].alliance_id,
					$table[galaxy].x AS x, $table[galaxy].y AS y, $table[players].galaxy_spot AS z
				FROM $table[players]
				INNER JOIN $table[galaxy] 
					ON $table[galaxy].id = $table[players].galaxy_id
				WHERE
					(score >= '$minscore' AND score <= '$maxscore') AND
					(($table[players].roid_steel + $table[players].roid_crystal + $table[players].roid_erbium + $table[players].roid_unused) >= '$minasteroids' AND ($table[players].roid_steel + $table[players].roid_crystal + $table[players].roid_erbium + $table[players].roid_unused) <= '$maxasteroids')
		";
		if (strlen($sql_excl) > 0) { $sql_scanuni .= $sql_excl; }
		if (strlen($sql_cluster) > 0) { $sql_scanuni .= $sql_cluster; }
		if (strlen($sql_order) > 0) { $sql_scanuni .= $sql_order; }
	}
}
switch($error) {
	case 0:
	/* $msg was set above */
	break;
	case 1:
	/* $msg was set above */
	break;
	case 100:
	$msg = "You can't scan negative or zero scans.";
	break;
	case 101:
	$msg = "You can only use numbers in 'amount of scans' field.";
	break;
	case 102:
	$msg = "You need to build the Basic intelligence unit construction to enable the asteroid scan possibilities.";
	break;
	case 103:
	$msg = 'There is no planet at these coordinates.';
	break;
	case 104:
	$msg = 'There are not enough resources available to perform such a scan.';
	break;
	case 105:
	$msg = 'You need to build the Advanced intelligence unit construction to enable global scan possibilities.';
	break;
	case 106:
	$msg = 'You need to build the Next-Generation intelligence unit construction to enable news scan possibilities.';
	break;
	case 107:
	$msg = 'You need to build the Universe intelligence unit construction to enable universe scan possibilities.';
	break;
	case 108:
	$msg = 'The maximum score value must be a number greater then 0.';
	break;
	case 109:
	$msg = 'The maximum asteroid value must be a number greater then 0.';
	break;
	case 110:
	$msg = 'The minimum score value must be a number greater then or equal to 0.';
	break;
	case 111:
	$msg = 'The minimum asteroid value must be a number greater then or equal to 0.';
	break;
	case 112:
	$msg = 'The minimum asteroid value can not be greater then the maximum asteroid value.';
	break;
	case 113:
	$msg = 'The minimum score value can not be greater then the maximum score value.';
	break;
}
$playerdata = getPlayerData($playerdata['id']);

if ($msg) {
	if ($error > 0) { $error_color = 'red'; }
	else { $error_color = 'green'; }
?>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td valign="top"><p align="center"><font color="<?echo $error_color;?>"><?echo $msg;?></font></td>
	</tr>
	<tr height="15">
		<td valign="top"></td>
	</tr>	
</table>
<?
}

if ((($do == 'globalscan') || ($do == 'newsscan') || ($do == 'uniscan')) && ($error < 100)) {
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="30%">Scan results <?if ($do != 'uniscan') {?>(<?=$x?>:<?=$y?>:<?=$z?>)<?}?></td>
		<td width="65%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="2%" background="img/border/L.gif">&nbsp;</td>
		<td width="95%" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top">
						<?
						if ($do == 'globalscan') {
							$globalscan = getPlayerdata($target_id);
						?>
						<table border="0" width="800">
							<tr>
								<td width="400" background="img/bg_balk.jpg" colspan="2"><b>Regular information</b></td>
								<td width="400" background="img/bg_balk.jpg" colspan="2"><b>Resource information</b></td>
							</tr>
							<tr>
								<td width="150">Coordinates:</td>
								<td width="250"><?=$x;?>:<?=$y;?>:<?=$z;?></td>
								<td width="150">Steel resource:</td>
								<td width="250"><?=parseInteger($globalscan['res_steel']);?></td>
							</tr>
							<tr>
								<td width="150">Rulername & planetname</td>
								<td width="250"><?=$globalscan['rulername'];?> of <?=$globalscan['planetname'];?></td>
								<td width="150">Crystal resource:</td>
								<td width="250"><?=parseInteger($globalscan['res_crystal']);?></td>
							</tr>
							<tr>
								<td width="150">Alliance name & tag:</td>
								<td width="250"><?=getAllianceNameById($globalscan['alliance_id']);?> (<?=getAllianceTageById($globalscan['alliance_id']);?>)</td>
								<td width="150">Erbium resource:</td>
								<td width="250"><?=parseInteger($globalscan['res_erbium']);?></td>
							</tr>
							<tr>
								<td width="150">Score:</td>
								<td width="250"><?=parseInteger($globalscan['score']);?></td>
								<td width="150">Titanium resource:</td>
								<td width="250"><?=parseInteger($globalscan['res_titanium']);?></td>
							</tr>
							<tr>
								<td width="800" colspan="4">&nbsp;</td>
							</tr>
							<tr>
								<td width="800" colspan="4" background="img/bg_balk.jpg"><b>Asteroid information</b></td>
							</tr>
							<tr>
								<td width="150">Steel:</td>
								<td width="650" colspan="3"><?=parseInteger($globalscan['roid_steel']);?></td>
							</tr>
							<tr>
								<td width="150">Crystal:</td>
								<td width="650" colspan="3"><?=parseInteger($globalscan['roid_crystal']);?></td>
							</tr>
							<tr>
								<td width="150">Erbium:</td>
								<td width="650" colspan="3"><?=parseInteger($globalscan['roid_erbium']);?></td>
							</tr>
							<tr>
								<td width="150">Unused:</td>
								<td width="650" colspan="3"><?=parseInteger($globalscan['roid_unused']);?></td>
							</tr>
							<tr>
								<td width="150">Total:</td>
								<td width="650" colspan="3"><?=parseInteger($globalscan['roid_steel']+$globalscan['roid_crystal']+$globalscan['roid_erbium']+$globalscan['roid_unused']);?></td>
							</tr>
							<tr>
								<td width="800" colspan="4">&nbsp;</td>
							</tr>
							<tr>
								<td width="800" colspan="4" background="img/bg_balk.jpg"><b>Ship information</b></td>
							</tr>
							<?
							$sql_ships = "
									SELECT $table[playerunit].type_id, $table[playerunit].unit_id, $table[playerunit].amount, $table[ships].name
									FROM $table[playerunit] INNER JOIN $table[ships]
									ON $table[playerunit].unit_id = $table[ships].id
									WHERE $table[playerunit].player_id = '$target_id'
									ORDER BY $table[ships].id";
							$res_ships = mysql_query($sql_ships);
							$num_ships = @mysql_num_rows($res_ships);
							if ($num_ships > 0) {
								while ($rec_ships = mysql_fetch_assoc($res_ships)) {
									if ($rec_ships['amount'] > 0) {
							?>
								<tr>
									<td width="150"><?=$rec_ships['name'];?></td>
									<td width="650" colspan="3"><?=parseInteger($rec_ships['amount']);?></td>
								</tr>
							<?
									}
								}
							} else {
							?>
								<tr>
									<td colspan="4" align="center">No ships for this target.</td>
								</tr>
							<?
							}
							?>
						</table>
						<?
						}
						if ($do == 'newsscan') {
						?>
						
						<table border="0" width="800">
							<?
							$sql_news = "SELECT * FROM $table[playernews] WHERE `player_id` = '$target_id' ORDER BY `id` DESC, `date` DESC";
							$res_news = mysql_query($sql_news);
							if (@mysql_num_rows($res_news) > 0) {
							?>
							<tr>
								<td colspan="3" align="center"></td>
							</tr>
							<tr>
								<td width="85" background="img/bg_balk.jpg"><b>Category</b></td>
								<td width="595" background="img/bg_balk.jpg"><b>Item</b></td>
								<td width="125" background="img/bg_balk.jpg"><b>Date</b></td>
							</tr>
							<?
							while ($rec_news = mysql_fetch_array($res_news)) {
							?>
							<tr>
								<td rowspan="2" valign="top"><?echo $rec_news['category'];?></td>
								<td><b><?echo $rec_news['subject'];?></b></td>
								<td><?echo date('H:i d-m-Y', $rec_news['date']);?></td>
							</tr>
							<tr>
								<td valign="top"><?echo $rec_news['text'];?></td>
								<td align="right"></td>
							</tr>
							<?
							}
							}
							else {
							?>
							<tr>
								<td colspan="3" align="center">There is no news for this planet!</td>
							</tr>
							<?
							}
							?>
						</table>
						<?
						}
						if ($do == 'uniscan') {
						?>
						<table border="0" width="800">
							<tr>
								<td width="75" background="img/bg_balk.jpg" align="center"><b>Coordinates</b></td>
								<td width="75" background="img/bg_balk.jpg" align="center"><b>Tag</b></td>
								<td width="500" background="img/bg_balk.jpg"><b>Ruler- & planetname</b></td>
								<td width="100" background="img/bg_balk.jpg"><b>Score</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Asteroids</b></td>
							</tr>
							<?
							$res_uniscan = mysql_query($sql_scanuni);
							$num_uniscan = mysql_num_rows($res_uniscan);
							if ($num_uniscan > 0) {
								while ($rec_uniscan = mysql_fetch_assoc($res_uniscan)) {
							?>
							<tr>
								<td align="center"><a href="main.php?mod=galaxy&act=view&x=<?=$rec_uniscan['x'];?>&y=<?=$rec_uniscan['y'];?>"><?=$rec_uniscan['x'];?>:<?=$rec_uniscan['y'];?>:<?=$rec_uniscan['z'];?></a></td>
								<td align="center"><?=getAllianceTageById($rec_uniscan['alliance_id']);?></td>
								<td><a href="main.php?mod=main&act=mail&do=compose&x=<?=$rec_uniscan['x'];?>>&y=<?=$rec_uniscan['y'];?>&z=<?=$rec_uniscan['z'];?>"><?=$rec_uniscan['rulername'];?> of <?=$rec_uniscan['planetname'];?></a></td>
								<td><?=parseInteger($rec_uniscan['score']);?></td>
								<td><?=parseInteger($rec_uniscan['total_asteroids']);?></td>
							</tr>
							<?
								}
							} else {
							?>
							<tr>
								<td colspan="5" align="center">There where no results for this scan.</td>
							</tr>
							<?
							}
							?>
						</table>						
						<?
						}
						?>
					</td>
				</tr>
			</table>
		</td>
		<td width="3%" background="img/border/R.gif">&nbsp;</td>
	</tr>
	<tr>
		<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
		<td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
		<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
	</tr>
</table>
<br><br>
<?
}
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="30%">Intelligence center</td>
		<td width="65%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="2%" background="img/border/L.gif">&nbsp;</td>
		<td width="95%" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top">
					<?
					if (checkItem($playerdata['id'], $BASIC_INTELLIGENCE)) {
					?>
						<form method="POST" action="main.php?mod=office&act=intelligence&do=asteroidscan">
						<table border="0" width="800">
							<tr>
								<td width="800" background="img/bg_balk.jpg" colspan="2"><b>Asteroid scans</b></td>
							</tr>
							<tr>
								<td width="150">Amount of scans to do:</td>
								<td width="650"><input type="text" name="scans_amount" size="10" value="0"></td>
							</tr>
							<tr>
								<td width="800" colspan="2" align="left">A single asteroid scan costs 2000 crystal.</td>
							</tr>
							<tr>
								<td width="800" colspan="2" align="center"><input type="submit" name="scanasteroids" value="  Scan for asteroids  "></td>
							</tr>
						</table>
						</form>
					<?
					} else {
					?>
						<table border="0" width="800">
							<tr>
								<td width="800" align="center">You need to build the Basic intelligence unit construction to enable the asteroid scan possibilities.</td>
							</tr>
						</table>
					<?
					}

					if (checkItem($playerdata['id'], $ADVANCED_INTELLIGENCE)) {
					?>
						<form method="POST" action="main.php?mod=office&act=intelligence&do=globalscan">
						<table border="0" width="800">
							<tr>
								<td width="800" background="img/bg_balk.jpg" colspan="2"><b>Global scans</b></td>
							</tr>
							<tr>
								<td width="150">Coordinates to scan:</td>
								<td width="650"><input type="text" name="x" size="1"> : <input type="text" name="y" size="1"> : <input type="text" name="z" size="1"></td>
							</tr>
							<tr>
								<td width="800" colspan="2" align="left">A single global scan costs 2700 crystal.</td>
							</tr>
							<tr>
								<td width="800" colspan="2" align="center"><input type="submit" name="globalscan" value="  Global-scan target  "></td>
							</tr>
						</table>
						</form>
					<?
					} else {
					?>
						<table border="0" width="800">
							<tr>
								<td width="800" align="center">You need to build the Advanced intelligence unit construction to enable globalscan possibilities.</td>
							</tr>
						</table>
					<?
					}
					if (checkItem($playerdata['id'], $NG_INTELLIGENCE)) {
					?>
						<form method="POST" action="main.php?mod=office&act=intelligence&do=newsscan">
						<table border="0" width="800">
							<tr>
								<td width="800" background="img/bg_balk.jpg" colspan="2"><b>News scans</b></td>
							</tr>
							<tr>
								<td width="150">Coordinates to scan:</td>
								<td width="650"><input type="text" name="x" size="1"> : <input type="text" name="y" size="1"> : <input type="text" name="z" size="1"></td>
							</tr>
							<tr>
								<td width="800" colspan="2" align="left">A single news scan costs 5000 crystal.</td>
							</tr>
							<tr>
								<td width="800" colspan="2" align="center"><input type="submit" name="newsscan" value="  News-scan target  "></td>
							</tr>
						</table>
						</form>
					<?
					} else {
					?>
						<table border="0" width="800">
							<tr>
								<td width="800" align="center">You need to build the Next-Generation intelligence unit construction to enable newsscan possibilities.</td>
							</tr>
						</table>
					<?
					}
					if (checkItem($playerdata['id'], $UNI_INTELLIGENCE)) {
						if (empty($minscore)) { $minscore = 0; }
						if (empty($maxscore)) { $maxscore = $playerdata['score']; }
						if (empty($minasteroids)) { $minasteroids = 0; }
						if (empty($maxasteroids)) { $maxasteroids = $playerdata['roids_steel'] + $playerdata['roids_crystal'] + $playerdata['roids_erbium'] + $playerdata['roids_unused']; }
					?>
						<form method="POST" action="main.php?mod=office&act=intelligence&do=uniscan">
						<table border="0" width="800">
							<tr>
								<td width="800" background="img/bg_balk.jpg" colspan="2"><b>Universe scans</b></td>
							</tr>
							<tr>
								<td width="150">Search in cluster:</td>
								<td width="650">
									<select name="findcluster">
										<option value="0"<?if (($findcluster == 0) || (empty($findcluster))) echo ' selected="selected"';?>>All clusters</option>
										<?
										$sql_findcluster = "SELECT `x` FROM $table[galaxy] GROUP BY `x` ORDER BY `x`";
										$res_findcluster = mysql_query($sql_findcluster);
										$num_findcluster = mysql_num_rows($res_findcluster);
										if ($num_findcluster > 0) {
											while ($rec_findcluster = mysql_fetch_array($res_findcluster)) {
										?>
											<option value="<?=$rec_findcluster['x'];?>"<?if ($findcluster == $rec_findcluster['x']) echo ' selected="selected"';?>>Cluster <?=$rec_findcluster['x'];?></option>
										<?
											}
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td width="800" colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td width="150">Minimum score:</td>
								<td width="650"><input type="text" name="minscore" size="20" value="<?=$minscore;?>"></td>
							</tr>
							<tr>
								<td width="150">Maximum score:</td>
								<td width="650"><input type="text" name="maxscore" size="20" value="<?=$maxscore;?>"></td>
							</tr>
							<tr>
								<td width="800" colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td width="150">Minimum asteroids:</td>
								<td width="650"><input type="text" name="minasteroids" size="7" value="<?=$minasteroids;?>"></td>
							</tr>
							<tr>
								<td width="150">Maximum asteroids:</td>
								<td width="650"><input type="text" name="maxasteroids" size="7" value="<?=$maxasteroids;?>"></td>
							</tr>
							<tr>
								<td width="800" colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td width="150" valign="top">Extra options:</td>
								<td width="650">
									<input type="checkbox" name="excl_galmem" value="1"<?if (!empty($excl_galmem)) echo ' checked="checked"';?>><label for="excl_galmem">Exclude galaxy members</label><br />
									<input type="checkbox" name="excl_allmem" value="1"<?if (!empty($excl_allmem)) echo ' checked="checked"';?>><label for="excl_allmem">Exclude alliance members</label><br />
								</td>
							</tr>
							<tr>
								<td width="800" colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td width="150" valign="top">Order results:</td>
								<td width="650">
									<input type="radio" name="order" value="score"<?if (empty($order) || ($order == 'score')) echo ' checked="checked"';?>><label for="order_score">Order by score (descending)</label><br />
									<input type="radio" name="order" value="asteroid"<?if ($order == 'asteroid') echo ' checked="checked"';?>><label for="order_asteroid">Order by asteroid amount (descending)</label><br />
									<input type="radio" name="order" value="xyz"<?if ($order == 'xyz') echo ' checked="checked"';?>><label for="order_xyz">Order by X:Y:Z coordinates (ascending)</label>
								</td>
							</tr>
							<tr>
								<td width="800" colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td width="800" colspan="2" align="left">A universe scan cost 15.000 crystal.</td>
							</tr>
							<tr>
								<td width="800" colspan="2" align="center"><input type="submit" name="uniscan" value="  Scan universe  "></td>
							</tr>
						</table>
						</form>
					<?
					} else {
					?>
						<table border="0" width="800">
							<tr>
								<td width="800" align="center">You need to build the Universe intelligence unit construction to enable universe scan possibilities.</td>
							</tr>
						</table>
					<?
					}
					?>
					</td>
				</tr>
			</table>
		</td>
		<td width="3%" background="img/border/R.gif">&nbsp;</td>
	</tr>
	<tr>
		<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
		<td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
		<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
	</tr>
</table>
