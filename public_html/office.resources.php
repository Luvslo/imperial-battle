<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}

if (!isset($do)) { $act = secureData($_GET['do']); }
if (!isset($do)) { $act = secureData($_POST['do']); }

$steel_totalres = 0;
if (checkItem($playerdata['id'], $STEEL_ADV_REF)) {
	$steel_totalres = $RES_FROM_ADV_REF;
} elseif (checkItem($playerdata['id'], $STEEL_REFINERY)) {
	$steel_totalres = $RES_FROM_REFINERY;
}
$total_usedroids = $playerdata['roid_steel'] + $playerdata['roid_crystal'] + $playerdata['roid_erbium'];
$asteroid_initcost = round(($total_usedroids*$RES_FROM_STEEL_ROID*0.25)+((pow($total_usedroids, 1.55))*25));

if ($do == 'initasteroids') {
	$asteroid_amount = secureData($_POST['asteroid_amount']);
	$asteroid_resource = secureData($_POST['asteroid_resource']);
	if ($asteroid_amount > $playerdata['roid_unused']) { $asteroid_amount = $playerdata['roid_unused']; }

	for ($i = 0; $i < $asteroid_amount; $i++) {
		$total_usedroids = $playerdata['roid_steel'] + $playerdata['roid_crystal'] + $playerdata['roid_erbium'];
		//$asteroid_initcost = (($total_usedroids*$RES_FROM_STEEL_ROID) * 1.05);
		$asteroid_initcost = round(($total_usedroids*$RES_FROM_STEEL_ROID*0.25)+((pow($total_usedroids, 1.55))*25));
		$steel_totalres = 0;
		if (checkItem($playerdata['id'], $STEEL_ADV_REF)) {
			$steel_totalres = $RES_FROM_ADV_REF;
		} elseif (checkItem($playerdata['id'], $STEEL_REFINERY)) {
			$steel_totalres = $RES_FROM_REFINERY;
		}
		if ($playerdata['res_steel'] < $asteroid_initcost) {
			break;
		} else {
			$playerdata['res_steel'] -= $asteroid_initcost;
			if ($asteroid_resource == 'steel') { $playerdata['roid_steel']++; }
			if ($asteroid_resource == 'crystal') { $playerdata['roid_crystal']++; }
			if ($asteroid_resource == 'erbium') { $playerdata['roid_erbium']++; }
			$playerdata['roid_unused']--;
		}
	}
	if ($i <= 0) { $error = 100; }
	if ($i > 0) { $error = 2; }
	$msg = 'Initiated '.$i.' '.$asteroid_resource.' asteroids';
	updatePlayerdata($playerdata['id'], $playerdata);
}
if ($do == 'altertitaniumprod') {
	$factory_id = getTitaniumFactoryId($playerdata['id']);

	$steelinv = secureData($_POST['steelinv']);
	$crystalinv = secureData($_POST['crystalinv']);
	$erbiuminv = secureData($_POST['erbiuminv']);

	if (!$factory_id) {
		$upd_factory = "INSERT INTO $table[titanium_factory] (`player_id`, `steel_investment`, `crystal_investment`, `erbium_investment`)
						VALUES ('$playerdata[id]', '$steelinv', '$crystalinv', '$erbiuminv')";
	} else {
		$upd_factory = "UPDATE $table[titanium_factory] SET `steel_investment` = '$steelinv', `crystal_investment` = '$crystalinv', `erbium_investment` = '$erbiuminv' WHERE `id` = '$factory_id' AND `player_id` = '$playerdata[id]'";
	}
	mysql_query($upd_factory) or die(mysql_error());
	$error = 1;
}
if ($do == 'donate') {
	$res_type = secureData($_POST['res_type']);
	$amount = secureData($_POST['amount']);
	$donate_to = secureData($_POST['donate_to']);

	$arrkey = 'res_'.$res_type;
	if ($playerdata[$arrkey] < $amount) { $error = 101; }
	if (($donate_to != 'fund') && ($donate_to <= 0)) { $error = 102; }
	if ($amount <= 0) { $error = 104; }
	if (!is_numeric($amount)) { $error = 105; }
	if ($error < 100) {
		$error = 0;
		$playerdata[$arrkey] -= $amount;
		if ($donate_to == 'fund') {
			$gal_colname = 'fund_'.$res_type;
			$sql_donategfund = "UPDATE $table[galaxy] SET `$gal_colname` = `$gal_colname` + '$amount' WHERE `id` = '$playerdata[galaxy_id]'";
			mysql_query($sql_donategfund) or die(mysql_error());
			if ($moe_id = getMOE($playerdata['galaxy_id'])) {
				$c = getXYZ($playerdata['id']);
				addNews($moe_id, 'Donation','Galactic fund donation', 'The galactic fund recieved a donation from '.$playerdata['rulername'].' of '.$playerdata['planetname'].' ('.$c[0].':'.$c[1].':'.$c[2].').<br />The donation is '.parseInteger($amount).' of '.$res_type.'.');
			}
			$error = 3;
		}
		if ($donate_to > 0) {
			$sql_donateplayer = "UPDATE $table[players] SET `$arrkey` = `$arrkey` + '$amount' WHERE `id` = '$donate_to'";
			mysql_query($sql_donateplayer) or die(mysql_error());
			$c = getXYZ($playerdata['id']);
			addNews($donate_to, 'Donation', 'Recieved donation', 'You recieved a donation from '.$playerdata['rulername'].' of '.$playerdata['planetname'].' ('.$c[0].':'.$c[1].':'.$c[2].').<br />The donation is '.parseInteger($amount).' of '.$res_type.'.');
			$error = 4;
		}
		if ($error == 0) { $error = 103; }
		updatePlayerData($playerdata['id'], $playerdata);
	}
}
switch($error) {
	case 1:
	$msg = "Succesfully altered the titanium factory production process.";
	break;
	case 2:
	/* Msg was set above. */
	break;
	case 3:
	$msg = 'Donated resources to the galactic fund.';
	break;
	case 4:
	$msg = 'Donated resources to a galaxy member.';
	break;
	case 100:
	/* Msg was set above. */
	break;
	case 101:
	$msg = 'You do not have enough resources of this type to donate.';
	break;
	case 102:
	$msg = 'There was no player or fund selected to do the donation.';
	break;
	case 103:
	$msg = 'The donation was only half succesfull, this is a bug and you should report this to the development team.';
	break;
	case 104:
	$msg = 'You can not donate nothing or a negative amount of resources.';
	break;
	case 105:
	$msg = 'The amount you want to donate is not a number. Please check if there are any other characters in the field (like dots).';
	break;
}

