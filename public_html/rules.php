<?
if ($act == 'doaccept') {
	if (!empty($_POST['accept'])) {
		$sql_acceptrules = "UPDATE $table[players] SET `rules_accepted` = '1' WHERE `id` = '$playerdata[id]'";
		mysql_query($sql_acceptrules);
		$error = 1;
		$playerdata = getPlayerdata($playerdata['id']);
	} else {
		$error = 100;
	}
}
switch($error) {
	case 1:
	$msg = "Thanks for accepting the rules.<br />Have fun with playing Imperial-Battle!";
	break;
	case 100:
	$msg = "There was an error processing your request. Contact the game administrators.";
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
?>

<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="110">Rules</td>
		<td width="692" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
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
							$sql_rulescat = "SELECT `id`, `name` FROM $table[rulescat] WHERE `online` = '1' ORDER BY `order`";
							$res_rulescat = mysql_query($sql_rulescat) or die(mysql_error());
							$num_rulescat = @mysql_num_rows($res_rulescat);
							if ($num_rulescat > 0) {
								while ($rec_rulescat = mysql_fetch_array($res_rulescat)) {
							?>
							<tr>
								<td width="800" background="img/bg_balk.jpg" colspan="2"><b><?=$rec_rulescat['name'];?></b></td>
							</tr>
							<?
									$sql_rules = "SELECT `id`, `name`, `description` FROM $table[rules] WHERE `cat_id` = '$rec_rulescat[id]' AND `online` = '1' ORDER BY `order`";
									$res_rules = mysql_query($sql_rules);
									$num_rules = @mysql_num_rows($res_rules);
									if ($num_rules > 0) {
										while ($rec_rules = mysql_fetch_assoc($res_rules)) {
									?>
							<tr>
								<td width="15" valign="top"><li></td>
								<td width="785" valign="top"><?=$rec_rules['description'];?></td>
							</tr>									
									<?
										}
									} else {
									?>
							<tr>
								<td width="800" align="center" colspan="2">There are no rules for this category.</td>
							</tr>
									
									<?
									}
								}
							} else {
							?>
							<tr>
								<td width="800" align="center" colspan="2">There are no rule categories found.</td>
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
if ($playerdata['rules_accepted'] == 0) {
?>
<br /><br />
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="200">Acceptation of the rules</td>
		<td width="602" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
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
								<td width="800" valign="top">
									You have not yet accepted the rules.<br />
									If you want to play Imperial-Battle, accept the rules listed on this page, by clicking on the button below.<br />
									This is only a one-time action.
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
							<form method="POST" action="main.php?mod=rules&act=doaccept">
							<tr>
								<td align="center"><input type="submit" name="accept" value="I have read the rules and accept them"></td>
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