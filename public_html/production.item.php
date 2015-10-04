<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}

$type_id = secureData($_GET['type']);
$sql_getitems = "SELECT * FROM $table[items] WHERE type_id = '$type_id'";
$rec_getitems = mysql_query($sql_getitems) or die(mysql_error());

$sql_getitemtype = "SELECT * FROM $table[itemtypes] WHERE `id` = '$type_id'";
$res_getitemtype = mysql_fetch_array(mysql_query($sql_getitemtype));
if (!isset($do)) { $act = secureData($_GET['do']); }
if (!isset($do)) { $act = secureData($_POST['do']); }

if ($do == 'build') {
	if (!isset($item)) { $item = secureData($_GET['item']); }
	if (!isset($item)) { $item = secureData($_POST['item']); }
	$error = tryProductionAdd($playerdata['id'], $type_id, $item);
}
if ($do == 'cancelprod') {
	$prod_id = secureData($_GET['prod_id']);
	
	$sql_findcprod = 
	 			"SELECT
					$table[productions].id, $table[productions].type_id, $table[productions].item_id, $table[productions].ready_tick,
					$table[items].cost_steel, $table[items].cost_crystal, $table[items].cost_erbium, $table[items].cost_titanium
				FROM $table[productions] 
				INNER JOIN $table[items] ON $table[productions].item_id = $table[items].id
				WHERE $table[productions].id = '$prod_id' AND $table[productions].player_id = $playerdata[id]";
	$res_findcprod = mysql_query($sql_findcprod);
	$num_findcprod = mysql_num_rows($res_findcprod);
	if ($num_findcprod > 0) {
		$rec_findcprod = mysql_fetch_assoc($res_findcprod);
		$sql_cancelprod = "DELETE FROM $table[productions] WHERE `id` = '$rec_findcprod[id]'";
		mysql_query($sql_cancelprod);
		if (mysql_affected_rows() > 0) {
			$playerdata['res_steel'] += ($rec_findcprod['cost_steel'] * 0.80);
			$playerdata['res_crystal'] += ($rec_findcprod['cost_crystal'] * 0.80);
			$playerdata['res_erbium'] += ($rec_findcprod['cost_erbium'] * 0.80);
			$playerdata['res_titanium'] += ($rec_findcprod['cost_titanium'] * 0.80);
			updatePlayerData(IB_PLAYER_ID, $playerdata);
			$error = 2;
		} else {
			$error = 106;
		}
	} else {
		$error = 107;
	}
}

switch($error) {
	case 1:
	$msg = "Succesfully added production.";
	break;
	case 2:
	$msg = 'Succesfully canceled production.<br />You got 80% of the spent resources back.';
	break;
	case 101:
	$msg = "This item has already been built/researched!";
	break;
	case 102:
	$msg = "The item you selected to build is already in production.";
	break;
	case 103:
	$msg = "You can't do more than one construction or research. Please wait until the current one is finished.";
	break;
	case 104:
	$msg = "Depencies are incorrect. You are missing constructions/researches for producing this.";
	break;
	case 105:
	$msg = "You have not enough resources to build/research this item.";
	break;
	case 106:
	$msg = 'The production was found, but not deleted. This is probably a bug.';
	break;
	case 107:
	$msg = 'There is no such production for your planet.';
	break; 
}

$playerdata = getPlayerdata($playerdata['id']);
if ($msg) {
	if ($error < 100) { $error_color = 'green'; }
	else { $error_color = 'red'; }
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
		<td width="180">Current available <?echo strtolower($res_getitemtype['type']);?></td>
		<td width="612" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="4" background="img/border/L.gif">&nbsp;</td>
		<td width="696" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="800">
				<tr>
					<td valign="top">
						<table border="0" width="800">
							<tr>
								<td width="350" background="img/bg_balk.jpg"><b><? echo $res_getitemtype['type']; ?> name</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Steel</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Crystal</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Erbium</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Titanium</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>ETA</b></td>
								<td width="96" background="img/bg_balk.jpg">&nbsp;</td>
							</tr>
							<?
							while ($res = mysql_fetch_array($rec_getitems)) {
								$eta = null;
								if (!checkitem($playerdata['id'], $res['depends'])) { continue; }
								if (!checkItem($playerdata['id'], $res['id'])) {
									if (!checkProduction($playerdata['id'], $type_id, $res['id'])) {
										$line = '<a href="main.php?mod=production&act=item&do=build&type='.$type_id.'&item='.$res[id].'"><b>'.$res_getitemtype['build_text'].'</b></a>';
									} else {
										$eta  = getProductionEta($playerdata['id'], $type_id, $res['id']);
										$line = $res_getitemtype['building_text'];
									}
								} else {
									$line = 'Completed';
								}
								if (!$eta) { $eta = $res['eta']; }
							?>
							<tr>
								<td><?=$res['name']; ?></td>
								<td><?=parseInteger($res['cost_steel']); ?></td>
								<td><?=parseInteger($res['cost_crystal']); ?></td>
								<td><?=parseInteger($res['cost_erbium']); ?></td>
								<td><?=parseInteger($res['cost_titanium']); ?></td>
								<td><?=$eta; ?></td>
								<td><?=$line;?></td>
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
<?
$sql_findprod = "SELECT
					$table[productions].id, $table[productions].type_id, $table[productions].item_id, $table[productions].ready_tick,
					$table[items].name
				FROM $table[productions] 
				INNER JOIN $table[items] ON $table[productions].item_id = $table[items].id
				WHERE $table[productions].type_id = '$type_id' AND $table[productions].player_id = $playerdata[id]";
$res_findprod = mysql_query($sql_findprod);
$num_findprod = mysql_num_rows($res_findprod);
if ($num_findprod > 0) {
?>
<br /><br />
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="220">Current <?echo strtolower($res_getitemtype['type']);?> in production</td>
		<td width="572" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="4" background="img/border/L.gif">&nbsp;</td>
		<td width="696" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="800">
				<tr>
					<td valign="top">
						<table border="0" width="800">
							<tr>
								<td width="250" background="img/bg_balk.jpg"><b><? echo $res_getitemtype['type']; ?> name</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>ETA</b></td>
								<td width="500" background="img/bg_balk.jpg">&nbsp;</td>
							</tr>
							<?
							while ($rec_findprod = mysql_fetch_assoc($res_findprod)) {
								$eta = $rec_findprod['ready_tick'] - IB_TICK_CURRENT;
							?>
							<form method="POST" action="main.php?mod=production&act=item&type=<?=$type_id?>&do=cancelprod&prod_id=<?=$rec_findprod['id']?>">
							<tr>
								<td><?=$rec_findprod['name'];?></td>
								<td><?=$eta;?></td>
								<td align="left"><input type="submit" name="docancel" value="Cancel"></td>
							</tr>
							</form>
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
<?
}
?>