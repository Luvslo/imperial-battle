<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}

$sql_getships = "SELECT * FROM $table[ships]";
$rec_getships = mysql_query($sql_getships) or die(mysql_error());

$sql_getdefense = "SELECT * FROM $table[defense]";
$rec_getdefense = mysql_query($sql_getdefense) or die(mysql_error());

if (!isset($do)) { $act = secureData($_GET['do']); }
if (!isset($do)) { $act = secureData($_POST['do']); }

if ($do == 'build') {
	if (!isset($type)) { $item = secureData($_GET['type']); }
	if (!isset($id)) { $item = secureData($_GET['id']); }
	if (!isset($amount)) { $amount = secureData($_POST['amount']); }
	if ($amount < 1) { $error = 103; }

	switch ($type) {
		case 'ship':
		$item_table = $table['ships'];
		$item_id = 3;
		break;
		case 'defense':
		$item_table = $table['defense'];
		$item_id = 4;
		break;
	}
	if ($error< 100) $error = tryShipDefenseProductionAdd($playerdata['id'], $item_id, $item_table, $id, $amount);
}
if ($do == 'cancelprod') {
	$prod_id = secureData($_GET['prod_id']);
	$sql_findprod = "SELECT `id`, `player_id`, `type_id`, `item_id`, `amount` FROM $table[productions] WHERE `id` = '$prod_id' AND `player_id` = '$playerdata[id]'";
	$res_findprod = mysql_query($sql_findprod);
	$num_findprod = mysql_num_rows($res_findprod);
	if ($num_findprod > 0) {
		$rec_findprod = mysql_fetch_assoc($res_findprod);
		$item_id = $rec_findprod['item_id'];
		$amount = $rec_findprod['amount'];
		$sql_findcosts = "SELECT `id`, `cost_steel`, `cost_crystal`, `cost_erbium`, `cost_titanium` FROM $table[ships] WHERE `id` = '$item_id'";
		$res_findcosts = mysql_query($sql_findcosts);
		if (mysql_num_rows($res_findcosts) > 0) {
			$rec_findcosts = mysql_fetch_assoc($res_findcosts);
			
			$playerdata['res_steel'] += (($amount * $rec_findcosts['cost_steel']) * 0.8);
			$playerdata['res_crystal'] += (($amount * $rec_findcosts['cost_crystal']) * 0.8);
			$playerdata['res_erbium'] += (($amount * $rec_findcosts['cost_erbium']) * 0.8);
			$playerdata['res_titanium'] += (($amount * $rec_findcosts['cost_titanium']) * 0.8);
			
			$sql_cancelprod = "DELETE FROM $table[productions] WHERE `id` = '$prod_id'";
			mysql_query($sql_cancelprod) or die(mysql_error());
			updatePlayerData($playerdata['id'], $playerdata);
			$error = 2;
		} else {
			$error = 104;
		}
	} else {
		$error = 105;
	}
}
switch($error) {
	case 1:
	$msg = "Succesfully added production.";
	break;
	case 2:
	$msg = 'Succesfully canceled the production. You got 80% of the spent resources back.';
	break;
	case 101:
	$msg = "There are depency errors. Are you trying to cheat? ;-)";
	break;
	case 102:
	$msg = "You have not enough resources to build this amount of ships/defense!";
	break;
	case 103:
	$msg = "Tsk! Don't build zero or negative amount ships.";
	break;
	case 104:
	$msg= 'There was an error when trying to find the costs for the production to cancel. Contact the administrators.';
	break;
	case 105:
	$msg = 'There is no such production for your planet.';
	break;
}
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
$playerdata = getPlayerdata($playerdata['id']);
?>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="180">Current available ships</td>
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
								<td width="150" background="img/bg_balk.jpg"><b>Unit name</b></td>
								<td width="150" background="img/bg_balk.jpg"><b>In stock</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Steel</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Crystal</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Erbium</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Titanium</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>ETA</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Amount</b></td>
								<td width="200" background="img/bg_balk.jpg">&nbsp;</td>
							</tr>
							<?
							while ($res = mysql_fetch_array($rec_getships)) {
								$max = array();
								$steel_max = 0;
								$crystal_max = 0;
								$erbium_max = 0;
								$titanium_max = 0;

								if (!checkitem($playerdata['id'], $res['depends'])) { continue; }
								$sql_stock = "SELECT `amount` FROM $table[playerunit] WHERE `player_id` = '$playerdata[id]' AND `unit_id` = '$res[id]'";
								$rec_stock = mysql_fetch_array(mysql_query($sql_stock));
								if ($rec_stock['amount'] <= 0) { $stock = 0; }
								else { $stock=$rec_stock['amount']; }

								if ($res['cost_steel'] > 0) {
									$max['steel'] = floor($playerdata['res_steel'] / $res['cost_steel']);
								}
								if ($res['cost_crystal'] > 0) {
									$max['crystal'] = floor($playerdata['res_crystal'] / $res['cost_crystal']);
								}
								if ($res['cost_erbium'] > 0) {
									$max['erbium'] = floor($playerdata['res_erbium'] / $res['cost_erbium']);
								}
								if ($res['cost_titanium'] > 0) {
									$max['titanium'] = floor($playerdata['res_titanium'] / $res['cost_titanium']);
								}
								sort($max);
							?>
							<form method="POST" action="main.php?mod=production&act=factory&do=build&type=ship&id=<?echo $res['id'];?>">
							<tr>
								<td><?=$res['name']; ?></td>
								<td><?=$stock; ?></td>
								<td><?=parseInteger($res['cost_steel']); ?></td>
								<td><?=parseInteger($res['cost_crystal']); ?></td>
								<td><?=parseInteger($res['cost_erbium']); ?></td>
								<td><?=parseInteger($res['cost_titanium']); ?></td>
								<td><?=$res['eta']; ?></td>
								<td><input type="text" name="amount" size="5"></td>
								<td><input type="submit" name="buildships" value=" Build "><? echo ' (max: '.$max[0].')';?></td>
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
if (false) {
?>
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="180">Current available defense</td>
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
								<td width="400" background="img/bg_balk.jpg"><b>Unit name</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Steel</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Crystal</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Erbium</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Titanium</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>ETA</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Amount</b></td>
								<td width="100" background="img/bg_balk.jpg">&nbsp;</td>
							</tr>
							<?
							while ($res = mysql_fetch_array($rec_getdefense)) {
								if (!checkitem($playerdata['id'], $res['depends'])) { break; }
							?>
							<form method="POST" action="main.php?mod=factory&act=build&type=defense&id=<?echo $res['id'];?>">
							<tr>
								<td><?echo $res['name']; ?></td>
								<td><?echo $res['cost_steel']; ?></td>
								<td><?echo $res['cost_crystal']; ?></td>
								<td><?echo $res['cost_erbium']; ?></td>
								<td><?echo $res['cost_titanium']; ?></td>
								<td><?echo $res['eta']; ?></td>
								<td><input type="text" name="amount" size="5"></td>
								<td><input type="submit" name="buildships" value=" Build "></td>
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
<?
$sql_findprod = "SELECT
					$table[productions].id, $table[productions].type_id, $table[productions].item_id, $table[productions].ready_tick,
					$table[productions].amount, $table[ships].name
				FROM $table[productions] 
				INNER JOIN $table[ships] ON $table[productions].item_id = $table[ships].id
				WHERE 
					($table[productions].type_id = '3' OR $table[productions].type_id = '4') 
					AND $table[productions].player_id = $playerdata[id]
				ORDER BY ready_tick";
$res_findprod = mysql_query($sql_findprod);
$num_findprod = mysql_num_rows($res_findprod);
if ($num_findprod > 0) {
?>
<br /><br />
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="220">Current units in production</td>
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
								<td width="250" background="img/bg_balk.jpg"><b>Unit name</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>Amount</b></td>
								<td width="50" background="img/bg_balk.jpg"><b>ETA</b></td>
								<td width="450" background="img/bg_balk.jpg">&nbsp;</td>
							</tr>
							<?
							while ($rec_findprod = mysql_fetch_assoc($res_findprod)) {
								$eta = $rec_findprod['ready_tick'] - IB_TICK_CURRENT;
							?>
							<form method="POST" action="main.php?mod=production&act=factory&do=cancelprod&prod_id=<?=$rec_findprod['id']?>">
							<tr>
								<td><?=$rec_findprod['name'];?></td>
								<td><?=parseInteger($rec_findprod['amount']);?></td>
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