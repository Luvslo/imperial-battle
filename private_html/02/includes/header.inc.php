<?
$news_image = 'img/icons/news_off.jpg';
$mail_image = 'img/icons/mail_off.jpg';
$allie_image = 'img/icons/allies_off.jpg';
$enemy_image = 'img/icons/enemys_off.jpg';

$sql_checkmail = "SELECT `id` FROM $table[mail] WHERE `read` = '0' AND `to_player` = '$playerdata[id]'";
$res_checkmail = mysql_query($sql_checkmail);
$new_mail = @mysql_num_rows($res_checkmail);
if ($new_mail > 0) { $mail_image = 'img/icons/mail_on.jpg'; }

$sql_checknews = "SELECT `id` FROM $table[playernews] WHERE `read` = '0' AND `player_id` = '$playerdata[id]'";
$res_checknews = mysql_query($sql_checknews);
$new_news = @mysql_num_rows($res_checknews);
if ($new_news > 0) { $news_image = 'img/icons/news_on.jpg'; }

$sql_checkenemy = "SELECT `id` FROM $table[playerfleet] WHERE `target_id` = '$playerdata[id]' AND `action` = 'attack'";
$res_checkenemy = mysql_query($sql_checkenemy);
$num_checkenemy = @mysql_num_rows($res_checkenemy);
if ($num_checkenemy > 0) { $enemy_image = 'img/icons/enemys_on.jpg'; }

$sql_checkally = "SELECT `id` FROM $table[playerfleet] WHERE `target_id` = '$playerdata[id]' AND `action` = 'defend'";
$res_checkally = mysql_query($sql_checkally);
$num_checkally = @mysql_num_rows($res_checkally);
if ($num_checkally > 0) { $allie_image = 'img/icons/allies_on.jpg'; }

?>

<html>
<head>
<meta http-equiv="Content-Language" content="nl">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Imperial-Battle | Main window</title>
<link href="css/default.css" rel="stylesheet" type="text/css">
<base target="_self">
<script language="javascript">
function updateResources(steel, crystal, erbium, titanium) {
	document.getElementById('res_steel').innerHTML = steel
	document.getElementById('res_crystal').innerHTML = crystal
	document.getElementById('res_erbium').innerHTML = erbium
	document.getElementById('res_titanium').innerHTML = titanium
}
function reloadMenu() {
	top.menu.location.replace('menu.php');
}

</script>
</head>

<body bgcolor="#223137" background="img/main_bg.gif" style="background-repeat: no-repeat">
<center>
<table border="0" cellpadding="0" cellspacing="0" width="800" height="19">
	<tr>
		<td background="img/bg_balk.jpg" style="border: 1px solid #3C5762">
		<table border="0" cellpadding="0" cellspacing="0" width="800">
			<tr>
				<td width="160"><b>&nbsp;Steel: <a id="res_steel"><?echo parseInteger($playerdata['res_steel']);?></a></b></td>
				<td width="160"><b>&nbsp;Crystal: <a id="res_crystal"><?echo parseInteger($playerdata['res_crystal']);?></a></b></td>
				<td width="160"><b>&nbsp;Erbium: <a id="res_erbium"><?echo parseInteger($playerdata['res_erbium']);?></a></b></td>
				<td width="160"><b>&nbsp;Titanium: <a id="res_titanium"><?echo parseInteger($playerdata['res_titanium']);?></a></b></td>
				<td width="160"><p align="right"><b>Score: <?echo parseInteger($playerdata['score']);?>&nbsp;&nbsp;</b></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="800" height="19">
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>
		<table border="0" cellpadding="0" cellspacing="0" width="800">
			<tr>
				<td width="4" valign="bottom">
				<img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
				<td width="96" valign="top"><p align="center"><b>Information</b></td>
				<td width="696" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
				<td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
			</tr>
			<tr>
				<td width="4" background="img/border/L.gif">&nbsp;</td>
				<td width="696" height="100%" colspan="2" valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="760">
						<tr>
							<td valign="top">
								<table border="0" width="456">
									<tr>
										<td width="75">Game time:</td>
										<td><? echo date("H:i", time()); ?></td>
									</tr>
									<tr>
										<td width="75">Tick:</td>
										<td>
										<?
										echo parseInteger(IB_TICK_CURRENT).'/'.parseInteger(IB_TICK_LAST);?> 
										<?
										$s = getNextTickTime();
										if ($s > 0) { echo '(next tick in '.getDuration($s).')'; }
										elseif ((IB_TICK_CURRENT == 0) OR (IB_TICK_CURRENT == IB_TICK_LAST)) { echo '(<font color="red">OFFLINE</font>)'; } 
										else { echo '(Processing...)'; }?>
										</td>
									</tr>
									<tr>
										<td width="75">Rank:</td>
										<td><?=getPlayerRank($playerdata['id']);?></td> 
									</tr>									
								</table>
							</td>
							<td width="304" valign="top">
								<table border="0" cellpadding="0" cellspacing="0" width="301">
									<tr>
										<td width="76" align="center"><a href="main.php?mod=main&act=news"><img border="0" src="<?echo $news_image;?>" width="76" height="62"></a></td>
										<td width="76" align="center"><a href="main.php?mod=main&act=mail"><img border="0" src="<?echo $mail_image;?>" width="75" height="62"></a></td>
										<td width="76" align="center"><img border="0" src="<?echo $allie_image;?>" width="75" height="62"></td>
										<td width="76" align="center"><img border="0" src="<?echo $enemy_image;?>" width="75" height="62"></td>
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
		</td>
	</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="80%" height="30" valign="top" colspan="2">
		<b><font size="4">&nbsp;<? echo $page; ?></font></b></td>
	</tr>
</table>