<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}

$sql_getships = "SELECT * FROM $table[ships]";
$rec_getships = mysql_query($sql_getships) or die(mysql_error());

if (!isset($do)) { $do = secureData($_GET['do']); }
if (!isset($do)) { $do = secureData($_POST['do']); }

if (!isset($fleet_id)) { $fleet_id = secureData($_GET['fleet_id']); }
if (!isset($fleet_id)) { $fleet_id = secureData($_POST['fleet_id']); }

if (!isset($ship_id)) { $ship_id = secureData($_GET['ship_id']); }
if (!isset($ship_id)) { $ship_id = secureData($_POST['ship_id']); }

if ($do == 'fleetaction') {
	if ($_POST['order']) { $do = 'orderfleet'; }
	if ($_POST['edit']) {  $do = 'editfleet'; }
	if ($_POST['delete']) { $do = 'deletefleet'; }
}
if ($do == 'createfleet') {
	if (getFleetAmount($playerdata['id']) < getMaxFleets($playerdata['id'])) {
		$sql_createfleet = "INSERT INTO `$table[playerfleet]` (`player_id`) VALUES ('$playerdata[id]')";
		mysql_query($sql_createfleet) or die(mysql_error());
		$do = 'editfleet';
		$ship_id = 0;
		$fleet_id = mysql_insert_id();
	} else {
		$error = 102;
	}
}
if ($do == 'editdelships') {
	$edit_submit = secureData($_POST['editships']);
	$del_submit = secureData($_POST['delships']);
	if ($edit_submit && !$del_submit) {
		$ship_id = secureData($_GET['ship_id']);
		$fleet_id = secureData($_GET['fleet_id']);
		$do = 'editfleet';
	}
	if (!isFleetHome($playerdata['id'], $fleet_id)) {
		$error = 107;
	}
	if ($del_submit && !$edit_submit && ($error < 100)) {
		$fleetship_id = secureData($_GET['fleetship_id']);
		$fleet_id = secureData($_GET['fleet_id']);
		$ship_id = 0;
		$sql_delfleetship = "DELETE FROM $table[playerfleet_ships] WHERE `id` = '$fleetship_id' AND `player_id` = '$playerdata[id]'";
		mysql_query($sql_delfleetship) or die(mysql_error());
		$do = 'editfleet';
	}
}
if ($do == 'editfleet') {

	if (!isset($ship_id)) { $ship_id = 0; debug('wtf');}
	$shipsubmit_add = secureData($_POST['addfleetship']);
	$shipsubmit_edit = secureData($_POST['editfleetship']);

	if (!isFleetHome($playerdata['id'], $fleet_id)) {
		$error = 107;
	}
	if ($shipsubmit_edit && !$shipsubmit_add && ($error < 100)) {
		if ($amountfleet < 1) {
			$error = 100;
		}
		if ($amountfleet > (getBaseShips($playerdata['id'], $ship_id) + getFleetShips($playerdata['id'], $fleet_id, $ship_id))) {
			$error = 101;
		}
		if ($error < 100) {
			$fleet_id = secureData($_GET['fleet_id']);
			$ship_id = secureData($_POST['ship_id']);
			$amountfleet = secureData($_POST['amountfleet']);
			$prim_target = secureData($_POST['prim_target']);
			$sec_target = secureData($_POST['sec_target']);

			$sql_egetships = "SELECT `id`, `amount` FROM $table[playerfleet_ships] WHERE `player_id` = '$playerdata[id]' AND `fleet_id` = '$fleet_id' AND `ship_id` = '$ship_id'";
			$rec_egetships = mysql_query($sql_egetships);
			$num_egetships = mysql_num_rows($rec_egetships);
			if ($num_egetships > 0) {
				$res_egetships = mysql_fetch_array($rec_egetships);
				$fleetship_id = $res_egetships['id'];
				$sql_updfleetship = "UPDATE $table[playerfleet_ships] SET `amount` = '$amountfleet', `primary` = '$prim_target', `secondary` = '$sec_target' WHERE `player_id` = '$playerdata[id]' AND `fleet_id` = '$fleet_id' AND `ship_id` = '$ship_id'";
				mysql_query($sql_updfleetship) or die(mysql_error());
			}
		}
	}
	if ($shipsubmit_add && !$shipsubmit_edit && ($error < 100)) {
		if ($amountfleet < 1) {
			$error = 100;
		}
		if ($amountfleet > (getBaseShips($playerdata['id'], $ship_id) + getFleetShips($playerdata['id'], $fleet_id, $ship_id))) {
			$error = 101;
		}
		if ($error < 100) {
			$fleet_id = secureData($_GET['fleet_id']);
			$ship_id = secureData($_POST['ship_id']);
			$amountfleet = secureData($_POST['amountfleet']);
			$prim_target = secureData($_POST['prim_target']);
			$sec_target = secureData($_POST['sec_target']);

			$sql_insfleetship = "INSERT INTO `$table[playerfleet_ships]` (`player_id`, `fleet_id`, `ship_id`, `amount`, `primary`, `secondary`)
									VALUES ('$playerdata[id]', '$fleet_id', '$ship_id', '$amountfleet', '$prim_target', '$sec_target')";
			mysql_query($sql_insfleetship) or die(mysql_error());
			$ship_id = 0;
		}
	}
}
if ($do == 'deletefleet') {
	if (isFleetHome($playerdata['id'], $fleet_id)) {
		$sql_delfleet = "DELETE FROM $table[playerfleet] WHERE `id` = '$fleet_id' AND `player_id` = '$playerdata[id]'";
		$sql_delfleetships = "DELETE FROM $table[playerfleet_ships] WHERE `fleet_id` = '$fleet_id' AND `player_id` = '$playerdata[id]'";
		mysql_query($sql_delfleet) or die(mysql_error());
		mysql_query($sql_delfleetships) or die(mysql_error());
	} else {
		$error = 109;
	}

}

