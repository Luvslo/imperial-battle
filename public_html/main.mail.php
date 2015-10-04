<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}
if (!isset($do)) { $act = secureData(strip_tags($_GET['do'])); }
if (!isset($do)) { $act = secureData(strip_tags($_POST['do'])); }

if ($do == 'compose') {
	$x = secureData($_GET['x']);
	$y = secureData($_GET['y']);
	$z = secureData($_GET['z']);
	if ($_POST['resubject']) {
		$resub = secureData($_POST['resubject']);
		if (substr($resub, 0, 4) == 'Re: ') { $subject = $resub; }
		else{ $subject = 'Re: '.$resub; }
	}
}
if ($do == 'send') {
	$x = secureData($_POST['x']);
	$y = secureData($_POST['y']);
	$z = secureData($_POST['z']);

	$subject = secureData($_POST['subject']);
	$text = secureData($_POST['text']);
	$error = 0;

	$player_to_id = getPlayerId($x, $y, $z);
	if ($player_to_id < 1) { $error = 1; }
	if ($subject == '') { $error = 2; }
	if ($error == 0) {
		$sql_newmail = "INSERT INTO $table[mail] (`id`, `from_player`, `to_player`, `subject`, `text`, `date`)
						VALUES ('', '$playerdata[id]', '$player_to_id', '$subject', '$text', UNIX_TIMESTAMP())";
		mysql_query($sql_newmail);
		unset($x, $y, $z, $subject, $text);
	}
	switch($error) {
		case 0:
		$msg = "The mail was succesfully sent.";
		break;
		case 1:
		$msg = "That player does not exist!";
		break;
		case 2:
		$msg = "The subject is a required field.";
		break;
	}
}
if ($do == 'delete') {
	$mail_id = secureData($_GET['mail_id']);
	$sql_delmail = "DELETE FROM $table[mail] WHERE `id` = '$mail_id' AND `to_player` = '$playerdata[id]'";
	mysql_query($sql_delmail);
	$msg = "Mail succesfully removed.";

}
if ($do == 'deleteall') {
	$sql_delallmail = "DELETE FROM $table[mail] WHERE `to_player` = '$playerdata[id]'";
	mysql_query($sql_delallmail) or die(mysql_error());
}
if ($do == 'read') {
	$mail_id = secureData($_GET['mail_id']);

?>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="200">Communication - Reading mail</td>
		<td width="592" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
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
							$sql_readmail = "SELECT * FROM $table[mail] WHERE `id` = '$mail_id' AND `to_player` = '$playerdata[id]'";
							$res_readmail = mysql_query($sql_readmail);
							$num_readmail = @mysql_num_rows($res_readmail);
							if ($num_readmail > 0) {
								$rec_readmail = mysql_fetch_array($res_readmail);
								$c = getXYZ($rec_readmail['from_player']);
							?>
							<tr>
								<td width="150">Recieved from:</td>
								<td width="550"><?=getRulernameById($rec_readmail['from_player']);?> of <?=getPlanetnameById($rec_readmail['from_player']);?>&nbsp;(<?=$c[0];?>:<?=$c[1];?>:<?=$c[2];?>)</td>
								<form method="POST" action="main.php?mod=main&act=mail&do=compose&x=<?=$c[0];?>&y=<?=$c[1];?>&z=<?=$c[2];?>"><input type="hidden" name="resubject" value="<?echo $rec_readmail['subject'];?>"><td width="50"><input type="submit" name="reply" value="  Reply  "></td></form>
								<form method="POST" action="main.php?mod=main&act=mail&do=delete&mail_id=<?echo $rec_readmail['id'];?>"><td width="50"><input type="submit" name="deletemail" value="  Delete  "></td></form>
							</tr>
							<tr>
								<td width="150">Date recieved:</td>
								<td width="650" colspan="3"><?echo date('H:i d-m-Y', $rec_readmail['date']);?></td>
							</tr>
							<tr>
								<td width="150">Subject:</td>
								<td width="650" colspan="3"><?echo $rec_readmail['subject'];?></td>
							</tr>
							<tr>
								<td height="15" colspan="5">&nbsp;</td>
							</tr>
							<tr>
								<td width="150">Message content:</td>
								<td width="650" colspan="3"><?echo nl2br(parseBBcode(stripslashes($rec_readmail['text']), 1, 1, 1, 1));?></td>
							</tr>
							<?
							}
							else {
							?>
							<tr>
								<td width="800" align="center">There is no such mail.</td>
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
<?
$sql_unreadmail = "UPDATE $table[mail] SET `read` = '1' WHERE `id` = '$mail_id'";
mysql_query($sql_unreadmail);
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
		<td width="210">Communication - Compose new mail</td>
		<td width="582" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="4" background="img/border/L.gif">&nbsp;</td>
		<td width="696" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="800">
				<tr>
					<td valign="top">
					<form method="POST" action="main.php?mod=main&act=mail&do=send" name="newmail">
						<table border="0" width="800">
							<tr>
								<td width="130">Send to:</td>
								<td width="670"><input type="text" name="x" size="2" maxlength="2" value="<?=$x;?>">&nbsp;<input type="text" name="y" size="2" maxlength="2" value="<?=$y;?>">&nbsp;<input type="text" name="z" size="2" maxlength="2" value="<?=$z;?>">&nbsp;(X:Y:Z)</td>
							</tr>
							<tr>
								<td width="130">Subject:</td>
								<td width="670"><input type="text" name="subject" size="73" value="<?=$subject;?>"></td>
							</tr>
							<tr>
								<td width="130" valign="top">Content:</td>
								<td width="670">
									<input type="button" name="bb_b" value="[b]" onclick="document.forms['newmail'].text.value=document.forms['newmail'].text.value+'[b]'+prompt('Text which should be bold:', '')+'[/b]'">
									<input type="button" name="bb_i" value="[i]" onclick="document.forms['newmail'].text.value=document.forms['newmail'].text.value+'[i]'+prompt('Text which should be italic:', '')+'[/i]'">
									<input type="button" name="bb_u" value="[u]" onclick="document.forms['newmail'].text.value=document.forms['newmail'].text.value+'[u]'+prompt('Text which should be underlined:', '')+'[/u]'">
									<input type="button" name="bb_url" value="[url]" onclick="document.forms['newmail'].text.value=document.forms['newmail'].text.value+'[url]'+prompt('URL you want to link:', '')+'[/url]'">
									<br/>
									<textarea rows="12" cols="70" name="text"><?=$text;?></textarea>
								</td>
							</tr>
							<tr height="15">
								<td width="800" colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td width="800" colspan="2" align="center"><input type="submit" name="sendmail" value="  Send mail  "></td>
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
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="180">Communication - INBOX</td>
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
								<td colspan="4" align="center"><form method="POST" action="main.php?mod=main&act=mail&do=deleteall"><input type="submit" name="deleteallmail" value="  Delete all mail " onclick="return confirm('Are you sure you want to delete all your mails?');"></form></td>
							</tr>
							<tr>
								<td width="150" background="img/bg_balk.jpg"><b>Recieved from</b></td>
								<td width="350" background="img/bg_balk.jpg"><b>Subject</b></td>
								<td width="150" background="img/bg_balk.jpg"><b>Date</b></td>
								<td width="150" background="img/bg_balk.jpg">&nbsp;</td>
							</tr>
							<?
							$sql_mails = "SELECT * FROM $table[mail] WHERE `to_player` = '$playerdata[id]' ORDER BY `date` DESC";
							$res_mails = mysql_query($sql_mails);
							if (@mysql_num_rows($res_mails) < 1) {
							?>
							<tr><td width="800" colspan="4" align="center">There are no mails.</td></tr>
							<?
							}
							else {
								while ($rec_mails = mysql_fetch_array($res_mails)) {
									$bo = null;
									$bc = null;
									if ($rec_mails['read'] == 0) { $bo = '<b>'; $bc = '</b>'; }
									$c = getXYZ($rec_mails['from_player']);
							?>
							<form method="POST" action="main.php?mod=main&act=mail&do=delete&mail_id=<?echo $rec_mails['id'];?>">
							<tr>
								<td><a href="main.php?mod=main&act=mail&do=read&mail_id=<?echo $rec_mails['id'];?>"><?echo $bo.getRulernameById($rec_mails['from_player']).' of '.getPlanetnameById($rec_mails['from_player']).'('.$c[0].':'.$c[1].':'.$c[2].')'.$bc;?></a></td>
								<td><a href="main.php?mod=main&act=mail&do=read&mail_id=<?echo $rec_mails['id'];?>"><?echo $bo.$rec_mails['subject'].$bc;?></a></td>
								<td><a href="main.php?mod=main&act=mail&do=read&mail_id=<?echo $rec_mails['id'];?>"><?echo $bo.date('H:i d-m-Y', $rec_mails['date']).$bc;?></a></td>
								<td align="center"><input type="submit" name="deletemail" value="  Delete  "></td>
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