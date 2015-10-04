<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}

$sql_getships = "SELECT * FROM $table[ships]";
$rec_getships = mysql_query($sql_getships) or die(mysql_error());

if (!isset($do)) { $act = secureData($_GET['do']); }
if (!isset($do)) { $act = secureData($_POST['do']); }
if ($do == 'move') {
	$amount = secureData($_POST['amount']);
	$ship_id = secureData($_POST['ship_id']);
	$from = secureData($_POST['from']);
	$to = secureData($_POST['to']);

	$error = 0;
	if ($amount > getShipsOnFleet($playerdata[id], $ship_id, $from)) { $error = 1; }
	if ($from == $to) { $error = 2; }

	if ($error == 0) {
		moveShips($playerdata['id'], $ship_id, $amount, $from, $to);
	}

	switch($error) {
		case 0:
			$msg = "Succesfully moved ships.";
			break;
		case 1:
			$msg = "You don't have that amount of ships to move from fleet $from to $to";
			break;
		case 2:
			$msg = "You can't move ships to the same fleet.";
			break;
	}

}
if ($do == 'order') {
	$error = 1;
	
	switch($error) {
		case 0:
			$msg = "Your order was succesfully recieved by the fleet.";
			break;
		case 1:
			$msg = "You can't send orders yet, because we're still in development ;-)";
			break;
		}
}
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
?>
<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
                <td width="180">Fleet overview</td>
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
                                                                <td width="200" background="img/bg_balk.jpg"><b>Total</b></td>
                                                                <td width="120" background="img/bg_balk.jpg"><b>Base (cloacked)</b></td>
                                                                <td width="120" background="img/bg_balk.jpg"><b>Base (uncloacked)</b></td>
                                                                <td width="120" background="img/bg_balk.jpg"><b>Fleet 1</b></td>
                                                                <td width="120" background="img/bg_balk.jpg"><b>Fleet 2</b></td>
                                                                <td width="120" background="img/bg_balk.jpg"><b>Fleet 3</b></td>
                                                        </tr>
                                                        <?
                                                        while ($res_getships = mysql_fetch_array($rec_getships)) {
                                                        	if (!checkitem($playerdata['id'], $res_getships['depends'])) { continue; }
                                                        	$sql_stock = "SELECT `amount` FROM $table[playerunit] WHERE `player_id` = '$playerdata[id]' AND `unit_id` = '$res_getships[id]'";
                                                        	$rec_stock = mysql_fetch_array(mysql_query($sql_stock));
                                                        	if ($rec_stock['amount'] <= 0) { $stock = 0; }
                                                        	else { $stock=$rec_stock['amount']; }

                                                        	$sql_fleets = "SELECT * FROM $table[fleet] WHERE `player_id` = '$playerdata[id]' AND `ship_id` = '$res_getships[id]'";
                                                        	$rec_fleets = mysql_fetch_array(mysql_query($sql_fleets));
														?>
														<tr>
															<td><?echo $stock.' '.$res_getships['name'].'s'; ?></td>
															<td><?echo $rec_fleets['base_cloacked'];?></td>
															<td><?echo $rec_fleets['base_uncloacked'];?></td>
															<td><?echo $rec_fleets['fleet_1'];?></td>
															<td><?echo $rec_fleets['fleet_2'];?></td>
															<td><?echo $rec_fleets['fleet_3'];?></td>
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
                <td width="180">Fleet movements</td>
                <td width="612" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
                <td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
        </tr>
        <tr>
                <td width="4" background="img/border/L.gif">&nbsp;</td>
                <td width="696" height="100%" valign="top" colspan="2">
                        <table border="0" cellpadding="0" cellspacing="0" width="800">
                                <tr>
                                        <td valign="top">
                                        		<form method="POST" action="main.php?mod=office&act=fleet&do=move">
                                                <table border="0" width="800">
                                                        <tr>
                                                                <td width="200" background="img/bg_balk.jpg"><b>Amount</b></td>
                                                                <td width="200" background="img/bg_balk.jpg"><b>Ship type</b></td>
                                                                <td width="200" background="img/bg_balk.jpg"><b>From</b></td>
                                                                <td width="200" background="img/bg_balk.jpg"><b>To</b></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="200" align="center"><input type="text" name="amount" size="25"></td>
                                                                <td width="200" align="center">
                                                                	<select size="1" name="ship_id" style="font-family: Verdana; color: #787878; font-size: 7.5pt; background-color: #ffffff; border: 1px solid #787878;">
                                                                	<?
                                                                	$rec_getships = mysql_query($sql_getships) or die(mysql_error());
                                                                	while ($res_getships = mysql_fetch_array($rec_getships)) {
																	?>
																	<option value="<?echo $res_getships['id'];?>"><?echo $res_getships['name'];?></option>
																	<?
                                                                	}
																	?>
                                                                	</select>
                                                                </td>
                                                                <td width="200" align="center">
                                                                	<select size="1" name="from" style="font-family: Verdana; color: #787878; font-size: 7.5pt; background-color: #ffffff; border: 1px solid #787878;">
																		<option value="base_cloacked">Base (cloacked)</option>
																		<option value="base_uncloacked">Base (uncloacked)</option>
																		<option value="fleet_1">Fleet 1</option>
																		<option value="fleet_2">Fleet 2</option>
																		<option value="fleet_3">Fleet 3</option>
                                                                	</select>
                                                                </td>
                                                                <td width="200" align="center">
                                                                	<select size="1" name="to" style="font-family: Verdana; color: #787878; font-size: 7.5pt; background-color: #ffffff; border: 1px solid #787878;">
																		<option value="base_cloacked">Base (cloacked)</option>
																		<option value="base_uncloacked">Base (uncloacked)</option>
																		<option value="fleet_1">Fleet 1</option>
																		<option value="fleet_2">Fleet 2</option>
																		<option value="fleet_3">Fleet 3</option>
                                                                	</select>
                                                                </td>
                                                        </tr>
                                                        <tr height="15"><td colspan="4">&nbsp;</td></tr>
                                                        <tr><td align="center"colspan="4"><input type="submit" name="moveships" value="  Move ships  "></td></tr>
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
                <td width="180">Fleet missions</td>
                <td width="612" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
                <td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
        </tr>
        <tr>
                <td width="4" background="img/border/L.gif">&nbsp;</td>
                <td width="696" height="100%" valign="top" colspan="2">
                        <table border="0" cellpadding="0" cellspacing="0" width="800">
                                <tr>
                                        <td valign="top">
                                        		<form method="POST" action="main.php?mod=office&act=fleet&do=order">
                                                <table border="0" width="800">
                                                        <tr>
                                                                <td width="200" background="img/bg_balk.jpg"><b>Fleet</b></td>
                                                                <td width="200" background="img/bg_balk.jpg"><b>Order</b></td>
                                                                <td width="200" background="img/bg_balk.jpg"><b>Target</b></td>
                                                                <td width="200" background="img/bg_balk.jpg">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                                <td width="200" align="center">
                                                                	<select size="1" name="to" style="font-family: Verdana; color: #787878; font-size: 7.5pt; background-color: #ffffff; border: 1px solid #787878;">
																		<option value="fleet_1">Fleet 1</option>
																		<option value="fleet_2">Fleet 2</option>
																		<option value="fleet_3">Fleet 3</option>
                                                                	</select>
                                                                </td>
                                                                <td width="200" align="center">
                                                              		<select size="1" name="to" style="font-family: Verdana; color: #787878; font-size: 7.5pt; background-color: #ffffff; border: 1px solid #787878;">
																		<option value="fleet_1">Attack</option>
																		<option value="fleet_2">Defend</option>
																		<option value="fleet_3">Retreat</option>
                                                                	</select>
                                                                </td>
                                                                <td width="200" align="center"><input type="text" name="x" size="1"> : <input type="text" name="y" size="1"> : <input type="text" name="z" size="1"></td>
                                                                <td width="200" align="center"><input type="submit" name="order" value="  Give order  "></td>
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