<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}


if (!isset($do)) { $act = secureData($_GET['do']); }
if (!isset($do)) { $act = secureData($_POST['do']); }
$error = -1;
if ($do == 'deleteall') {
	$sql_delallnews = "DELETE FROM $table[playernews] WHERE `player_id` = '$playerdata[id]' AND `read` = '1'";
	mysql_query($sql_delallnews) or die(mysql_error());
	$error = 0;
}
if ($do == 'delete') {
	$news_id = secureData($_GET['news_id']);
	if ($news_id) {
		$sql_delnews = "DELETE FROM $table[playernews] WHERE `id` = '$news_id' AND `player_id` = '$playerdata[id]'";
		mysql_query($sql_delnews) or die(mysql_error());
		if (mysql_affected_rows() > 0) {
			$error = 1;
		} else {
			$error = 100;
		}
	} else {
		$error = 101;
	}
}
switch($error) {
	case 0:
		$msg = "Succesfully deleted all news items.";
		break;
	case 1:
		$msg = "Succesfully deleted the news item.";
		break;
	case 100:
		$msg = "The news could not be deleted. Maybe it isn't your news? :-)";
		break;
	case 101:
		$msg = "There was an error deleting your news item. Try clicking the delete buttons to delete news.";
		break;
}
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
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
		<td width="180">News items</td>
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
							$sql_news = "SELECT * FROM $table[playernews] WHERE `player_id` = '$playerdata[id]' ORDER BY `id` DESC, `date` DESC";
							$res_news = mysql_query($sql_news);
							if (@mysql_num_rows($res_news) > 0) {
							?>
							<tr>
								<td colspan="3" align="center"><form method="POST" action="main.php?mod=main&act=news&do=deleteall"><input type="submit" name="deleteallnews" value="  Delete all news "></form></td>
							</tr>
							<tr>
								<td width="85" background="img/bg_balk.jpg"><b>Category</b></td>
								<td width="595" background="img/bg_balk.jpg"><b>Item</b></td>
								<td width="125" background="img/bg_balk.jpg"><b>Date</b></td>
							</tr>
							<?
							while ($rec_news = mysql_fetch_array($res_news)) {
							?>
							<tr>
								<td rowspan="2" valign="top"><?echo $rec_news['category'];?></td>
								<td><b><?echo $rec_news['subject'];?></b></td>
								<td><?echo date('H:i d-m-Y', $rec_news['date']);?></td>
							</tr>
							<tr>
								<td valign="top"><?echo $rec_news['text'];?></td>
								<td align="right"><form method="POST" action="main.php?mod=main&act=news&do=delete&news_id=<?echo $rec_news['id'];?>"><input type="submit" name="deletenews" value="  Delete  "></form></td>
							</tr>
							<?
							}
							}
							else {
							?>
							<tr>
								<td colspan="3" align="center">There is no news for your planet!</td>
							</tr>
							<?
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
$sql_readednews = "UPDATE $table[playernews] SET `read` = '1' WHERE `player_id` = '$playerdata[id]'";
mysql_query($sql_readednews);
?>