if ($do == 'orderfleet') {
	$fleet_id = secureData($_GET['fleet_id']);
}
if ($do == 'order') {
	$fleet_id = secureData($_GET['fleet_id']);

	$x = secureData($_POST['x']);
	$y = secureData($_POST['y']);
	$z = secureData($_POST['z']);
	$target_id =  getPlayerId($x, $y, $z);

	$action = secureData($_POST['action']);
	$action_time = secureData($_POST['action_time']);
	if (getFleetShipAmount($playerdata['id'], $fleet_id) == 0) {
		$error = 104;
	}
	if (!isFleetHome($playerdata['id'], $fleet_id) && $action != 'home') {
		$error = 105;
	}
	if (isFleetHome($playerdata['id'], $fleet_id) && $action == 'home') {
		$error = 110;
	}
	if ($target_id == $playerdata['id']) { $error = 108; }
	if ((getPlayerProperty($target_id, 'score') < ($playerdata['score'] / 3)) && ($action == 'attack')) { $error = 111; }
	if (!$action) { $action = 'home'; }
	if (!$action_time) { $action_time = 0; }
	if ($action == 'home') {
		$target_id = $playerdata['id'];
		$action_time = 0;
	}
	if ($target_id == 0) {
		$error = 103;
	}
	if ($error < 100) {
		$total_fuel = 0;
		$sql_fleetdata = "SELECT $table[ships].id, $table[ships].traveltime, $table[ships].fuel, $table[playerfleet_ships].amount
								FROM $table[playerfleet_ships] 
								LEFT JOIN $table[ships] ON $table[ships].id = $table[playerfleet_ships].ship_id
								WHERE $table[playerfleet_ships].fleet_id = '$fleet_id' ORDER BY $table[ships].traveltime DESC";

		$rec_fleetdata = mysql_query($sql_fleetdata);
		$num_fleetdata = mysql_num_rows($rec_fleetdata);
		if ($num_fleetdata > 0) {
			$counter = 0;
			while ($res_fleetdata = mysql_fetch_assoc($rec_fleetdata)) {
				$total_fuel += ($res_fleetdata['amount'] * $res_fleetdata['fuel']);
				if ($counter == 0) { $traveltime = $res_fleetdata['traveltime']; }
				$counter++;
			}
			if (($total_fuel > $playerdata['res_erbium']) && ($action != 'home')){
				$error = 106;
			} else {
				$current_tick = getCurrentTick();
				if ($action != 'home') {
					$playerdata['res_erbium'] -= $total_fuel;
					updatePlayerData($playerdata['id'], $playerdata);
				}
				if ($action == 'home') {
					$sql_oldlfeetdata = "SELECT `player_id`, `target_id`, `action`, `action_start`, `action_time`, `sent_tick` FROM `$table[playerfleet]` WHERE `id` = '$fleet_id'";
					$res_oldfleetdata = mysql_query($sql_oldlfeetdata);
					$num_oldfleetdata = @mysql_num_rows($res_oldfleetdata);
					if ($num_oldfleetdata > 0) {
						$rec_oldfleetdata = mysql_fetch_assoc($res_oldfleetdata);
					}
					/*$sql_travtime = "SELECT $table[ships].traveltime
					FROM $table[ships]
					INNER JOIN $table[playerfleet_ships] ON $table[ships].id = $table[playerfleet_ships].ship_id
					WHERE $table[playerfleet_ships].fleet_id = '$fleet_id'
					ORDER BY `traveltime` DESC";
					$res_travtime = mysql_query($sql_travtime);
					$num_travtime = @mysql_num_rows($res_travtime);
					if ($num_travtime > 0) {
					$rec_travtime = mysql_fetch_assoc($res_travtime);
					}*/

					if (($current_tick - $rec_oldfleetdata['sent_tick']) > $traveltime) {
						$eta = $traveltime;
						$target_xyz = getXYZ($rec_oldfleetdata['target_id']);
						$player_xyz = getXYZ($playerdata['id']);
						$eta_bonus = 0;
						if (($target_xyz[0] == $player_xyz[0]) && ($target_xyz[1] == $player_xyz[1])) { $eta_bonus = 10; debug(3);}
						elseif ($target_xyz[0] == $player_xyz[0]) { $eta_bonus = 5; debug(4); }
						$eta -= $eta_bonus;
					}
					else {
						$eta = $current_tick - $rec_oldfleetdata['sent_tick'];
					}

					$action_start = $current_tick + $eta;

					$sql_sendfleet = "UPDATE $table[playerfleet] SET `action` = '$action', `target_id` = '$target_id', `action_start` = '$action_start', `action_time` = '0' WHERE `id` = '$fleet_id'";
					$xyz = getXYZ($playerdata['id']);
					addNews($rec_oldfleetdata['target_id'], 'Combat', 'Fleet retreat', getRulernameById($playerdata['id']).' of '.getPlanetnameById($playerdata['id']).' from '.$xyz[0].':'.$xyz[1].':'.$xyz[2].' has recalled his fleet.');
				} else {
					$target_xyz = getXYZ($target_id);
					$player_xyz = getXYZ($playerdata['id']);
					$eta_bonus = 0;
					if (($target_xyz[0] == $player_xyz[0]) && ($target_xyz[1] == $player_xyz[1])) { $eta_bonus = 10; }
					elseif ($target_xyz[0] == $player_xyz[0]) { $eta_bonus = 5; }
					$traveltime -= $eta_bonus;
					$starttick = $current_tick + $traveltime;

					$sql_sendfleet = "UPDATE $table[playerfleet] SET `action` = '$action', `target_id` = '$target_id', `action_start` = '$starttick', `action_time` = '$action_time', `sent_tick` = '$current_tick' WHERE `id` = '$fleet_id'";
					if ($action == 'attack') {
						$xyz = getXYZ($playerdata['id']);
						addNews($target_id, 'Combat', 'Incoming hostile fleet', getRulernameById($playerdata['id']).' of '.getPlanetnameById($playerdata['id']).' from '.$xyz[0].':'.$xyz[1].':'.$xyz[2].' is sending a hostile fleet to your planet.<br>The ETA for this fleet is '.$traveltime.'.');
					}
					if ($action == 'defend') {
						$xyz = getXYZ($playerdata['id']);
						addNews($target_id, 'Combat', 'Incoming friendly fleet', getRulernameById($playerdata['id']).' of '.getPlanetnameById($playerdata['id']).' from '.$xyz[0].':'.$xyz[1].':'.$xyz[2].' is sending a friendly fleet to your planet.<br>The ETA for this fleet is '.$traveltime.'.');
					}
				}
				mysql_query($sql_sendfleet) or die(mysql_error());
				switch ($action) {
					case 'attack':
					$error = 1;
					break;
					case 'defend':
					$error = 2;
					break;
					case 'home';
					$error = 3;
					break;
				}
			}
		}
	}
}

