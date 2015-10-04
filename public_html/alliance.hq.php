<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}
$alliance_id = $playerdata['alliance_id'];
if ($alliance_id == 0) {
?>
<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td align="center">You are not a member of an alliance.</td>
        </tr>
</table>
<?
} else {
if (!isset($do)) { $act = secureData($_GET['do']); }
if (!isset($do)) { $act = secureData($_POST['do']); }

if ($do == 'deletenews') {
	$news_id = secureData($_GET['news_id']);
	$sql_delnews = "DELETE FROM $table[alliancenews] WHERE `id` = '$news_id' AND `alliance_id` = '$alliance_id'";
	mysql_query($sql_delnews) or die(mysql_error());
}
?>
<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
                <td width="180">Alliance information</td>
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
                                                        	<td align="right" width="400" valign="top">
                                                               	<table border="0" width="400">
                                                               	<?
                                                               	$sql_totalmembers = "SELECT `id`, `username`, `roid_steel`, `roid_crystal`, `roid_erbium`, `roid_unused`,`score` FROM $table[players] WHERE `alliance_id` = '$alliance_id'";
                                                               	$res_totalmembers = mysql_query($sql_totalmembers);
                                                               	$num_totalmembers = mysql_num_rows($res_totalmembers);
                                                               	$num_totalroids = 0;
                                                               	$num_totalscore = 0;
                                                               	while ($rec_totalmembers = mysql_fetch_array($res_totalmembers)) {
                                                               		$num_totalroids += $rec_totalmembers['roid_steel'];
                                                               		$num_totalroids += $rec_totalmembers['roid_crystal'];
                                                               		$num_totalroids += $rec_totalmembers['roid_erbium'];
                                                               		$num_totalroids += $rec_totalmembers['roid_unused'];
                                                               		$num_totalscore += $rec_totalmembers['score'];
                                                               	}
                                                               	?>
                                                               		<tr>
                                                               			<td width="150">Alliance name:</td>
                                                               			<td width="250"><?=getAllianceName($playerdata['id']);?></td>
                                                               		</tr>
                                                               		<tr>
                                                               			<td width="150">Alliance tag:</td>
                                                               			<td width="250"><?=getAllianceTag($playerdata['id']);?></td>
                                                               		</tr>
                                                               		<tr>
                                                               			<td width="150">Alliance commander:</td>
                                                               			<td width="250"><?=getRulernameById(getAllianceCommander($alliance_id));?> of <?=getPlanetnameById(getAllianceCommander($alliance_id));?></td>
                                                               		</tr>
                                                               		<tr>
                                                               			<td width="150">Alliance sub commander:</td>
                                                               			<td width="250"><? if (getAllianceSubCommander($alliance_id)) { ?><?=getRulernameById(getAllianceSubCommander($alliance_id));?> of <?=getPlanetnameById(getAllianceSubCommander($alliance_id));?><? } else { echo '-'; } ?></td>
                                                               		</tr>
                                                               		<tr>
                                                               			<td width="150">Alliance start date:</td>
                                                               			<td width="250"><?=date("H:i d-m-Y", getAllianceStartdate($alliance_id));?></td>
                                                               		</tr>
                                                               	</table>
                                                            </td>
                                                            <td width="400" valign="top">
                                                               	<table border="0" width="400">
                                                               		<tr>
                                                               			<td width="150">Current rank:</td>
                                                               			<td width="250">-</td>
                                                               		</tr>
                                                               		<tr>
                                                               			<td width="150">Total score:</td>
                                                               			<td width="250"><?=parseInteger($num_totalscore);?></td>
                                                               		</tr>
                                                               		<tr>
                                                               			<td width="150">Total asteroids:</td>
                                                               			<td width="250"><?=parseInteger($num_totalroids);?></td>
                                                               		</tr>
                                                               		<tr>
                                                               			<td width="150">Total members:</td>
                                                               			<td width="250"><?=$num_totalmembers;?></td>
                                                               		</tr>
                                                               	</table>
                                                            </td>
                                                            </td>
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
                <td width="180">Message from your commander</td>
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
														$sql_alliancedata = "SELECT `message`, `message_lastedit` FROM $table[alliance] WHERE `id` = '$alliance_id'";
														$rec_alliancedata = mysql_fetch_array(mysql_query($sql_alliancedata));
                                                		?>
                                                        <tr>
                                                                <td width="800" background="img/bg_balk.jpg" align="right"><b>Message posted at: <?=date("H:i d-m-Y", $rec_alliancedata['message_lastedit']);?></b></td>
                                                        </tr>
                                                        <tr>
                                                                <td align="left"><?=nl2br(parseBBcode(stripslashes($rec_alliancedata['message']), 1, 1, 1, 1));?></td>
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
                <td width="180">Alliance news</td>
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
                                                $sql_alliancenews = "SELECT `id`, `subject`, `message`, `player_id`, `timestamp` FROM $table[alliancenews] WHERE `alliance_id` = '$alliance_id' ORDER BY `timestamp` DESC";
                                                $res_alliancenews = mysql_query($sql_alliancenews);
                                                if (@mysql_num_rows($res_alliancenews) > 0) {
                                                	while ($rec_alliancenews = mysql_fetch_array($res_alliancenews)) {
                                                ?>
                                                        <tr>
                                                                <td width="475" background="img/bg_balk.jpg"><b><?=stripslashes($rec_alliancenews['subject']); ?></b></td>
                                                                <td width="100" background="img/bg_balk.jpg"><b><?=getRulernameById($rec_alliancenews['player_id']);?> of <?=getPlanetnameById($rec_alliancenews['player_id']);?></b></td>
                                                                <td width="125" background="img/bg_balk.jpg"><b><?=date("H:i d-m-Y", $rec_alliancenews['timestamp']);?></b></td>
                                                        </tr>
                                                        <tr>
                                                                <td colspan="2" valign="top"><?echo nl2br(stripslashes($rec_alliancenews['message']));?></td>
                                                                <td align="center" valign="top">
                                                                <?
																$sql_alliancedata = "SELECT `founder_id` FROM $table[alliance] WHERE `id` = '$alliance_id'";
																$rec_alliancedata = mysql_fetch_array(mysql_query($sql_alliancedata));
																if ($rec_alliancedata['founder_id'] == $playerdata['id']) {
																?>
																<form method="POST" action="main.php?mod=alliance&act=hq&do=deletenews&news_id=<?echo $rec_alliancenews['id'];?>"><input type="submit" name="deletenews" value="  Delete  "></form>
																<?
																} else {
																	echo '&nbsp;';
																}
                                                                ?>
                                                                </td>
                                                        </tr>
                                                <?
                                                	}
                                                } else {
												?>
                                                        <tr>
                                                                <td align="center">There is no news for your alliance.</td>
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
}
?>