<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}
if (!isset($do)) { $do = secureData($_GET['do']); }
if (!isset($do)) { $do = secureData($_POST['do']); }

$error = 0;

if ($do == 'newthread') {
	$date = time();
	$galaxy_id = $playerdata['galaxy_id'];
	$poster_id = $playerdata['id'];
	$subject = secureData($_POST['subject']);
	$text = secureData($_POST['text']);

	if ($subject == '') { $error = 101; }
	if ($text == '') { $error = 102; }

	if ($error < 100) {
		$sql_createthread = "INSERT INTO $table[galaxyforum_threads] (`galaxy_id`, `subject`, `starter`, `date`)
						VALUES ('$galaxy_id', '$subject', '$poster_id', '$date')";
		mysql_query($sql_createthread);
		$thread_id = mysql_insert_id();
		$sql_createpost = "INSERT INTO `$table[galaxyforum_posts]` (`thread_id`, `galaxy_id`, `poster_id`, `text`, `date`)
						VALUES ('$thread_id', '$galaxy_id', '$poster_id', '$text', '$date')";
		mysql_query($sql_createpost);
	}
}
if ($do == 'delthread') {
	$thread_id = secureData($_GET['thread_id']);
	if ((getGalaxyCommander($playerdata['galaxy_id']) != $playerdata['id']) && (getMOC($playerdata['galaxy_id']) != $playerdata['id'])) { $error = 103; }
	else {
		$sql_delthread = "DELETE FROM $table[galaxyforum_threads] WHERE `galaxy_id` = '$playerdata[galaxy_id]' AND `id` = '$thread_id'";
		$sql_delposts = "DELETE FROM $table[galaxyforum_posts] WHERE `galaxy_id` = '$playerdata[galaxy_id]' AND `thread_id` = '$thread_id'";
		mysql_query($sql_delthread) or die(mysql_error());
		mysql_query($sql_delposts) or die(mysql_error());
		if (mysql_affected_rows() > 0) {
			$error = 1;
		} else {
			$error = 104;
		}
	}
}

switch($error) {
	case 1:
	$msg = 'Thread was succesfully removed.';
	break;
	case 101:
	$msg = "It's not possible to create threads with no subject.";
	break;
	case 102:
	$msg = "What's the use in posting a thread with no message content? :-)";
	break;
	case 103:
	$msg = 'You\'re not the commander or minister of communication for this galaxy.';
	break;
	case 104:
	$msg = 'No threads/posts where deleted';
	break;
}
if ($msg) {
	if ($error > 100) { $error_color = 'red'; }
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
unset($error, $msg);
$moc_id = getMOC($playerdata['galaxy_id']);
$gc_id = getGalaxyCommander($playerdata['galaxy_id']);
?>

<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="180">Galaxy forum</td>
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
								<td width="800" colspan="3"><?=getForumPath($mod, 1);?></td>
							</tr>
							<tr>
								<td width="800" colspan="3">&nbsp;</td>
							</tr>
							<tr>
								<td width="325" background="img/bg_balk.jpg"><b>Thread title</b></td>
								<td width="150" background="img/bg_balk.jpg"><b>Starter</b></td>
								<td width="150" background="img/bg_balk.jpg"><b>Last poster</b></td>
								<td width="125" background="img/bg_balk.jpg"><b>Date</b></td>
								<td width="50" background="img/bg_balk.jpg">&nbsp;</td>
							</tr>
							<?
							$sql_threads = "SELECT $table[galaxyforum_threads].id, $table[galaxyforum_threads].galaxy_id, $table[galaxyforum_threads].subject, $table[galaxyforum_threads].starter,
												$table[galaxyforum_posts].date, $table[galaxyforum_posts].poster_id
											FROM $table[galaxyforum_threads]
											INNER JOIN $table[galaxyforum_posts] ON 
												$table[galaxyforum_threads].id = $table[galaxyforum_posts].thread_id
												AND $table[galaxyforum_threads].date = $table[galaxyforum_posts].date
											WHERE 
												$table[galaxyforum_threads].galaxy_id = '$playerdata[galaxy_id]'
											GROUP BY $table[galaxyforum_posts].thread_id
											ORDER BY $table[galaxyforum_posts].date DESC
							";
							$res_threads = mysql_query($sql_threads);
							if (@mysql_num_rows($res_threads) > 0) {
								while ($rec_threads = mysql_fetch_array($res_threads)) {
							?>
							<form method="POST" action="main.php?mod=<?=$mod;?>&act=forum&do=delthread&thread_id=<?=$rec_threads['id'];?>">
							<tr>
								<td><a href="main.php?mod=<?=$mod;?>&act=forum&subact=viewthread&thread_id=<?echo $rec_threads['id'];?>"><u><?echo stripslashes($rec_threads['subject']);?></u></a></td>
								<td><a href="main.php?mod=main&act=mail&do=compose&to=<?=$rec_threads['starter'];?>"><?echo getRulernameById($rec_threads['starter']);?> of <?echo getPlanetnameById($rec_threads['starter']);?></a></td>
								<td><a href="main.php?mod=main&act=mail&do=compose&to=<?=$rec_threads['poster_id'];?>"><?echo getRulernameById($rec_threads['poster_id']);?> of <?echo getPlanetnameById($rec_threads['poster_id']);?></a></td>
								<td><?echo date('H:i:s d-m-Y', $rec_threads['date']);?></td>
								<td align="center"><?if ($moc_id||$gc_id == $playerdata['id']) {?><input type="submit" name="delthread" value="del"><? }?></td>
							</tr>
							<?
								}
							} else {
							?>
							<tr>
								<td colspan="5" align="center">There are no threads in this galaxy forum.</td>
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
		<td width="180">Create new thread</td>
		<td width="612" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="4" background="img/border/L.gif">&nbsp;</td>
		<td width="696" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="800">
				<tr>
					<td valign="top">
						<form method="POST" action="main.php?mod=galaxy&act=forum&do=newthread" name="newthread">
						<table border="0" width="800">
							<tr>
								<td width="800" background="img/bg_balk.jpg" colspan="2"><b>Enter new thread information</b></td>
							</tr>
							<tr>
								<td width="100">Subject:</td>
								<td width="700"><input type="text" name="subject" size="50"></td>
							</tr>
							<tr>
								<td width="100" valign="top">Message:</td>
								<td width="700">
									<input type="button" name="bb_b" value="[b]" onclick="document.forms['newthread'].text.value=document.forms['newthread'].text.value+'[b]'+prompt('Text which should be bold:', '')+'[/b]'">
									<input type="button" name="bb_i" value="[i]" onclick="document.forms['newthread'].text.value=document.forms['newthread'].text.value+'[i]'+prompt('Text which should be italic:', '')+'[/i]'">
									<input type="button" name="bb_u" value="[u]" onclick="document.forms['newthread'].text.value=document.forms['newthread'].text.value+'[u]'+prompt('Text which should be underlined:', '')+'[/u]'">
									<input type="button" name="bb_url" value="[url]" onclick="document.forms['newthread'].text.value=document.forms['newthread'].text.value+'[url]'+prompt('URL you want to link:', '')+'[/url]'"><br />
									<textarea name="text" rows="10" cols="50"></textarea>
								</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="2" align="center"><input type="submit" name="postthread" value="  Create new thread  "></td>
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