<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}

if (!isset($do)) { $act = secureData($_GET['do']); }
if (!isset($do)) { $act = secureData($_POST['do']); }
$error = -1;
if ($do == 'changepasswd') { 
	$oldpassword = secureData($_POST['oldpassword']);
	$password = secureData($_POST['password']);
	$password2 = secureData($_POST['password2']);
	
	if ($password == $password2) { 
		$md5_oldpass = md5($oldpassword);
		$sql_checkpass = "SELECT `id`, `password` FROM $table[players] WHERE `id` = '$playerdata[id]' AND `password` = '$md5_oldpass'";
		$res_checkpass = mysql_query($sql_checkpass);
		$rec_checkpass = mysql_fetch_array($res_checkpass);
		$num_checkpass = mysql_num_rows($res_checkpass);
		
		if ($num_checkpass > 0) {
			$new_md5pass = md5($password);
			$sql_updpass = "UPDATE $table[players] SET `password` = '$new_md5pass' WHERE `id` = '$playerdata[id]'";
			mysql_query($sql_updpass) or die(mysql_error());
			$error = 0;
		} else {
			$error = 100;
		}
	} else {
		$error = 101;
	}
}

if ($do == 'changegalaxy') { 
	$password = secureData($_POST['password']);
	$sql_checkgal = "SELECT `id`, `x`, `y` FROM $table[galaxy] WHERE `private` = '1' AND `password` = '$password'";
	$res_checkgal = mysql_query($sql_checkgal);
	$num_checkgal = mysql_num_rows($res_checkgal);
	$rec_checkgal = mysql_fetch_array($res_checkgal);
	
	if ($num_checkgal > 0) {
		if (getFreeGalaxySpot($rec_checkgal['id'])) {
			$galaxy_spot = getFreeGalaxySpot($rec_checkgal['id']);
			if ($playerdata['id'] == getGalaxyCommander($playerdata['galaxy_id'])) {
				$sql_updgalcom = "UPDATE $table[galaxy] SET `commander_id` = '0', `moc_id` = '0', `mow_id` = '0', `moe_id` = '0' WHERE `id` = '$playerdata[galaxy_id]'";
			}
			if ($field = isPlayerMinister($playerdata['id'])) {
				$sql_updgalcom = "UPDATE $table[galaxy] SET `$field` = '0' WHERE `id` = '$playerdata[galaxy_id]'";
			}
			$playerdata['galaxy_id'] = $rec_checkgal['id'];
			$playerdata['galaxy_spot'] = $galaxy_spot;
			$playerdata['res_steel'] -= ($playerdata['res_steel'] * 0.3);
			$playerdata['res_crystal'] -= ($playerdata['res_crystal'] * 0.3);
			$playerdata['res_erbium'] -= ($playerdata['res_erbium'] * 0.3);
			$playerdata['res_titanium'] -= ($playerdata['res_titanium'] * 0.3);
			
			$sql_remvotes = "DELETE FROM $table[politics] WHERE `player_id` = '$playerdata[id]' OR `voted_on` = '$playerdata[id]'";
			mysql_query($sql_remvotes) or die(mysql_error());
			
			updatePlayerData($playerdata['id'], $playerdata);
			//$sql_updgal = "UPDATE $table[players] SET `galaxy_id` = '$rec_checkgal[id]', `galaxy_spot` = '$galaxy_spot' WHERE `id` = $playerdata[id]";
			//mysql_query($sql_updgal) or die(mysql_error());
			
			if (isset($sql_updgalcom)) { mysql_query($sql_updgalcom) or die(mysql_error()); }
			$error = 1;
		} else {
			$error = 102;
		}
	} else {
		$error = 103;
	}
}
if ($do == 'createalliance') { 
	$name = secureData($_POST['name']);
	$tag = secureData($_POST['tag']);
	$time = time();
	$length = rand(10, 27);
	$password = substr(md5($time.$name.$tag.'dfug984758945hgjkdfhg48975348975dkhgdkjhg3984t5394875dkjfhgdkjfhg'), 5, $length);
	
	$sql_checkalliance = "SELECT `id` FROM $table[alliance] WHERE `name` = '$name' OR `tag` = '$tag'";
	$res_checkalliance = mysql_query($sql_checkalliance);
	$num_checkalliance = mysql_num_rows($res_checkalliance);
	if (!$name || !$tag) { $error = 107; }
	elseif ($num_checkalliance == 0) {
		$sql_createalliance = "INSERT INTO $table[alliance] (`name`, `tag`, `password`, `founder_id`, `startdate`, `message`, `message_lastedit`) 
								VALUES ('$name', '$tag', '$password', '$playerdata[id]', '$time', 'Welcome to the $name alliance.', '$time')";
		mysql_query($sql_createalliance) or die(mysql_error());
		$alliance_id = mysql_insert_id();
		$playerdata['alliance_id'] = $alliance_id;
		updatePlayerData($playerdata['id'], $playerdata);
		$error = 2;
	} else {
		$error = 104;
	}
}
if ($do == 'joinalliance') { 
	$password = secureData($_POST['password']);
	$sql_checkalliance = "SELECT `id` FROM $table[alliance] WHERE `password` = '$password'";
	$res_checkalliance = mysql_query($sql_checkalliance);
	$num_checkalliance = mysql_num_rows($res_checkalliance);
	$rec_checkalliance = mysql_fetch_array($res_checkalliance);
	if ($num_checkalliance > 0) {
		if ($rec_checkalliance['id'] != $playerdata['alliance_id']) {
			$playerdata['alliance_id'] = $rec_checkalliance['id'];
			updatePlayerData($playerdata['id'], $playerdata);
			$error = 3;
		} else {
			$error = 105;
		}
	} else {
		$error = 106;
	}
}
if ($do == 'changerulerplanet') {
	$rulername = secureData($_POST['rulername']);
	$planetname = secureData($_POST['planetname']);
	if (!$rulername || !$planetname) { $error = 108; }
	if (getCurrentTick() > 1500) { $error = 109; }
	if ((getIdByRulername($rulername)) && ($rulername != $playerdata['rulername'])) { $error = 110; }
	if ((getIdByPlanetname($planetname)) && ($planetname != $playerdata['planetname'])) { $error = 111; }
	if ($error < 100) {
		$sql_updrp = "UPDATE $table[players] SET `rulername` = '$rulername', `planetname` = '$planetname' WHERE `id` = '$playerdata[id]'";
		mysql_query($sql_updrp) or die(mysql_error());
		$error = 4;
	}
}
$playerdata = getPlayerdata($playerdata['id']);
switch($error) {
	case 0:
		$msg = "Succesfully changed password";
		break;
	case 1:
		$msg = "Succesfully changed galaxy and took 30% of your resources.";
		break;
	case 2:
		$msg = "Succesfully created new alliance. You can find the alliance password on the administration page.<br>If you were a member of any other alliance, the membership has been dropped.";
		break;
	case 3:
		$msg = "Succesfully joined the specified alliance.<br>If you were a member of any other alliance, the membership has been dropped.";
		break;
	case 4:
		$msg = 'Succesfully updated ruler- & planetname.';
		break;
	case 100:
		$msg = "Your old password doesn`t match. Please try again.";
		break;
	case 101:
		$msg = "The new passwords didn`t matched. Please try again";
		break;
	case 102:
		$msg = "The galaxy password you specified is correct, but there are no free spots in the galaxy.";
		break;
	case 103:
		$msg = "The galaxy password you specified is incorrect. Please try again.";
		break;
	case 104:
		$msg = "Either the alliance name or alliance tag is already in use. Please choose another one.";
		break;
	case 105:
		$msg = "You are already a member of this alliance.";
		break;
	case 106:
		$msg = "The alliance password you entered is incorrect.";
		break;
	case 107:
		$msg = "You need to fill in both alliance name and alliance tag.";
		break;
	case 108:
		$msg = 'You need to fill in both ruler- & planetname.';
		break;
	case 109:
		$msg = 'You can only change ruler- & planetname when the ticker is below 1500 ticks.';
		break;
	case 110:
		$msg = 'That rulername is already taken.';
		break;
	case 111:
		$msg = 'That planetname is already taken.';
		break;
}
if ($error > 99) { $error_color = 'red'; }
else { $error_color = 'green'; }