if ($msg) {
	if ($error > 100) { $error_color = 'red'; }
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

$playerdata = getPlayerdata($playerdata['id']);
$total_usedroids = $playerdata['roid_steel'] + $playerdata['roid_crystal'] + $playerdata['roid_erbium'];
$asteroid_initcost = round(($total_usedroids*$RES_FROM_STEEL_ROID*0.25)+((pow($total_usedroids, 1.55))*25));
if (checkItem($playerdata['id'], $TITANIUM_FACTORY)) {
	$factory_id = getTitaniumFactoryId($playerdata['id']);
	if ($factory_id > 0) {
		$sql_factorydata = "SELECT `steel_investment`, `crystal_investment`, `erbium_investment` FROM $table[titanium_factory] WHERE `id` = '$factory_id'";
		$res_factorydata = mysql_query($sql_factorydata);
		$num_factorydata = mysql_num_rows($res_factorydata);
		if ($num_factorydata > 0) {
			$rec_factorydata = mysql_fetch_array($res_factorydata);
			$steel_investment = $rec_factorydata['steel_investment'];
			$crystal_investment = $rec_factorydata['crystal_investment'];
			$erbium_investment = $rec_factorydata['erbium_investment'];
		} else {
			$steel_investment = 0;
			$crystal_investment = 0;
			$erbium_investment = 0;
		}
	}
}

$steel_res['planet'] = 0;
if (checkItem($playerdata['id'], $STEEL_ADV_REF)) {
	$steel_res['planet'] = $RES_FROM_ADV_REF;
	$steel_res['asteroids'] = $playerdata['roid_steel'] * $RES_FROM_STEEL_ROID;
} elseif (checkItem($playerdata['id'], $STEEL_REFINERY)) {
	$steel_res['planet'] = $RES_FROM_REFINERY;
	$steel_res['asteroids'] = 0;
}
$steel_res['factory_prod'] = (($steel_res['asteroids'] + $steel_res['planet'])/100)*$steel_investment;

$crystal_res['planet'] = 0;
if (checkItem($playerdata['id'], $CRYSTAL_ADV_REF)) {
	$crystal_res['planet'] = $RES_FROM_ADV_REF;
	$crystal_res['asteroids'] = $playerdata['roid_crystal'] * $RES_FROM_CRYSTAL_ROID;
} elseif (checkItem($playerdata['id'], $CRYSTAL_REFINERY)) {
	$crystal_res['planet'] = $RES_FROM_REFINERY;
	$crystal_res['asteroids'] = 0;
}
$crystal_res['factory_prod'] = (($crystal_res['asteroids'] + $crystal_res['planet'])/100)*$crystal_investment;

$erbium_res['planet'] = 0;
if (checkItem($playerdata['id'], $ERBIUM_ADV_REF)) {
	$erbium_res['planet'] = $RES_FROM_ADV_REF;
	$erbium_res['asteroids'] = $playerdata['roid_erbium'] * $RES_FROM_ERBIUM_ROID;
} elseif (checkItem($playerdata['id'], $ERBIUM_REFINERY)) {
	$erbium_res['planet'] = $RES_FROM_REFINERY;
	$erbium_res['asteroids'] = 0;
}
$erbium_res['factory_prod'] = (($erbium_res['asteroids'] + $erbium_res['planet'])/100)*$erbium_investment;


if (($steel_investment == 0) || ($crystal_investment == 0) || ($erbium_investment == 0)) {
	$steel_res['factory_prod'] = 0;
	$crystal_res['factory_prod'] = 0;
	$erbium_res['factory_prod'] = 0;
	$titanium_production = 0;
	$minus = null;
} else {
	$total_cost = $steel_res['factory_prod'] + $crystal_res['factory_prod'] + $erbium_res['factory_prod'];
	$titanium_production = ($total_cost / 3.14879);
	$minus = '-';
}
$steel_res['total'] = ($steel_res['asteroids'] + $steel_res['planet']) - $steel_res['factory_prod'];
$crystal_res['total'] = ($crystal_res['asteroids'] + $crystal_res['planet']) - $crystal_res['factory_prod'];
$erbium_res['total'] = ($erbium_res['asteroids'] + $erbium_res['planet']) - $erbium_res['factory_prod'];
$titanium_production = ceil($titanium_production);
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="30%">Resources overview</td>
		<td width="65%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="2%" background="img/border/L.gif">&nbsp;</td>
		<td width="95%" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top">
						<table border="0" width="800">
							<tr>
								<td width="125" background="img/bg_balk.jpg"><b>Resource type</b></td>
								<td width="150" background="img/bg_balk.jpg"><b>Planet mining</b></td>
								<td width="200" background="img/bg_balk.jpg"><b>Asteroid mining</b></td>
								<td width="150" background="img/bg_balk.jpg"><b>Factory production</b></td>
								<td width="175" background="img/bg_balk.jpg"><b>Total mining</b></td>
							</tr>
							<tr>
								<td>Steel</td>
								<td>
								<?echo $steel_res['planet'];?>
								</td>
								<td><?echo parseInteger($steel_res['asteroids']);?>
								<td><?echo $minus.parseInteger($steel_res['factory_prod']);?></td>
								</td>
								<td><?echo parseInteger($steel_res['total']);?></td>
							</tr>
							<tr>
								<td>Crystal</td>
								<td>
								<?echo $crystal_res['planet'];?>
								</td>
								<td><?echo parseInteger($crystal_res['asteroids']);?>
								<td><?echo $minus.parseInteger($crystal_res['factory_prod']);?></td>
								</td>
								<td><?echo parseInteger($crystal_res['total']);?></td>
							</tr>
							<tr>
								<td>Erbium</td>
								<td>
								<?echo $erbium_res['planet'];?>
								</td>
								<td><?echo parseInteger($erbium_res['asteroids']);?>
								<td><?echo $minus.parseInteger($erbium_res['factory_prod']);?></td>
								</td>
								<td><?echo parseInteger($erbium_res['total']);?></td>
							</tr>
							<tr>
								<td>Titanium</td>
								<td>0</td>
								<td>0</td>
								<td><?echo parseInteger($titanium_production);?></td>
								<td><?echo parseInteger($titanium_production);?></td>
							</tr>
						</table>
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
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="30%">Donate resources</td>
		<td width="65%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="2%" background="img/border/L.gif">&nbsp;</td>
		<td width="95%" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top">
						<form method="POST" action="main.php?mod=office&act=resources&do=donate">
						<table border="0" width="800">
							<tr>
								<td width="100" background="img/bg_balk.jpg" align="center"><b>Type resource</b></td>
								<td width="100" background="img/bg_balk.jpg" align="center"><b>Amount</b></td>
								<td width="600" background="img/bg_balk.jpg"><b>Donate to</b></td>
							</tr>
							<tr>
								<td align="center">
									<select name="res_type">
										<option value="steel">Steel</option>
										<option value="crystal">Crystal</option>
										<option value="erbium">Erbium</option>
										<option value="titanium">Titanium</option>
									</select>
								</td>
								<td align="center"><input type="text" name="amount" size="15"></td>
								<td>
									<select name="donate_to">
										<option value="fund">Galactic Fund</option>
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
								<td colspan="3" width="800">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="3" width="800" align="center"><input type="submit" name="sendonation" value="Send donation"></td>
							</tr>
						</table>
						</form>
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
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="30%">Asteroid control</td>
		<td width="65%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="2%" background="img/border/L.gif">&nbsp;</td>
		<td width="95%" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top">
						<table border="0" width="800">
							<tr>
								<td width="800" background="img/bg_balk.jpg" colspan="2"><b>Asteroid overview</b></td>
							</tr>
							<tr>
								<td width="250">Steel asteroids:</td>
								<td width="550"><?echo parseInteger($playerdata['roid_steel']);?></td>
							</tr>
							<tr>
								<td width="250">Crystal asteroids:</td>
								<td width="550"><?echo parseInteger($playerdata['roid_crystal']);?></td>
							</tr>
							<tr>
								<td width="250">Erbium asteroids:</td>
								<td width="550"><?echo parseInteger($playerdata['roid_erbium']);?></td>
							</tr>
							<tr>
								<td width="250">Unused asteroids:</td>
								<td width="550"><?echo parseInteger($playerdata['roid_unused']);?></td>
							</tr>
							<tr>
								<td width="800" colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td width="250">Total asteroids:</td>
								<td width="550"><?echo parseInteger($playerdata['roid_steel'] + $playerdata['roid_crystal'] + $playerdata['roid_erbium'] + $playerdata['roid_unused']);?></td>
							</tr>
						</table><br>
						<form method="POST" action="main.php?mod=office&act=resources&do=initasteroids">
						<table border="0" width="800">
							<tr>
								<td width="800" background="img/bg_balk.jpg" colspan="2"><b>Asteroid initiation</b></td>
							</tr>
							<tr>
								<td width="800" colspan="2">Asteroid initiation will cost <?echo parseInteger($asteroid_initcost);?> steel resource, but raises with every newly initiated asteroid.</td>
							</tr>
							<tr>
								<td width="250">Amount of unused asteroids to initiate:</td>
								<td width="550"><input type="text" name="asteroid_amount" size="11"></td>
							</tr>
							<tr>
								<td width="250">Resource type which should be harvested:</td>
								<td width="550">
									<select name="asteroid_resource">
										<option value="steel">Steel</option>
										<option value="crystal">Crystal</option>
										<option value="erbium">Erbium</option>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2" width="800" align="center"><input type="submit" name="initasteroids" value="  Initiate asteroids  "></td>
							</tr>
						</table>
						</form>
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
<?
if (checkItem($playerdata['id'], $TITANIUM_FACTORY)) {
?>
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="30%">Titanium factory control</td>
		<td width="65%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="2%" background="img/border/L.gif">&nbsp;</td>
		<td width="95%" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top">
						<table border="0" width="800">
							<tr>
								<td width="800" background="img/bg_balk.jpg" colspan="2"><b>Current production process</b></td>
							</tr>
							<tr>
								<td width="250">Steel cost:</td>
								<td width="550"><?echo parseInteger($steel_res['factory_prod']);?></td>
							</tr>
							<tr>
								<td width="250">Crystal cost:</td>
								<td width="550"><?echo parseInteger($crystal_res['factory_prod']);?></td>
							</tr>
							<tr>
								<td width="250">Erbium cost:</td>
								<td width="550"><?echo parseInteger($erbium_res['factory_prod']);?></td>
							</tr>
							<tr>
								<td width="800" colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td width="250">Titanium production each tick:</td>
								<td width="550">
								<?
								echo parseInteger($titanium_production);
								?>
								</td>
							</tr>
						</table><br>
						<form method="POST" action="main.php?mod=office&act=resources&do=altertitaniumprod">
						<table border="0" width="800">
							<tr>
								<td width="800" background="img/bg_balk.jpg" colspan="2"><b>Alter titanium production process</b></td>
							</tr>
							<tr>
								<td width="250">Steel investment:</td>
								<td width="550">
									<select name="steelinv">
										<?
										for ($cntr = 0; $cntr <= 100; $cntr += 10) {
											if ($cntr == $steel_investment) { echo '<option value="'.$cntr.'" selected>'.$cntr.'%</option>'; }
											else { echo '<option value="'.$cntr.'">'.$cntr.'%</option>'; }
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td width="250">Crystal investment:</td>
								<td width="550">
									<select name="crystalinv">
										<?
										for ($cntr = 0; $cntr <= 100; $cntr += 10) {
											if ($cntr == $crystal_investment) { echo '<option value="'.$cntr.'" selected>'.$cntr.'%</option>'; }
											else { echo '<option value="'.$cntr.'">'.$cntr.'%</option>'; }
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td width="250">Erbium investment:</td>
								<td width="550">
									<select name="erbiuminv">
										<?
										for ($cntr = 0; $cntr <= 100; $cntr += 10) {
											if ($cntr == $erbium_investment) { echo '<option value="'.$cntr.'" selected>'.$cntr.'%</option>'; }
											else { echo '<option value="'.$cntr.'">'.$cntr.'%</option>'; }
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td width="800" colspan="2" align="center"><input type="submit" name="altertitaniumprod" value="  Submit changes  "></td>
							</tr>
						</table>
						</form>
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
<?
}
?>