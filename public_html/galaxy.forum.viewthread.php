<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}

if (!isset($thread_id)) { $thread_id = $_GET['thread_id']; }

$sql_threaddata = "SELECT * FROM $table[galaxyforum_threads] WHERE `id` = '$thread_id' AND `galaxy_id` = '$playerdata[galaxy_id]' ORDER BY `date` DESC";
$rec_threaddata = mysql_fetch_array(mysql_query($sql_threaddata));

if ($do == 'newreply') {
	$error = 0;
	$date = time();
	$galaxy_id = $playerdata['galaxy_id'];
	$poster_id = $playerdata['id'];
	$text = secureData($_POST['replytext']);

	if ($text == '') { $error = 101; }

	if ($error < 100) {
		$sql_createpost = "INSERT INTO $table[galaxyforum_posts] (`thread_id`, `galaxy_id`, `poster_id`, `text`, `date`)
						VALUES ('$thread_id', '$galaxy_id', '$poster_id', '$text', '$date')";
		$sql_updatepostdate = "UPDATE $table[galaxyforum_threads] SET `date` = '$date' WHERE `id` = '$thread_id'";
		mysql_query($sql_createpost) or die(mysql_error());
		mysql_query($sql_updatepostdate) or die(mysql_error());
	}
}

switch($error) {
	case 101:
	$msg = "What's the use in posting a message with no content? :-)";
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
?>

<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="180">View thread</td>
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
								<td width="800"><?=getForumPath($mod, 2, $thread_id);?></td>
							</tr>
							<tr>
								<td width="800">&nbsp;</td>
							</tr>
							<tr>
								<td width="800" background="img/bg_balk.jpg"><b>Subject: <?echo stripslashes($rec_threaddata['subject']);?></b></td>
							</tr>
							<tr height="15">
								<td>&nbsp;</td>
							</tr>
							<?
							$sql_postdata = "SELECT * FROM $table[galaxyforum_posts] WHERE `thread_id` = '$thread_id' AND `galaxy_id` = '$playerdata[galaxy_id]' ORDER BY `date`";
							$res_postdata = mysql_query($sql_postdata);
							if (@mysql_num_rows($res_postdata) > 0) {
								while ($rec_postdata = mysql_fetch_array($res_postdata)) {
							?>
							<tr>
								<td>
								<table width="800" style="border: 1px solid #3C5762">
									<tr>
										<td align="right">Posted by <b><?=getRulernameById($rec_postdata['poster_id']);?> of <?=getPlanetnameById($rec_postdata['poster_id']);?></b> at <b><?echo date('H:i:s d-m-Y', $rec_postdata['date']);?></b></td>
									</tr>
									<tr>
										<td width="650"><?echo nl2br(parseBBcode(stripslashes($rec_postdata['text']), 1, 1, 1, 1, 0));?></td>
									</tr>
								</table>
								</td>
							</tr>
							<tr height="5">
								<td></td>
							</tr>
							<?
								}
							} else {
							?>
							<tr>
								<td align="center">This thread has no posts, or is not a valid thread for your galaxy.</td>
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
		<td width="180">Post reply</td>
		<td width="612" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
		<td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
	<tr>
		<td width="4" background="img/border/L.gif">&nbsp;</td>
		<td width="696" height="100%" valign="top" colspan="2">
			<table border="0" cellpadding="0" cellspacing="0" width="800">
				<tr>
					<td valign="top">
					<form method="POST" name="reply" action="main.php?mod=<?=$mod;?>&act=forum&subact=viewthread&do=newreply&thread_id=<?echo $thread_id;?>">
						<table border="0" width="800">
							<tr>
								<td width="800" background="img/bg_balk.jpg" colspan="2"><b>Enter new reply information</b></td>
							</tr>
							<tr>
								<td width="100" valign="top"></td>
								<td width="700">
									<input type="button" name="bb_b" value="[b]" onclick="document.forms['reply'].replytext.value=document.forms['reply'].replytext.value+'[b]'+prompt('Text which should be bold:', '')+'[/b]'">
									<input type="button" name="bb_i" value="[i]" onclick="document.forms['reply'].replytext.value=document.forms['reply'].replytext.value+'[i]'+prompt('Text which should be italic:', '')+'[/i]'">
									<input type="button" name="bb_u" value="[u]" onclick="document.forms['reply'].replytext.value=document.forms['reply'].replytext.value+'[u]'+prompt('Text which should be underlined:', '')+'[/u]'">
									<input type="button" name="bb_url" value="[url]" onclick="document.forms['reply'].replytext.value=document.forms['reply'].replytext.value+'[url]'+prompt('URL you want to link:', '')+'[/url]'">
								</td>
							</tr>
							<tr>
								<td width="100" valign="top">Message:</td>
								<td width="700">
									<textarea name="replytext" rows="10" cols="120"></textarea>
								</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="2" align="center"><input type="submit" name="posteply" value="  Submit reply  "></td>
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