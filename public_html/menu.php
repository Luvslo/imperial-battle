<?
require_once('global.inc.php');

if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}

$player_id = $_SESSION['uid'];
$playerdata = getPlayerdata($player_id);
updatePlayerLoginData($player_id, $_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_HOST']);
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Imperial-Battle | Navigation</title>
<link href="css/default.css" rel="stylesheet" type="text/css">
<base target="hoofd">
</head>

<body bgcolor="#223137" background="img/menu_bg.gif" style="background-repeat: no-repeat">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" height="19">
			<tr>
				<td background="img/bg_balk.jpg" style="border: 1px solid #3C5762">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td align="center">&nbsp;<b><?echo stripslashes($playerdata['rulername']);?></b> of <b><?echo stripslashes($playerdata['planetname']);?></b></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<br>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td align="center" valign="top">
					<script type="text/javascript"><!--
						google_ad_client = "pub-3535748112978011";
						google_ad_width = 125;
						google_ad_height = 125;
						google_ad_format = "125x125_as";
						google_ad_type = "text_image";
						//2006-10-12: IB menu
						google_ad_channel ="0308747151";
						google_color_border = "3C5762";
						google_color_bg = "223137";
						google_color_link = "F2984C";
						google_color_text = "B3B3B3";
						google_color_url = "C3D9FF";
					//--></script>
						<script type="text/javascript"
  							src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
						</script>
				</td>
			</tr>
		</table>		
		<br>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
				<td width="75%">Round 6</td>
				<td width="20%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
				<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
			</tr>
			<tr>
				<td width="2%" background="img/border/L.gif">&nbsp;</td>
				<td width="95%" height="100%" valign="top" colspan="2">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td valign="top">
								<li><a href="http://forum.imperial-battle.com/" target="_blank">Forum</a><br>
								<li><a href="irc://irc.imperial-battle.com/" target="_blank">IRC</a><br><br>
								<li><a href="main.php?mod=rules" target="main">Rules</a><br>
								<li><a href="http://www.imperial-battle.com/wiki/" target="_blank">Documentation</a><br>
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
				<td width="30%">Main</td>
				<td width="65%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
				<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
			</tr>
			<tr>
				<td width="2%" background="img/border/L.gif">&nbsp;</td>
				<td width="95%" height="100%" valign="top" colspan="2">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td valign="top">
								<li><a href="main.php?mod=main&act=overview" target="main">Overview</a><br>
								<li><a href="main.php?mod=main&act=universe" target="main">Universe</a><br>
								<br>
								<li><a href="main.php?mod=main&act=mail" target="main">Mail</a><br>
								<li><a href="main.php?mod=main&act=news" target="main">News</a>
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
		if ($playerdata['alliance_id'] > 0) {
		?>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
				<td width="30%">Alliance</td>
				<td width="65%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
				<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
			</tr>
			<tr>
				<td width="2%" background="img/border/L.gif">&nbsp;</td>
				<td width="95%" height="100%" valign="top" colspan="2">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td valign="top">
								<li><a href="main.php?mod=alliance&act=hq" target="main">Headquarters</a><br>
								<li><a href="main.php?mod=alliance&act=status" target="main">Status</a><br>
								<li><a href="main.php?mod=alliance&act=memberlist" target="main">Memberlist</a><br>
								<li><a href="main.php?mod=alliance&act=forum" target="main">Forum</a><br>
								<br>
								<li><a href="main.php?mod=alliance&act=admin" target="main">Administration</a>
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
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
				<td width="30%">Galaxy</td>
				<td width="65%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
				<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
			</tr>
			<tr>
				<td width="2%" background="img/border/L.gif">&nbsp;</td>
				<td width="95%" height="100%" valign="top" colspan="2">
					<li><a href="main.php?mod=galaxy&act=view" target="main">View</a><br>
					<li><a href="main.php?mod=galaxy&act=status" target="main">Status</a><br>
					<li><a href="main.php?mod=galaxy&act=forum" target="main">Forum</a><br>
					<li><a href="main.php?mod=galaxy&act=politics" target="main">Politics</a>
				</td>
				<td width="3%" background="img/border/R.gif">&nbsp;</td>
			</tr>
			<tr>
				<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
				<td width="95%" background="img/border/O.gif" colspan="2">&nbsp;</td>
				<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
			</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
				<td width="40%">Productions</td>
				<td width="55%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
				<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
			</tr>
			<tr>
				<td width="2%" background="img/border/L.gif">&nbsp;</td>
				<td width="95%" height="100%" valign="top" colspan="2">
					<li><a href="main.php?mod=production&act=item&type=1" target="main">Constructions</a><br>
					<li><a href="main.php?mod=production&act=item&type=2" target="main">Researches</a><br>
					<li><a href="main.php?mod=production&act=factory" target="main">Factory</a>
				</td>
				<td width="3%" background="img/border/R.gif">&nbsp;</td>
			</tr>
			<tr>
				<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
				<td width="95%" background="img/border/O.gif" colspan="2">&nbsp;</td>
				<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
			</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
				<td width="30%">Office</td>
				<td width="65%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
				<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
			</tr>
			<tr>
				<td width="2%" background="img/border/L.gif">&nbsp;</td>
				<td width="95%" height="100%" valign="top" colspan="2">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td valign="top">
								<li><a href="main.php?mod=office&act=resources" target="main">Resources</a><br>
								<li><a href="main.php?mod=office&act=fleet" target="main">Fleet control</a><br>
								<li><a href="main.php?mod=office&act=intelligence" target="main">Intelligence</a>
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
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
				<td width="30%">Other</td>
				<td width="65%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
				<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
			</tr>
			<tr>
				<td width="2%" background="img/border/L.gif">&nbsp;</td>
				<td width="95%" height="100%" valign="top" colspan="2">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td valign="top">
								<li><a href="main.php?mod=other&act=help" target="main">Help</a><br>
								<li><a href="main.php?mod=other&act=myaccount" target="main">My account</a><br>
								<li><a href="main.php?mod=logout" target="main">Logout</a>
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
</body>

</html>