switch($error) {
	case 1:
	$msg = "The current fleet is sent out to an attack mission.";
	break;
	case 2:
	$msg = "The current fleet is sent out to an defend mission.";
	break;
	case 3:
	$msg = "The current fleet is returning home.";
	break;
	case 100:
	$msg = "You can't add/edit a negative amount of ships in this fleet.";
	break;
	case 101:
	$msg = "You do not have that amount of ships at base.";
	break;
	case 102:
	$msg = "You can only have reached the maximum amount of fleets.<br />If this amount is lower then three, be sure to research new technology and construct new Fleet Control Units.";
	break;
	case 103:
	$msg = "Invalid target. There is no one located on this coordinates.";
	break;
	case 104:
	$msg = "You can't order empty fleets.";
	break;
	case 105:
	$msg = "This fleet is not at home. You can only retreat an outgoing fleet.";
	break;
	case 106:
	$msg = "You do not have enough erbium to fuel this fleet. The fleet will stay home.<br>The amount of erbium needed is ".parseInteger($total_fuel);
	break;
	case 107:
	$msg = "This fleet is not at home. You can not edit fleets which are on a mission.";
	break;
	case 108:
	$msg = "Attacking your own planet isn't such a good idea, don't you think? :-)";
	break;
	case 109:
	$msg = "This fleet is not at home. You can not delete fleets which are on a mission.";
	break;
	case 110:
	$msg = "The fleet is already home.";
	break;
	case 111:
	$msg = 'The target you want to attack is to small. The targets score should be atleat 1/3 of your own score';
	break;
}