if ($msg) {
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
<script language="javascript">reloadMenu();</script>
<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
                <td width="180">Static preferences</td>
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
                                                                <td width="800" background="img/bg_balk.jpg" colspan="2"><b>Account details & settings</b></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="150">Username:</td>
                                                                <td width="650"><? echo $playerdata['username']; ?></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="150">Rulername:</td>
                                                                <td width="650"><? echo $playerdata['rulername']; ?></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="150">Planetname:</td>
                                                                <td width="650"><? echo $playerdata['planetname'];?></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="150">Universe location:</td>
                                                                <td width="650">
                                                                <?
                                                                $xyz = getXYZ($playerdata['id']);
                                                                echo $xyz[0].':'.$xyz[1].':'.$xyz[2];
                                                                ?>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <td width="150">Alliance name & tag:</td>
                                                                <td width="650"><?echo getAllianceName($playerdata['id']).' - '.getAllianceTag($playerdata['id']);?></td>
                                                        </tr>
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
                <td width="180">Variable preferences</td>
                <td width="612" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
                <td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
        </tr>
        <tr>
                <td width="4" background="img/border/L.gif">&nbsp;</td>
                <td width="696" height="100%" valign="top" colspan="2">
                        <table border="0" cellpadding="0" cellspacing="0" width="800">
                                <tr>
                                        <td valign="top">
                                        		<form method="POST" action="main.php?mod=other&act=myaccount&do=changerulerplanet">
                                                <table border="0" width="800">
                                                        <tr>
                                                                <td width="800" background="img/bg_balk.jpg" colspan="2"><b>Change ruler & planetname</b></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="150">Rulername:</td>
                                                                <td width="650"><input type="text" name="rulername" size="40" value="<?=$playerdata['rulername'];?>"></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="150">Planetname:</td>
                                                                <td width="650"><input type="text" name="planetname" size="40" value="<?=$playerdata['planetname'];?>"></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="800" colspan="2" align="center"><input type="submit" name="changepasswd" value="  Change names  "></td>
                                                        </tr>
                                                </table>
                                                </form>
                                        		<form method="POST" action="main.php?mod=other&act=myaccount&do=changepasswd">
                                                <table border="0" width="800">
                                                        <tr>
                                                                <td width="800" background="img/bg_balk.jpg" colspan="2"><b>Change password</b></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="150">Old password:</td>
                                                                <td width="650"><input type="password" name="oldpassword" size="40"></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="150">Password:</td>
                                                                <td width="650"><input type="password" name="password" size="40"></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="150">Confirm password:</td>
                                                                <td width="650"><input type="password" name="password2" size="40"></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="800" colspan="2" align="center"><input type="submit" name="changepasswd" value="  Change password  "></td>
                                                        </tr>
                                                </table>
                                                </form>
                                                <form method="POST" action="main.php?mod=other&act=myaccount&do=changegalaxy">
                                                <table border="0" width="800">
                                                        <tr>
                                                                <td width="800" background="img/bg_balk.jpg" colspan="2"><b>Change galaxy</b></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="150">Galaxy password:</td>
                                                                <td width="650"><input type="text" name="password" size="40"></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="800" colspan="2" align="center"><input type="submit" name="changegalaxy" value="  Change galaxy  "></td>
                                                        </tr>
                                                </table>
                                                </form>
                                                <form method="POST" action="main.php?mod=other&act=myaccount&do=createalliance">
                                                <table border="0" width="800">
                                                        <tr>
                                                                <td width="800" background="img/bg_balk.jpg" colspan="2"><b>Create alliance</b></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="150">Alliance name:</td>
                                                                <td width="650"><input type="text" name="name" size="40"></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="150">Alliance tag:</td>
                                                                <td width="650"><input type="text" name="tag" size="40" maxlength="12"></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="800" colspan="2" align="center"><input type="submit" name="createalliance" value="  Create alliance  "></td>
                                                        </tr>
                                                </table>
                                                </form>
                                                <form method="POST" action="main.php?mod=other&act=myaccount&do=joinalliance">
                                                <table border="0" width="800">
                                                        <tr>
                                                                <td width="800" background="img/bg_balk.jpg" colspan="2"><b>Join alliance</b></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="150">Alliance password:</td>
                                                                <td width="650"><input type="text" name="password" size="40"></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="800" colspan="2" align="center"><input type="submit" name="joinalliance" value="  Join alliance  "></td>
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