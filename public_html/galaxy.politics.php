<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}

$galaxy_id = $playerdata['galaxy_id'];

$sql_galaxydata = "SELECT * FROM $table[players] WHERE `galaxy_id` = '$galaxy_id' ORDER BY `galaxy_spot`";
$res_galaxydata = mysql_query($sql_galaxydata);

$sql_xy = "SELECT `x`, `y` FROM $table[galaxy] WHERE `id` = '$galaxy_id'";

$rec_xy = mysql_fetch_array(mysql_query($sql_xy)) or die(mysql_error());
$x = $rec_xy['x'];
$y = $rec_xy['y'];

if (!isset($do)) { $act = secureData($_GET['do']); }
if (!isset($do)) { $act = secureData($_POST['do']); }

if ($do == 'vote') {
	$newvote = secureData($_POST['vote_id']);
	$get_vote = "SELECT * FROM $table[politics] WHERE `player_id` = '$playerdata[id]' AND `galaxy_id` = '$galaxy_id'";
	$res_vote = mysql_query($get_vote);
	if (@mysql_num_rows($res_vote) > 0) {
		$rec_vote = mysql_fetch_array($res_vote);
		$sql_setvote = "UPDATE $table[politics] SET `voted_on` = '$newvote' WHERE `id` = '$rec_vote[id]'";
	} else {
		$sql_setvote = "INSERT INTO $table[politics] (`id`, `galaxy_id`, `player_id`, `voted_on`)
						VALUES ('', '$galaxy_id', '$playerdata[id]', '$newvote')";
	}
	mysql_query($sql_setvote);
}
if ($do == 'changeministers') {
	$moc_id = secureData($_POST['moc_id']);
	$mow_id = secureData($_POST['mow_id']);
	$moe_id	= secureData($_POST['moe_id']);
	$topic = secureData($_POST['topic']);
	$image_url = secureData($_POST['image_url']);

	//if (($moc_id == $mow_id) || ($moc_id == $moe_id) || ($mow_id == $moe_id)) { return; }
	if ((($moc_id == $mow_id) && ($moc_id > 0)) || (($moc_id == $moe_id) && ($moc_id > 0)) || (($mow_id == $moe_id) && ($mow_id > 0))) { $error = 100; }
	if (strlen($topic) <= 0) { $error = 107; }
	if ($error < 100) {
		$sql_updministers = "UPDATE $table[galaxy] SET `topic` = '$topic', `image_url` = '$image_url', `moc_id` = '$moc_id', `mow_id` = '$mow_id',
							`moe_id` = '$moe_id' WHERE `id` = '$galaxy_id' ";
		mysql_query($sql_updministers);
	}
}
if ($do == 'massmail') {
	$error = 0;
	$subject = secureData($_POST['subject']);
	$text = secureData($_POST['text']);

	$sql_galmembers = "SELECT `id` FROM $table[players] WHERE `galaxy_id` = '$playerdata[galaxy_id]'";
	$res_galmembers = mysql_query($sql_galmembers);
	$num_galmembers = mysql_num_rows($res_galmembers);
	if ($subject == '') { $error = 101; }
	if ($text == '') { $error = 102; }

	while ($galmembers = mysql_fetch_array($res_galmembers)) {
		$player_to_id = $galmembers['id'];
		if ($error == 0) {
			$sql_newmail = "INSERT INTO $table[mail] (`from_player`, `to_player`, `subject`, `text`, `date`)
						VALUES ('$playerdata[id]', '$player_to_id', '$subject', '$text', UNIX_TIMESTAMP())";
			mysql_query($sql_newmail);
		}
	}
	if ($error == 0) {
		$error = 1;
	}
}
if ($do == 'donatefund') {
	$sql_galaxydata = "SELECT `id`, `x`, `y`, `topic`, `image_url`, `commander_id`, `moc_id`, `mow_id`, `moe_id`,
							`fund_steel`, `fund_crystal`, `fund_erbium`, `fund_titanium`, `private`, `password` 
						FROM $table[galaxy] 
						WHERE `id` = '$galaxy_id'";
	$galaxy_data = mysql_fetch_array(mysql_query($sql_galaxydata));

	$res_type = secureData($_POST['res_type']);
	$donate_to = securedata($_POST['donate_to']);
	$amount = secureData($_POST['amount']);

	$fund_arrkey = 'fund_'.$res_type;
	$player_arrkey = 'res_'.$res_type;
	if (($playerdata['id'] != $galaxy_data['commander_id']) && ($playerdata['id'] != $galaxy_data['moe_id'])) { $error = 106; }
	if ($amount <= 0) { $error = 103; }
	if ($galaxy_data[$fund_arrkey] < $amount) { $error = 104; }
	if ($donate_to <= 0) { $error = 105; }
	if ($error < 100) {
		$sql_donateplayer = "UPDATE $table[players] SET `$player_arrkey` = `$player_arrkey` + '$amount' WHERE `id` = '$donate_to'";
		mysql_query($sql_donateplayer) or die(mysql_error());
		addNews($donate_to, 'Donation', 'Recieved donation', 'You recieved a donation from the galactic fund.<br />The donation is '.$amount.' of '.$res_type.'.');
		$sql_updatefund = "UPDATE $table[galaxy] SET `$fund_arrkey` = `$fund_arrkey` - '$amount' WHERE `id` = '$playerdata[galaxy_id]'";
		mysql_query($sql_updatefund) or die(mysql_error());
	}
}
setCommander($galaxy_id);