$formcounter = -1;
if (!isset($ship_id)) { $ship_id = 0; }

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

<script>
function submitEditFleet(num) {
	document.forms[num].submit();
}
</script>
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
                                                                <td width="50" background="img/bg_balk.jpg"><b>Fleet #</b></td>
                                                                <td width="50" background="img/bg_balk.jpg"><b>Action</b></td>
                                                                <td width="310" background="img/bg_balk.jpg"><b>Target</b></td>
                                                                <td width="50" background="img/bg_balk.jpg"><b>ETA</b></td>
                                                                <td width="45" background="img/bg_balk.jpg"><b>Ticks</b></td>
                                                                <td width="75" background="img/bg_balk.jpg"><b>Total ships</b></td>
                                                                <td width="220" background="img/bg_balk.jpg" align="center"><b>Actions</b></td>
                                                        </tr>
                                                        <?
                                                        $sql_getfleets = "SELECT `id`, `action`, `target_id`, `action_start`, `action_time` FROM $table[playerfleet] WHERE `player_id` = $playerdata[id] ORDER BY `id`";
                                                        $rec_getfleets = mysql_query($sql_getfleets);
                                                        if (mysql_num_rows($rec_getfleets) < 1) {
                                                        ?>
                                                        <tr>
                                                        	<td colspan="7" align="center">You do not have any active fleets</td>
                                                        </tr>
                                                        <? 
                                                        } else {
                                                        	while ($res_getfleets = mysql_fetch_array($rec_getfleets)) {
                                                        		$total_ships = 0;
                                                        		$sql_totalships = "SELECT `amount` FROM $table[playerfleet_ships] WHERE `fleet_id` = '$res_getfleets[id]'";
                                                        		$rec_totalships = mysql_query($sql_totalships);
                                                        		while ($res_totalships = mysql_fetch_array($rec_totalships)) {
                                                        			$total_ships += $res_totalships['amount'];
                                                        		}
                                                        		$formcounter++;

                                                        		if ($res_getfleets['action'] == 'attack') { $tdclass = 'class="hostile"'; }
                                                        		elseif ($res_getfleets['action'] == 'defend') { $tdclass = 'class="friendly"'; }
                                                        		else { unset($tdclass); }
                                                        ?>
                                                        <form method="POST" action="main.php?mod=office&act=fleet&do=fleetaction&fleet_id=<?=$res_getfleets['id'];?>">
                                                        <tr>
                                                                <td <?if ($tdclass) echo $tdclass;?>><?=$res_getfleets['id'];?></td>
                                                                <td <?if ($tdclass) echo $tdclass;?>><?=$res_getfleets['action'];?></td>
                                                                <td <?if ($tdclass) echo $tdclass;?>><?
                                                                $xyz = getXYZ($res_getfleets['target_id']);
                                                                $rulername = getRulernameById($res_getfleets['target_id']);
                                                                $planetname = getPlanetnameById($res_getfleets['target_id']);
                                                                if ($rulername && $xyz) {
                                                                	echo $xyz[0].':'.$xyz[1].':'.$xyz[2].' ('.$rulername.' of '.$planetname.')';
                                                                } else {
                                                                	echo '&nbsp;';
                                                                }
                                                                ?></td>
                                                                <td <?if ($tdclass) echo $tdclass;?>><?
                                                                if (($res_getfleets['action_start'] > 0) || (($res_getfleets['action_start'] - getCurrentTick()) > 0)) {
                                                                	echo ($res_getfleets['action_start'] - getCurrentTick());
                                                                } else {
                                                                	echo 0;
                                                                }
                                                                ?></td>
                                                                <td <?if ($tdclass) echo $tdclass;?>><?=$res_getfleets['action_time'];?></td>
                                                                <td align="center" <?if ($tdclass) echo $tdclass;?>><?=parseInteger($total_ships);?></td>
                                                                <td align="center"><input type="submit" name="order" value="Order"> <input type="submit" name="edit" value="Edit"> <input type="submit" name="delete" value="Delete"></td>
                                                        </tr>
                                                        </form>
                                                        <?
                                                        	}
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
if ($do == 'editfleet') {
?>
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
                <td width="180">Edit fleet - Fleet # <?=$fleet_id;?></td>
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
                                                		<?

                                                		$sql_shipdata = "SELECT `id`, `name`, `depends`, `eta`, `primary_target`, `secondary_target` FROM `$table[ships]`";
                                                		$rec_shipdata = mysql_query($sql_shipdata);
                                                		$shipdata = array();
                                                		$i = 0;
                                                		while ($res_shipdata = mysql_fetch_assoc($rec_shipdata)) {
                                                			$shipdata[$i] = $res_shipdata;
                                                			$i++;
                                                		}

                                                        ?>
                                                		<tr>
                                                			<td width="800" colspan="7">Current ships in this fleet:</td>
                                                		</tr>
                                                        <tr>
                                                                <td width="135" background="img/bg_balk.jpg"><b>Ship name</b></td>
                                                                <td width="150" background="img/bg_balk.jpg" align="center" colspan="2"><b>Amount in fleet</b></td>
                                                                <td width="95" background="img/bg_balk.jpg" align="center"><b>Travel time</b></td>
                                                                <td width="170" background="img/bg_balk.jpg" align="center"><b>Primary target</b></td>
                                                                <td width="170" background="img/bg_balk.jpg" align="center"><b>Secondary target</b></td>
                                                                <td width="80" background="img/bg_balk.jpg">&nbsp;</td>
                                                        </tr>
                                                        <?
                                                        $sql_fleetships = "SELECT `id`, `ship_id`, `amount`, `primary`, `secondary` FROM `$table[playerfleet_ships]` WHERE `player_id` = '$playerdata[id]' AND `fleet_id` = '$fleet_id'";
                                                        $rec_fleetships = mysql_query($sql_fleetships);
                                                        $num_fleetships = mysql_num_rows($rec_fleetships);

                                                        if ($num_fleetships > 0) {
                                                        	while ($res_fleetships = mysql_fetch_assoc($rec_fleetships)) {
                                                        		$formcounter++;
                                                        		$traveltime = getShipProperty($res_fleetships['ship_id'], 'traveltime');
                                                        ?>
                                                        <form method="POST" action="main.php?mod=office&act=fleet&do=editdelships&fleet_id=<?=$fleet_id;?>&ship_id=<?=$res_fleetships['ship_id'];?>&fleetship_id=<?=$res_fleetships['id'];?>">                                                        
                                                        <tr>
                                                                <td><?echo  getShipProperty($res_fleetships['ship_id'], 'name');?></td>
                                                                <td align="center" colspan="2"><? echo $res_fleetships['amount'];?></td>
                                                                <td align="center"><?=$traveltime-10;?>/<?=$traveltime-5;?>/<?=$traveltime;?></td>
                                                                <td align="center"><? echo getShipProperty($res_fleetships['primary'], 'name');?></td>
                                                                <td align="center"><? echo getShipProperty($res_fleetships['secondary'], 'name');?></td>
                                                                <td align="center"><input type="submit" name="editships" value="Edit"> <input type="submit" name="delships" value="Del"></td>
                                                        </tr>
                                                        </form>
                                                        <?
                                                        	}
                                                        } else {
                                                        ?>
                                                        <tr>
                                                        	<td width="800" colspan="7" align="center">There are no ships in this fleet.</td>
                                                        </tr>
                                                        <?
                                                        }
                                                        ?>
                                                		<tr>
                                                			<td width="800" colspan="7">&nbsp;</td>
                                                		</tr>
                                                		<tr>
                                                			<td width="800" colspan="7">Add ships to the current fleet</td>
                                                		</tr>
                                                        <tr>
                                                                <td width="135" background="img/bg_balk.jpg"><b>Ship name</b></td>
                                                                <td width="75" background="img/bg_balk.jpg" align="center"><b># at base</b></td>
                                                                <td width="75" background="img/bg_balk.jpg" align="center"><b># in fleet</b></td>
                                                                <td width="95" background="img/bg_balk.jpg" align="center"><b><?if ($edit_submit) { echo 'New amount'; } else { echo '# to add'; }?></b></td>
                                                                <td width="170" background="img/bg_balk.jpg" align="center"><b>Primary target</b></td>
                                                                <td width="170" background="img/bg_balk.jpg" align="center"><b>Secondary target</b></td>
                                                                <td width="80" background="img/bg_balk.jpg">&nbsp;</td>
                                                        </tr>
                                                        <?
                                                        $formcounter++;
                                                        $baseships = getBaseShips($playerdata['id'], $ship_id);
                                                        $fleetships = getFleetShips($playerdata['id'], $fleet_id, $ship_id);
                                                        ?>
                                                        <form method="POST" action="main.php?mod=office&act=fleet&do=editfleet&fleet_id=<?=$fleet_id;?>">
                                                        <tr>
                                                        	<td>
                                                        		<select name="ship_id" onChange="submitEditFleet(<?=$formcounter;?>)">
                                                        			<option value="0"<?if ($ship_id == 0) { echo 'selected'; }?>>-</option>
                                                        			<?
                                                        			for ($j = 0; $j < count($shipdata); $j++) {
                                                        				if (checkItem($playerdata['id'], $shipdata[$j]['depends']) && (!inFleet($fleet_id, $shipdata[$j]['id']) || ($edit_submit && inFleetShipCheck($fleet_id, $shipdata[$j]['id'], $ship_id)))) {
                                                        			?>
                                                        					<option value="<?=$shipdata[$j]['id'];?>"<?if ($ship_id == $shipdata[$j]['id']) { echo 'selected'; }?>><?=$shipdata[$j]['name'];?></option>
                                                        			<?
                                                        				}
                                                        			}
                                                        			?>
                                                        		</select>
                                                        	</td>
                                                        	<td align="center"><?if (!$baseships) { echo 0; } else { echo $baseships; }?></td>
                                                        	<td align="center"><?if (!$fleetships) { echo 0; } else { echo $fleetships; }?></td>
                                                            <td align="center"><input type="text" name="amountfleet" size="10" value="<?if (!$fleetships) { echo 0; } else { echo $fleetships; }?>"></td>
                                                            <td align="center">
                                                            	<select name="prim_target">
                                                            		<?
                                                            		if (($ship_id == 0) || (getShipProperty($ship_id, 'primary_target')) == -1) {

																	?>
                                                        				<option value="0" selected>-</option>
                                                        			<?
                                                            		} else {
                                                            			for ($j = 0; $j < count($shipdata); $j++) {
                                                            				if ($edit_submit) {
                                                        			?>
                                                        					<option value="<?=$shipdata[$j]['id'];?>"<?if ($shipdata[$j]['id'] == getFleetShipProperty($ship_id, 'primary')) { echo 'selected'; }?>><?=$shipdata[$j]['name'];?></option>
                                                        			<?
                                                            				} else {
                                                        			?>
                                                        					<option value="<?=$shipdata[$j]['id'];?>"<?if ($shipdata[$j]['id'] == getShipProperty($ship_id, 'primary_target')) { echo 'selected'; }?>><?=$shipdata[$j]['name'];?></option>
                                                        			<?
                                                            				}
                                                            			}
                                                            		}
                                                        			?>
                                                            	</select>
                                                            </td>
                                                            <td align="center">
                                                            	<select name="sec_target">
                                                            		<?
                                                            		if (($ship_id == 0) || (getShipProperty($ship_id, 'primary_target')) == -1) {
																	?>
                                                        				<option value="0" selected>-</option>
                                                        			<?
                                                            		} else {
                                                            			for ($j = 0; $j < count($shipdata); $j++) {
                                                            				if ($edit_submit) {
                                                        			?>
                                                        					<option value="<?=$shipdata[$j]['id'];?>"<?if ($shipdata[$j]['id'] == getFleetShipProperty($ship_id, 'secondary')) { echo 'selected'; }?>><?=$shipdata[$j]['name'];?></option>
                                                        			<?
                                                            				} else {
                                                        			?>
                                                        					<option value="<?=$shipdata[$j]['id'];?>"<?if ($shipdata[$j]['id'] == getShipProperty($ship_id, 'secondary_target')) { echo 'selected'; }?>><?=$shipdata[$j]['name'];?></option>
                                                        			<?
                                                            				}
                                                            			}
                                                            		}
                                                        			?>
                                                            	</select>
                                                            </td>
                                                            <td align="center">
                                                            <?if (inFleet($fleet_id, $ship_id) && $edit_submit) {?>
                                                            <input type="submit" name="editfleetship" value="Edit">
                                                            <? } elseif ($ship_id > 0) {?>
                                                            <input type="submit" name="addfleetship" value="Add">
                                                            <? } ?>
                                                            </td>
                                                        </tr>
                                                        </form>
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
if ($do == 'orderfleet') {
?>
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="180">Order fleet - Fleet # <?=$fleet_id;?></td>
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
                        		<td width="800" colspan="5">Current ships in this fleet:</td>
                        	</tr>
                        	<tr>
                        		<td width="135" background="img/bg_balk.jpg"><b>Ship name</b></td>
                        		<td width="250" background="img/bg_balk.jpg" align="center"><b>Amount in fleet</b></td>
                        		<td width="95" background="img/bg_balk.jpg" align="center"><b>Travel time</b></td>
                        		<td width="160" background="img/bg_balk.jpg" align="center"><b>Primary target</b></td>
                        		<td width="160" background="img/bg_balk.jpg" align="center"><b>Secondary target</b></td>
                        	</tr>
                            <?
                            $sql_fleetinfo = "SELECT `action` FROM $table[playerfleet] WHERE id = '$fleet_id'";
                            $res_fleetinfo = mysql_query($sql_fleetinfo) or die(mysql_error());
                            if (@mysql_num_rows($res_fleetinfo) > 0) { $rec_fleetinfo = mysql_fetch_assoc($res_fleetinfo); }
                            $sql_fleetships = "SELECT `id`, `ship_id`, `amount`, `primary`, `secondary` FROM `$table[playerfleet_ships]` WHERE `player_id` = '$playerdata[id]' AND `fleet_id` = '$fleet_id'";
                            $rec_fleetships = mysql_query($sql_fleetships);
                            $num_fleetships = mysql_num_rows($rec_fleetships);

                            if ($num_fleetships > 0) {
                            	while ($res_fleetships = mysql_fetch_assoc($rec_fleetships)) {
                            		$formcounter++;
                            		$traveltime = getShipProperty($res_fleetships['ship_id'], 'traveltime');
                            ?>                                                    
                            <tr>
    	                        <td><?echo  getShipProperty($res_fleetships['ship_id'], 'name');?></td>
	                            <td align="center"><? echo $res_fleetships['amount'];?></td>
        	                    <td align="center"><?=$traveltime-10;?>/<?=$traveltime-5;?>/<?=$traveltime;?></td>
            	                <td align="center"><? echo getShipProperty($res_fleetships['primary'], 'name');?></td>
                	            <td align="center"><? echo getShipProperty($res_fleetships['secondary'], 'name');?></td>
                            </tr>
                            <?
                            	}
                            } else {
                            ?>
                            <tr>
                        	    <td width="800" colspan="5" align="center">There are no ships in this fleet.</td>
                            </tr>
                            <?
                            }
                            ?>
                            <tr>
                            	<td width="800" colspan="5">&nbsp;</td>
                            </tr>
							<tr>
								<td width="120" background="img/bg_balk.jpg" colspan="5"><b>Order control</b></td>
							</tr>
							<?
							$formcounter++;
							?>
							<form method="POST" action="main.php?mod=office&act=fleet&do=order&fleet_id=<?=$fleet_id;?>">
							<tr>
								<td width="150">Target:</td>
								<td width="650" colspan="4"><input type="text" size="2" maxlength="2" name="x"> : <input type="text" size="2" maxlength="2" name="y"> : <input type="text" size="2" maxlength="2" name="z"></td>
							</tr>
							<tr>
								<td width="150">Mission:</td>
								<td width="75" colspan="4">
									<select name="action">
										<?
										if ($rec_fleetinfo['action'] == 'home') {
										?>
										<option value="attack">Attack</option>
										<option value="defend">Defend</option>
										<?
										}
										?>
										<option value="home">Retreat</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="150">Time (in ticks):</td>
								<td width="425" colspan="4">
									<?
									if ($rec_fleetinfo['action'] == 'home') {
									?>
									<select name="action_time">
										<option value="5">5</option>
										<option value="4">4</option>
										<option value="3">3</option>
										<option value="2">2</option>
										<option value="1">1</option>
									</select>
									<?
									} else { echo 'n/a'; }
									?>
								</td>
							</tr>
							<tr>
								<td width="800" colspan="5" align="center"><input type="submit" name="giveorder" value="  Give order  "></td>
							</tr>
							</form>
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

<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="180">Ship overview</td>
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
                        		<td width="200" background="img/bg_balk.jpg"><b>Ship name</b></td>
                        		<td width="100" background="img/bg_balk.jpg" align="left"><b>Total amount</b></td>
                        		<td width="100" background="img/bg_balk.jpg" align="left"><b>Amount at base</b></td>
                        		<td width="400" background="img/bg_balk.jpg" align="left"><b>Amount in fleets</b></td>
                        	</tr>
                        	<?
                        	$sql_shipover = "SELECT $table[playerunit].amount, $table[ships].id, $table[ships].name
                        						FROM $table[playerunit]
                        						INNER JOIN $table[ships] ON $table[ships].id = $table[playerunit].unit_id
                        						WHERE $table[playerunit].player_id = $playerdata[id]";
                        	$res_shipover = mysql_query($sql_shipover);
                        	$num_shipover = @mysql_num_rows($res_shipover);
                        	if ($num_shipover > 0) {
                        		while($rec_shipover = mysql_fetch_assoc($res_shipover)) {
                        			if ($rec_shipover['amount'] > 0) {
                        	?>
                        	<tr>
                        		<td><?=$rec_shipover['name'];?></td>
                        		<td><?=parseInteger($rec_shipover['amount']);?></td>
                        		<td><?=parseInteger($rec_shipover['amount'] - getAllFleetships($playerdata['id'], $rec_shipover['id']));?></td>
                        		<td><?=parseInteger(getAllFleetships($playerdata['id'], $rec_shipover['id']));?></td>
                        	</tr>
                        	<?
                        			}
                        		}
                        	}
                        	?>
							</form>
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
if (getFleetAmount($playerdata['id']) < getMaxFleets($playerdata['id'])) {
?>
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
                <td width="180">Create new fleet</td>
                <td width="612" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
                <td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
        </tr>
        <tr>
                <td width="4" background="img/border/L.gif">&nbsp;</td>
                <td width="696" height="100%" valign="top" colspan="2">
                        <table border="0" cellpadding="0" cellspacing="0" width="800">
                                <tr>
                                        <td valign="top">
                                                <form method="POST" action="main.php?mod=office&act=fleet&do=createfleet">
                                                <? $formcounter++; ?>
                                                <table border="0" width="800">
                                                        <tr>
                                                                <td width="800" align="center"><b>&nbsp;</b></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="800" align="center"><input type="submit" name="createfleet" value="  Click here to create a new fleet  "></td>
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