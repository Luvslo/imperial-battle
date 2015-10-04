<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}
$error = 0;

$alliance_id = $playerdata['alliance_id'];

$sql_alliancedata = "SELECT `name`, `tag`, `password`, `founder_id`, `subcommander_id`, `message` FROM $table[alliance] WHERE `id` = '$alliance_id'";
$res_alliancedata = mysql_query($sql_alliancedata);
$rec_alliancedata = mysql_fetch_array($res_alliancedata);
if ($alliance_id == 0) {
?>
<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td align="center">You are not a member of an alliance.</td>
        </tr>
</table>
<?
}
elseif (($rec_alliancedata['subcommander_id'] == $playerdata['id']) || ($rec_alliancedata['founder_id'] == $playerdata['id'])) {

	if (!isset($do)) { $act = addslashes($_GET['do']); }
	if (!isset($do)) { $act = addslashes($_POST['do']); }
	if ($do == 'updatemsg') {
		$time = time();
		$message = secureData($_POST['message']);
		$sql_updatemsg = "UPDATE $table[alliance] SET `message` = '$message', `message_lastedit` = '$time' WHERE `id` = '$alliance_id' AND (`founder_id` = '$playerdata[id]' OR `subcommander_id` = '$playerdata[id]')";
		mysql_query($sql_updatemsg) or die(mysql_error());
	}
	if ($do == 'addnews') {
		$subject = secureData($_POST['subject']);
		$message = secureData($_POST['message']);
		$time = time();
		$sql_addnews = "INSERT INTO $table[alliancenews] (`alliance_id` , `subject` , `message` , `player_id` , `timestamp` )
						VALUES ('$playerdata[alliance_id]', '$subject', '$message', '$playerdata[id]', '$time')";
		mysql_query($sql_addnews) or die(mysql_error());
	}
	if ($do == 'newpass') {
		$name = getAllianceName($playerdata['id']);
		$tag = getAllianceTag($playerdata['id']);;
		$time = time();
		$length = rand(10, 27);
		$password = substr(md5($time.$name.$tag.'dfug984758945hgjkdfhg48975348796gfqwqwoiuytubvcbnctre56443984t5394875dkjfhgdkjfhg'), 5, $length);
		$sql_updpass = "UPDATE $table[alliance] SET `password` = '$password' WHERE `id` = '$playerdata[alliance_id]' AND `founder_id` = '$playerdata[id]'";
		mysql_query($sql_updpass) or die(mysql_error());
	}
	if ($do == 'changesubcommander') {
		if ($playerdata['id'] == getAllianceCommander($playerdata['alliance_id'])) {
			$subcom_id = secureData($_POST['subcom_id']);
			$sql_updsubcom = "UPDATE $table[alliance] SET `subcommander_id` = '$subcom_id' WHERE `id` = '$playerdata[alliance_id]'";
			mysql_query($sql_updsubcom) or die(mysql_error());
		} else {
			$error = 100;
		}
	}
	if ($do == 'kickmem') {
		$player_id = secureData($_POST['player_id']);
		if ($player_id > 0) {
			$sql_kick = "UPDATE $table[players] SET `alliance_id` = '0' WHERE `id` = '$player_id'";
			mysql_query($sql_kick) or die(mysql_error());
			$error = 1;
		}
	}
	if ($do == 'massmail') {
		$subject = secureData($_POST['subject']);
		$text = secureData($_POST['text']);

		$sql_allmembers = "SELECT `id` FROM $table[players] WHERE `alliance_id` = '$playerdata[alliance_id]'";
		$res_allmembers = mysql_query($sql_allmembers);
		$num_allmembers = mysql_num_rows($res_allmembers);
		if ($subject == '') { $error = 101; }
		if ($text == '') { $error = 102; }

		while ($rec_allmembers = mysql_fetch_array($res_allmembers)) {
			$player_to_id = $rec_allmembers['id'];
			if ($error < 100) {
				$sql_newmail = "INSERT INTO $table[mail] (`from_player`, `to_player`, `subject`, `text`, `date`)
						VALUES ('$playerdata[id]', '$player_to_id', '$subject', '$text', UNIX_TIMESTAMP())";
				mysql_query($sql_newmail);
			}
		}
	}

	$sql_alliancedata = "SELECT `name`, `tag`, `password`, `founder_id`, `message` FROM $table[alliance] WHERE `id` = '$alliance_id'";
	$res_alliancedata = mysql_query($sql_alliancedata);
	$rec_alliancedata = mysql_fetch_array($res_alliancedata);

	switch($error) {
		case 1:
		$msg = "The player was removed succesfully from your alliance.";
		break;
		case 100:
		$msg = "You're trying to hack, ain't you?";
		break;
		case 101:
		$msg = "A mail is useless with an empty subject.";
		break;
		case 102:
		$msg = "Why sending empty mails?";
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
<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
                <td width="180">Alliance settings</td>
                <td width="612" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
                <td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
        </tr>
        <tr>
                <td width="4" background="img/border/L.gif">&nbsp;</td>
                <td width="696" height="100%" valign="top" colspan="2">
                        <table border="0" cellpadding="0" cellspacing="0" width="800">
                                <tr>
                                        <td valign="top">
                                                <form method="POST" action="main.php?mod=alliance&act=admin&do=addnews">
                                                <table border="0" width="800">
                                                		<tr>
															<td colspan="2" background="img/bg_balk.jpg"><b>Add news item</b></td>                                                			
                                                		</tr>
                                                        <tr>
                                                                <td width="75" valign="top">Subject:</td>
                                                                <td width="725"><input type="text" name="subject" size="65"></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="75" valign="top">Message:</td>
                                                                <td width="725"><textarea rows="10" cols="100" name="message"></textarea></td>
                                                        </tr>
                                                        <tr>
                                                        	<td colspan="2" align="center"><input type="submit" name="changemessage" value="  Add news  "></td>
                                                        </tr>
                                                </table>
                                      			</form>
                                                <br>
                                        		<form method="POST" action="main.php?mod=alliance&act=admin&do=updatemsg" name="mftc">
                                                <table border="0" width="800">
                                                		<tr>
															<td colspan="2" background="img/bg_balk.jpg"><b>Message from the commander</b></td>                                                			
                                                		</tr>
                                                        <tr>
                                                                <td width="75" valign="top">Content:</td>
                                                                <td width="725">
																	<input type="button" name="bb_b" value="[b]" onclick="document.forms['mftc'].message.value=document.forms['mftc'].message.value+'[b]'+prompt('Text which should be bold:', '')+'[/b]'">
																	<input type="button" name="bb_i" value="[i]" onclick="document.forms['mftc'].message.value=document.forms['mftc'].message.value+'[i]'+prompt('Text which should be italic:', '')+'[/i]'">
																	<input type="button" name="bb_u" value="[u]" onclick="document.forms['mftc'].message.value=document.forms['mftc'].message.value+'[u]'+prompt('Text which should be underlined:', '')+'[/u]'">
																	<input type="button" name="bb_url" value="[url]" onclick="document.forms['mftc'].message.value=document.forms['mftc'].message.value+'[url]'+prompt('URL you want to link:', '')+'[/url]'"><br />
                                                                	<textarea rows="10" cols="100" name="message"><?echo stripslashes($rec_alliancedata['message']);?></textarea></td>
                                                        </tr>
                                                        <tr>
                                                        	<td colspan="2" align="center"><input type="submit" name="changemessage" value="  Update message  "></td>
                                                        </tr>
                                                </table>
                                                </form>
                                                <br>					
                                                <form method="POST" action="main.php?mod=alliance&act=admin&do=massmail" name="massmail">
												<table border="0" width="800">
													<tr>
														<td width="800" align="left" background="img/bg_balk.jpg" colspan="2"><b>Alliance mass mail</b></td>
													</tr>
													<tr>
														<td width="75">Subject:</td>
														<td width="725"><input type="text" name="subject" size="70"></td>
													</tr>
													<tr>
														<td width="75" valign="top">Content:</td>
														<td width="725">
															<input type="button" name="bb_b" value="[b]" onclick="document.forms['massmail'].text.value=document.forms['massmail'].text.value+'[b]'+prompt('Text which should be bold:', '')+'[/b]'">
															<input type="button" name="bb_i" value="[i]" onclick="document.forms['massmail'].text.value=document.forms['massmail'].text.value+'[i]'+prompt('Text which should be italic:', '')+'[/i]'">
															<input type="button" name="bb_u" value="[u]" onclick="document.forms['massmail'].text.value=document.forms['massmail'].text.value+'[u]'+prompt('Text which should be underlined:', '')+'[/u]'">
															<input type="button" name="bb_url" value="[url]" onclick="document.forms['massmail'].text.value=document.forms['massmail'].text.value+'[url]'+prompt('URL you want to link:', '')+'[/url]'"><br />
															<textarea rows="9" cols="70" name="text"></textarea>
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
                                                <br>
                                        		<form method="POST" action="main.php?mod=alliance&act=admin&do=kickmem">
                                                <table border="0" width="800">
                                                		<tr>
															<td colspan="2" background="img/bg_balk.jpg"><b>Kick alliance member</b></td>                                                			
                                                		</tr>
                                                        <tr>
                                                                <td width="75" valign="top">Username:</td>
                                                                <td width="725">
                                                                <select name="player_id">
                                                                	<option value="0">-</option>
                                                                <?
                                                                $sql_amem = "SELECT `id`, `rulername`, `planetname` FROM `$table[players]` WHERE `alliance_id` = '$playerdata[alliance_id]' ORDER BY `username`";
                                                                $res_amem = mysql_query($sql_amem);
                                                                $num_amem = @mysql_num_rows($res_amem);
                                                                if ($num_amem > 0) {
                                                                	while ($rec_amem = mysql_fetch_array($res_amem)) {
                                                                ?>
                                                                	<option value="<?=$rec_amem['id']?>"><?=$rec_amem['rulername']?> of <?=$rec_amem['planetname']?></option>
                                                                <?
                                                                	}
                                                                }
                                                                ?>
                                                                </select>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                        	<td colspan="2" align="center"><input type="submit" name="kickmem" value="  Kick member  "></td>
                                                        </tr>
                                                </table>
                                                </form>
                                                <br>
                                                <?
                                                if ($playerdata['id'] == getAllianceCommander($playerdata['alliance_id'])) {
                                                ?>
                                        		<form method="POST" action="main.php?mod=alliance&act=admin&do=changesubcommander">
                                                <table border="0" width="800">
                                                		<tr>
															<td colspan="2" background="img/bg_balk.jpg"><b>Alliance sub commander</b></td>                                                			
                                                		</tr>
                                                        <tr>
                                                                <td width="75" valign="top">Username:</td>
                                                                <td width="725"><select name="subcom_id">
                                                                <?
                                                                $sql_alliancemembers = "SELECT `id`, `rulername`, `planetname` FROM $table[players] WHERE `alliance_id` = '$playerdata[alliance_id]'";
                                                                $res_alliancemembers = mysql_query($sql_alliancemembers);
                                                                $subcommander_id = getAllianceSubCommander($playerdata['alliance_id']);
                                                                while ($rec_alliancemembers = mysql_fetch_array($res_alliancemembers)) {
                                                                	$sel = null;
                                                                	if ($rec_alliancemembers['id'] != $playerdata['id']) {
                                                                		if ($rec_alliancemembers['id'] == $subcommander_id) { $sel = ' selected'; }
                                                                		echo '<option value="'.$rec_alliancemembers['id'].'"'.$sel.'>'.$rec_alliancemembers['rulername'].' of '.$rec_alliancemembers['planetname'].'</option>';
                                                                	} else {
                                                                		continue;
                                                                	}
                                                                }
                                                                ?>
                                                                </select>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                        	<td colspan="2" align="center"><input type="submit" name="changemessage" value="  Make sub commander  "></td>
                                                        </tr>
                                                </table>
                                                </form>
                                                <br>
                                                <?
                                                }
												?>
                                                <table border="0" width="800">
                                                		<tr>
															<td colspan="2" background="img/bg_balk.jpg"><b>Join password</b></td>                                                			
                                                		</tr>
                                                        <tr>
                                                                <td width="75" valign="top">Password:</td>
                                                                <td width="725"><?echo $rec_alliancedata['password'];?></td>
                                                        </tr>
                                                        <form method="POST" action="main.php?mod=alliance&act=admin&do=newpass">
                                                        <tr>
                                                                <td align="center" valign="top" colspan="2"><input type="submit" name="generatepass" value="  Generate new alliance password  "></td>
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
} else {
?>

<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td align="center">You are not a commander or sub commander for this alliance. This page is only available for the alliance commander.</td>
        </tr>
</table>
<?
}
?>