$sql_galaxydata = "SELECT `id`, `x`, `y`, `topic`, `image_url`, `commander_id`, `moc_id`, `mow_id`, `moe_id`,
						`fund_steel`, `fund_crystal`, `fund_erbium`, `fund_titanium`, `private`, `password` 
					FROM $table[galaxy] 
					WHERE `id` = '$galaxy_id'";
$GALAXY_INFO = mysql_fetch_array(mysql_query($sql_galaxydata));
switch($error) {
	case 1:
	$msg = "Succesfully sent massmail to $num_galmembers players.";
	break;
	case 100:
	$msg = "You can't put one player on two functions.";
	break;
	case 101:
	$msg = "A mail is useless with an empty subject.";
	break;
	case 102:
	$msg = "Why sending empty mails?";
	break;
	case 103:
	$msg = 'You can not donate nothing or a negative amount.';
	break;
	case 104:
	$msg = 'That amount is not in the galactic fund.';
	break;
	case 105:
	$msg = 'You have to select a galaxy member to donate the fund resources to.';
	break;
	case 106:
	$msg = 'You are not the minister of economics or galaxy commander for this galaxy. You can\'t donate resources from the galactic fund.';
	break;
	case 107:
	$msg = 'Empty galaxy names are not allowed.';
	break;
}

