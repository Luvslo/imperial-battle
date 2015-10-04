<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}

if (!$user->checklogin()) {
	include('goto.login.php	');
	die();
}

if (!isset($x)) { $mod = secureData($_GET['x']); }
if (!isset($x)) { $mod = secureData($_POST['x']); }

if (!isset($y)) { $act = secureData($_GET['y']); }
if (!isset($y)) { $act = secureData($_POST['y']); }

if ($_POST['left']) {

	if ($x != 1) {
		if ($y == 1) { $x--; $y = $MAX_CLUSTER; }
		else { $y--; }
	}
	else {
		if ($y != 1) { $y--; }
	}
}
if ($_POST['right']) {
	if ($y == $MAX_CLUSTER) { $x++; $y = 1; }
	else { $y++; }
}

if ((!isset($x)) && (!isset($y))) {
	$player_id = $_SESSION['uid'];
	$sql_getgalaxy = "SELECT `galaxy_id` FROM $table[players] WHERE `id` = '$player_id'";
	$rec_getgalaxy = mysql_fetch_array(mysql_query($sql_getgalaxy));
	$galaxy_id = $rec_getgalaxy[galaxy_id];
	$sql_getxy = "SELECT `x`, `y` FROM $table[galaxy] WHERE `id` = $galaxy_id";
	$rec_getxy = mysql_fetch_array(mysql_query($sql_getxy));
	$x = $rec_getxy[x];
	$y = $rec_getxy[y];
}
$sql_galaxydata = $db->doQuery("SELECT * FROM $table[galaxy] WHERE `x` = '$x' AND `y` = '$y'");
$rec_galaxydata = $db->getFetchArray($sql_galaxydata);
$galaxy_id 		= $rec_galaxydata['id'];

$sql_playerdata = $db->doQuery("SELECT * FROM $table[players] WHERE `galaxy_id` = '$galaxy_id' ORDER BY `galaxy_spot`");
$rec_playerdata = $db->fetchArray($sql_playerdata);

$galaxy_score = 0;
$galaxy_roids = 0;
$sql_totalscore = $db->doQuery("SELECT (`roid_steel` + `roid_crystal` + `roid_erbium` + `roid_unused`) AS `asteroids`, `score` FROM $table[players] WHERE `galaxy_id` = '$galaxy_id'");
while ($rec_totalscore = mysql_fetch_array($sql_totalscore)) {
	$galaxy_score += $rec_totalscore['score'];
	$galaxy_roids += $rec_totalscore['asteroids'];
}
?>

<form method="post" name="galaxyview" action="main.php?mod=galaxy&act=view">
<table border="0" width="800" cellpadding="5">
	<tr>
		<td width="392"><input type="submit" value="<--" name="left" style="float: right"></td>
		<td width="100"><p align="center">
		<input type="text" name="x" size="2" value="<? echo $x; ?>">:<input type="text" name="y" size="2" value="<? echo $y; ?>">
		&nbsp;&nbsp;<input type="submit" value="GO" name="go"></td>
		<td width="392">
		<input type="submit" value="-->" name="right" style="float: left"></td>
	</tr>
</table>
</form>
<table border="0" width="800">
	<tr>
		<td colspan="4" width="800">
		<p align="center"><img src="<? if (!$rec_galaxydata['image_url']) { echo 'img/galaxy_pic.gif'; } else { echo $rec_galaxydata['image_url']; }?>" border="0" alt=""></td>
	</tr>
</table>
<br>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="125">Galaxy information</td>
		<td width="667" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="4" background="img/border/L.gif">&nbsp;</td>
		<td width="696" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="760">
				<tr>
					<td valign="top">
						<table border="0" width="800">
							<tr>
								<td colspan="7">
								<p align="center"><? echo stripslashes($rec_galaxydata['topic']); ?></td>
							</tr>
							<tr>
								<td colspan="7"><p align="center"><b>Total score:</b> <?echo parseInteger($galaxy_score);?> - <b>Total asteroids:</b> <?echo parseInteger($galaxy_roids);?></td>
							</tr>
							<tr>
								<td colspan="7"><br></td>
							</tr>
							<?
							if (mysql_num_rows($sql_playerdata) > 0) {
							?>
							<tr>
								<td width="75" align="center" background="img/bg_balk.jpg"><b>Location</b></td>
								<td width="100" align="center" background="img/bg_balk.jpg"><b>Alliance</b></td>
								<td width="360" align="center" background="img/bg_balk.jpg" colspan="3"><b>Ruler- & planetname</b></td>
								<td width="150" align="right" background="img/bg_balk.jpg"><b>Score</b></td>
								<td width="75" align="right" background="img/bg_balk.jpg"><b>Asteroids</b></td>
							</tr>
							<?
							while ($rec_playerdata = mysql_fetch_array($sql_playerdata)) {
								$z = $rec_playerdata[galaxy_spot];
								$totalroids = $rec_playerdata[roid_steel] + $rec_playerdata[roid_crystal] + $rec_playerdata[roid_erbium] + $rec_playerdata[roid_unused];

								if ($galaxy_id == $playerdata['galaxy_id']) {
									$currenttime = time();
									$logintime = $rec_playerdata['lastlogin'];
									$time_diff = $currenttime - $logintime;
									if ($time_diff > 300) { $status = null; }
									else { $status = '*'; }
								} else {
									$status = null;
								}

								$name_color = "#FFFFFF";
								if ($rec_playerdata['id'] == $rec_galaxydata['moc_id']) { $name_color = "#FF00FF"; }
								if ($rec_playerdata['id'] == $rec_galaxydata['mow_id']) { $name_color = "#FF0000"; }
								if ($rec_playerdata['id'] == $rec_galaxydata['moe_id']) { $name_color = "#FFFF00"; }
								if ($rec_playerdata['id'] == $rec_galaxydata['commander_id']) { $name_color = "#0000FF"; }
							?>
							<tr>
								<td align="center"><? echo $x; ?>:<? echo $y; ?>:<? echo $z; ?></td>
								<td align="center"><? echo getAllianceTag($rec_playerdata['id']);?></td>
								<td align="right" width="177"><a href="main.php?mod=main&act=mail&do=compose&x=<?=$x;?>&y=<?=$y;?>&z=<?=$z;?>"><font color="<?echo $name_color;?>"><? echo stripslashes($rec_playerdata[rulername]); ?></font></a></td>
								<td align="center" width="6"><a href="main.php?mod=main&act=mail&do=compose&x=<?=$x;?>&y=<?=$y;?>&z=<?=$z;?>"><font color="<?echo $name_color;?>">of</font></a></td>
								<td align="left" width="177"><a href="main.php?mod=main&act=mail&do=compose&x=<?=$x;?>&y=<?=$y;?>&z=<?=$z;?>"><font color="<?echo $name_color;?>"><? echo stripslashes($rec_playerdata[planetname]); ?></font></a><?if ($status) {?><font color="#FFFFFF"><?echo $status;?></font><? } ?></td>
								<td align="right"><? echo parseInteger($rec_playerdata[score]); ?></td>
								<td align="right"><? echo parseInteger($totalroids);?></td>
							</tr>
							<?
							}
							}
							else {
							?>
							<tr><td colspan="7" align="center">This galaxy is empty, so go away!</td></tr>
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
<? if (mysql_num_rows($sql_playerdata) > 0) { ?>
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="125">Explanation</td>
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
								<td width="60" align="center" background="img/bg_balk.jpg"><b>Color</b></td>
								<td width="175" align="left" background="img/bg_balk.jpg"><b>Function name</b></td>
								<td width="525" align="left" background="img/bg_balk.jpg"><b>Description</b></td>
							</tr>
							<tr>
								<td align="center" valign="top"><font color="#0000FF">Blue</font></td>
								<td valign="top">Galaxy Commander</td>
								<td valign="top">This player is responsible for the whole galaxy. The galaxy commander gets elected by all users in the galaxy. The commander can choose his ministers.</td>
							</tr>
							<tr>
								<td align="center" valign="top"><font color="#FF0000">Red</font></td>
								<td valign="top">Minister of war</td>
								<td valign="top">This player is responsible for galactic wars.</td>
							</tr>
							<tr>
								<td align="center" valign="top"><font color="#FF00FF">Pink</font></td>
								<td valign="top">Minister of communication</td>
								<td valign="top">This player is responsible for galactic communication.</td>
							</tr>
							<tr>
								<td align="center" valign="top"><font color="#FFFF00">Yellow</font></td>
								<td valign="top">Minister of economics</td>
								<td valign="top">This player is responsible for the economic part of the galaxy.</td>
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
<? } ?>