if ($msg) {
	if ($error > 99) { $error_color = 'red'; }
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
?>

<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="125">Galaxy politics</td>
		<td width="667" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="4" background="img/border/L.gif">&nbsp;</td>
		<td width="696" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="760">
				<tr>
					<td valign="top">
						<table border="0" width="760">
							<tr>
								<td width="100" align="center" background="img/bg_balk.jpg"><b>Location</b></td>
								<td width="265" align="left" background="img/bg_balk.jpg"><b>Player</b></td>
								<td width="265" align="left" background="img/bg_balk.jpg"><b>Voted on</b></td>
								<td width="100" align="left" background="img/bg_balk.jpg"><b>Total votes</b></td>
							</tr>
							<?
							$counter = 0;
							$votedata = array(array());
							while ($rec_galaxydata = mysql_fetch_array($res_galaxydata)) {
								$z = $rec_galaxydata['galaxy_spot'];

								$sql_getvote = "SELECT `voted_on` FROM $table[politics] WHERE `galaxy_id` = '$galaxy_id' AND `player_id` = '$rec_galaxydata[id]'";
								$rec_getvote = mysql_fetch_array(mysql_query($sql_getvote));

								$votedata[$counter][0] = $x.':'.$y.':'.$z;
								$votedata[$counter][1] = $rec_galaxydata['id'];
								$votedata[$counter][2] = $rec_getvote['voted_on'];
								$votedata[$counter][3] = 0;

								$counter++;
							}

							for ($f = 0; $f < count($votedata); $f++) {
								for ($g = 0; $g < count($votedata); $g++) {
									if ($votedata[$g][2] == $votedata[$f][1]) { $votedata[$f][3]++; }
								}
							}

							for ($i = 0; $i < count($votedata); $i++) {
							?>
							<tr>
								<td align="center"><?=$votedata[$i][0];?></td>
								<td align="left"><?=getRulernameById($votedata[$i][1]);?> of <?=getPlanetnameById($votedata[$i][1]);?></td>
								<td align="left"><?if ($votedata[$i][2] > 0) { ?><?=getRulernameById($votedata[$i][2]);?> of <?=getPlanetnameById($votedata[$i][2]);?><? } else { echo '&nbsp;'; }?></td>
								<td align="left"><?=$votedata[$i][3];?></td>
							</tr>
							<?
							}
							?>
						</table>
					</td>
				</tr>
			</table>
		</td>
		<td width="4" background="img/border/R.gif">&nbsp;</td>
	</tr>
	<tr>
		<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
		<td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
		<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
	</tr>
</table>
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="125">Current situation</td>
		<td width="667" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="4" background="img/border/L.gif">&nbsp;</td>
		<td width="696" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="760">
				<tr>
					<td valign="top">
						<form method="POST" action="main.php?mod=galaxy&act=politics&do=changeministers">
						<table border="0" width="760">
							<tr>
								<td width="175" align="left" background="img/bg_balk.jpg"><b>Function</b></td>
								<td width="585" align="left" background="img/bg_balk.jpg"><b>Player</b></td>
							</tr>
							<tr>
								<td align="left">Galaxy commander:</td>
								<td align="left"><? if ($GALAXY_INFO['commander_id'] > 0) {?><?=getRulernameById($GALAXY_INFO['commander_id']);?> of <?=getPlanetnameById($GALAXY_INFO['commander_id']);?><? } ?></td>
							</tr>
							<tr>
								<td align="left">Minister of communication:</td>
								<td align="left">
									<?
									if ($playerdata['id'] == $GALAXY_INFO['commander_id']) {
									?>
									<select size="1" name="moc_id" style="font-family: Verdana; color: #787878; font-size: 7.5pt; background-color: #ffffff; border: 1px solid #787878;">
										<option value="0">-</option>
										<?
										$sql_galaxydata = "SELECT `id`, `rulername`, `planetname` FROM $table[players] WHERE `galaxy_id` = '$galaxy_id' ORDER BY `galaxy_spot`";
										$res_galaxydata = mysql_query($sql_galaxydata);
										while ($rec_galdata = mysql_fetch_array($res_galaxydata)) {
											if ($rec_galdata['id'] == $GALAXY_INFO['moc_id']) { $sel = 'selected'; }
											else { $sel = null; }
											if ($rec_galdata['id'] != $playerdata['id']) {
										?>
											<option value="<?echo $rec_galdata['id'].'" '.$sel;?>><?=$rec_galdata['rulername'];?> of <?=$rec_galdata['planetname'];?></option>
										<?
}
}
										?>
									</select>
									<?
}
elseif ($GALAXY_INFO['moc_id'] > 0) { echo getRulernameById($GALAXY_INFO['moc_id']).' of '.getPlanetnameById($GALAXY_INFO['moc_id']); }
									?>
								</td>
							</tr>
							<tr>
								<td align="left">Minister of war:</td>
								<td align="left">
									<?
									if ($playerdata['id'] == $GALAXY_INFO['commander_id']) {
									?>
									<select size="1" name="mow_id" style="font-family: Verdana; color: #787878; font-size: 7.5pt; background-color: #ffffff; border: 1px solid #787878;">
										<option value="0">-</option>
										<?
										$sql_galaxydata = "SELECT `id`, `rulername`, `planetname` FROM $table[players] WHERE `galaxy_id` = '$galaxy_id' ORDER BY `galaxy_spot`";
										$res_galaxydata = mysql_query($sql_galaxydata);
										while ($rec_galdata = mysql_fetch_array($res_galaxydata)) {
											if ($rec_galdata['id'] == $GALAXY_INFO['mow_id']) { $sel = 'selected'; }
											else { $sel = null; }
											if ($rec_galdata['id'] != $playerdata['id']) {
										?>
											<option value="<?echo $rec_galdata['id'].'" '.$sel;?>><?=$rec_galdata['rulername'];?> of <?=$rec_galdata['planetname'];?></option>
										<?
}
}
										?>
									</select>
									<?
}
elseif ($GALAXY_INFO['mow_id'] > 0) {
	echo getRulernameById($GALAXY_INFO['mow_id']).' of '.getPlanetnameById($GALAXY_INFO['mow_id']);
}
									?>
								</td>
							</tr>
							<tr>
								<td align="left">Minister of economics:</td>
								<td align="left">
									<?
									if ($playerdata['id'] == $GALAXY_INFO['commander_id']) {
									?>
									<select size="1" name="moe_id" style="font-family: Verdana; color: #787878; font-size: 7.5pt; background-color: #ffffff; border: 1px solid #787878;">
										<option value="0">-</option>
										<?
										$sql_galaxydata = "SELECT `id`, `rulername`, `planetname` FROM $table[players] WHERE `galaxy_id` = '$galaxy_id' ORDER BY `galaxy_spot`";
										$res_galaxydata = mysql_query($sql_galaxydata);
										while ($rec_galdata = mysql_fetch_array($res_galaxydata)) {
											if ($rec_galdata['id'] == $GALAXY_INFO['moe_id']) { $sel = 'selected'; }
											else { $sel = null; }
											if ($rec_galdata['id'] != $playerdata['id']) {
										?>
											<option value="<?echo $rec_galdata['id'].'" '.$sel;?>><?=$rec_galdata['rulername'];?> of <?=$rec_galdata['planetname'];?></option>
										<?
}
}
										?>
									</select>
									<?
}
elseif($GALAXY_INFO['moe_id'] > 0) { echo getRulernameById($GALAXY_INFO['moe_id']).' of '.getPlanetnameById($GALAXY_INFO['moe_id']); }
									?>
								</td>
							</tr>
							<tr>
								<td width="175" align="left" background="img/bg_balk.jpg" colspan="2"><b>Other settings</b></td>
							</tr>
							<tr>
								<td align="left">Galaxy name:</td>
								<td align="left">
								<?
								if ($playerdata['id'] == $GALAXY_INFO['commander_id']) {
								?>
								<input type="text" name="topic" value="<?echo stripslashes($GALAXY_INFO['topic']);?>" size="100">
								<?
								} else { echo stripslashes($GALAXY_INFO['topic']); }
								?>
								</td>
							</tr>
							<tr>
								<td align="left">Galaxy image url:</td>
								<td align="left">
								<?
								if ($playerdata['id'] == $GALAXY_INFO['commander_id']) {
								?>
								<input type="text" name="image_url" value="<?echo $GALAXY_INFO['image_url'];?>" size="100">
								<?
								} else { echo $GALAXY_INFO['image_url']; }
								?>
								
								</td>
							</tr>
							<?
							if ($playerdata['id'] == $GALAXY_INFO['commander_id']) {
							?>
							<tr>
								<td align="left" height="15" colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td align="center" colspan="2"><input type="submit" name="changeminister" value=" Apply changes "></td>
							</tr>
							<?
							}
							?>	
						</table>
						</form>
					</td>
				</tr>
			</table>
		</td>
		<td width="4" background="img/border/R.gif">&nbsp;</td>
	</tr>
	<tr>
		<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
		<td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
		<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
	</tr>
</table>
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="125">Galactic Fund</td>
		<td width="667" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="4" background="img/border/L.gif">&nbsp;</td>
		<td width="696" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="760">
				<tr>
					<td valign="top">
						<?
						if (($playerdata['id'] == $GALAXY_INFO['commander_id']) || ($playerdata['id'] == $GALAXY_INFO['moe_id'])) {
						?>
						<form method="POST" action="main.php?mod=galaxy&act=politics&do=donatefund">
						<?
						}
						?>
						<table border="0" width="760">
							<tr>
								<td width="100" align="left" background="img/bg_balk.jpg"><b>Type</b></td>
								<td width="650" align="left" background="img/bg_balk.jpg"><b>Amount</b></td>
							</tr>
							<tr>
								<td>Steel:</td>
								<td><?=parseInteger($GALAXY_INFO['fund_steel']);?></td>
							</tr>
							<tr>
								<td>Crystal:</td>
								<td><?=parseInteger($GALAXY_INFO['fund_crystal']);?></td>
							</tr>
							<tr>
								<td>Erbium:</td>
								<td><?=parseInteger($GALAXY_INFO['fund_erbium']);?></td>
							</tr>
							<tr>
								<td>Titanium:</td>
								<td><?=parseInteger($GALAXY_INFO['fund_titanium']);?></td>
							</tr>
							<?
							if (($playerdata['id'] == $GALAXY_INFO['commander_id']) || ($playerdata['id'] == $GALAXY_INFO['moe_id'])) {
							?>
							<tr>
								<td width="800" align="left" background="img/bg_balk.jpg" colspan="2"><b>Donate from the fund</b></td>
							</tr>
							<tr>
								<td>Type:</td>
								<td>
									<select name="res_type">
										<option value="steel">Steel</option>
										<option value="crystal">Crystal</option>
										<option value="erbium">Erbium</option>
										<option value="titanium">Titanium</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>To player:</td>
								<td>
									<select name="donate_to">
										<option value="0" selected="selected">-</option>
										<?
										$sql_galaxydata = "SELECT `id`, `rulername`, `planetname` FROM $table[players] WHERE `galaxy_id` = '$playerdata[galaxy_id]' ORDER BY `galaxy_spot`";
										$res_galaxydata = mysql_query($sql_galaxydata);
										while ($rec_galdata = mysql_fetch_array($res_galaxydata)) {
											if ($rec_galdata['id'] != $playerdata['id']) {
										?>
											<option value="<?=$rec_galdata['id'];?>"><?=$rec_galdata['rulername'];?> of <?=$rec_galdata['planetname'];?></option>
										<?
											}
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td>Amount:</td>
								<td><input type="text" name="amount" size="15"></td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="2" align="center"><input type="submit" name="sendonation" value="Send donation"></td>
							</tr>
							<?
							}
							?>
						</table>
						<?
						if (($playerdata['id'] == $GALAXY_INFO['commander_id']) || ($playerdata['id'] == $GALAXY_INFO['moe_id'])) {
						?>
						</form>
						<?
						}
						?>
					</td>
				</tr>
			</table>
		</td>
		<td width="4" background="img/border/R.gif">&nbsp;</td>
	</tr>
	<tr>
		<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
		<td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
		<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
	</tr>
</table>
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="125">Vote for commander</td>
		<td width="667" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="4" background="img/border/L.gif">&nbsp;</td>
		<td width="696" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="760">
				<tr>
					<td valign="top">
						<form method="POST" action="main.php?mod=galaxy&act=politics&do=vote">
						<table border="0" width="760">
							<tr>
								<td width="250" align="left" background="img/bg_balk.jpg"><b>Player</b></td>
								<td width="510" align="left" background="img/bg_balk.jpg">&nbsp;</td>
							</tr>
							<tr>
								<td align="center">
									<select size="1" name="vote_id" style="font-family: Verdana; color: #787878; font-size: 7.5pt; background-color: #ffffff; border: 1px solid #787878;">
										<?
										$sql_galaxydata = "SELECT `id`, `rulername`, `planetname` FROM $table[players] WHERE `galaxy_id` = '$galaxy_id' ORDER BY `galaxy_spot`";
										$res_galaxydata = mysql_query($sql_galaxydata);
										$sql_getownvote = "SELECT * FROM $table[politics] WHERE `player_id` = '$playerdata[id]'";
										$rec_getownvote = mysql_fetch_array(mysql_query($sql_getownvote));
										while ($rec_galdata = mysql_fetch_array($res_galaxydata)) {
											$sel = null;
											if ($rec_galdata['id'] == $rec_getownvote['voted_on']) { $sel = 'selected'; }
										?>
											<option value="<?echo $rec_galdata['id'];?>"<?echo $sel;?>><?=$rec_galdata['rulername'];?> of <?=$rec_galdata['planetname'];?></option>
										<?
										}
										?>
									</select>
								</td>
								<td align="left"><input type="submit" name="vote" value=" Vote for this user "></td>
							</tr>
						</table>
						</form>
					</td>
				</tr>
			</table>
		</td>
		<td width="4" background="img/border/R.gif">&nbsp;</td>
	</tr>
	<tr>
		<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
		<td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
		<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
	</tr>
</table>
<?
if (($playerdata['id'] == $GALAXY_INFO['commander_id']) || ($playerdata['id'] == $GALAXY_INFO['moc_id'])) {
?>
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="125">Massmail galaxy</td>
		<td width="667" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="4" background="img/border/L.gif">&nbsp;</td>
		<td width="696" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="760">
				<tr>
					<td valign="top">
						<form method="POST" action="main.php?mod=galaxy&act=politics&do=massmail" name="massmail">
						<table border="0" width="760">
							<tr>
								<td width="800" align="left" background="img/bg_balk.jpg" colspan="2"><b>Enter message</b></td>
							</tr>
							<tr>
								<td width="130">Subject:</td>
								<td width="670"><input type="text" name="subject" size="70"></td>
							</tr>
							<tr>
								<td width="130" valign="top">Content:</td>
								<td width="670">
									<input type="button" name="bb_b" value="[b]" onclick="document.forms['massmail'].text.value=document.forms['massmail'].text.value+'[b]'+prompt('Text which should be bold:', '')+'[/b]'">
									<input type="button" name="bb_i" value="[i]" onclick="document.forms['massmail'].text.value=document.forms['massmail'].text.value+'[i]'+prompt('Text which should be italic:', '')+'[/i]'">
									<input type="button" name="bb_u" value="[u]" onclick="document.forms['massmail'].text.value=document.forms['massmail'].text.value+'[u]'+prompt('Text which should be underlined:', '')+'[/u]'">
									<input type="button" name="bb_url" value="[url]" onclick="document.forms['massmail'].text.value=document.forms['massmail'].text.value+'[url]'+prompt('URL you want to link:', '')+'[/url]'"><br />
									<textarea rows="9" cols="70" name="text"></textarea>
								</td>
							</tr>
							<tr height="15">
								<td width="800" colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td width="800" colspan="2" align="center"><input type="submit" name="sendmail" value="  Send mail  "></td>
							</tr>
						</table>
						</form>
					</td>
				</tr>
			</table>
		</td>
		<td width="4" background="img/border/R.gif">&nbsp;</td>
	</tr>
	<tr>
		<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
		<td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
		<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
	</tr>
</table>
<?
}
?>