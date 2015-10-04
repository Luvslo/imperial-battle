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
?>
<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
                <td width="180">Memberlist</td>
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
                                                                <td width="75" background="img/bg_balk.jpg" align="center"><b>Location</b></td>
                                                                <td width="450" background="img/bg_balk.jpg" align=""><b>Ruler- & planetname</b></td>
                                                                <td width="100" background="img/bg_balk.jpg" align="right"><b>Score</b></td>
                                                                <td width="75" background="img/bg_balk.jpg" align="right"><b>Asteroids</b></td>
                                                                <td width="100" background="img/bg_balk.jpg" align="center"><b>Status</b></td>
                                                        </tr>                                                
                                                <?
												$sql_memberlist = "SELECT `id`, `username`, `rulername`,`planetname`, `lastlogin`, `roid_steel`, `roid_crystal`, `roid_erbium`, `roid_unused`, `score` FROM $table[players] WHERE `alliance_id` = '$alliance_id' ORDER BY `score` DESC";
												$res_memberlist = mysql_query($sql_memberlist);
												while ($rec_memberlist = mysql_fetch_array($res_memberlist)) {
													$currenttime = time();
													$logintime = $rec_memberlist['lastlogin'];
													$time_diff = $currenttime - $logintime;
													if ($time_diff > 300) {
														$status_color = 'red';
														$status = 'Offline';
													} else {
														$status_color = 'green';
														$status = 'Online';
													}
													
													$xyz = getXYZ($rec_memberlist['id']);
													$totalroids = 0;
													$totalroids += $rec_memberlist['roid_steel'];
													$totalroids += $rec_memberlist['roid_crystal'];
													$totalroids += $rec_memberlist['roid_erbium'];
													$totalroids += $rec_memberlist['roid_unused'];
                                                ?>

                                                        <tr>
                                                                <td align="center"><?echo $xyz[0].':'.$xyz[1].':'.$xyz[2];?></td>
                                                                <td align="center"><a href="main.php?mod=main&act=mail&do=compose&x=<?=$xyz[0];?>&y=<?=$xyz[1];?>&z=<?=$xyz[2];?>"><? echo $rec_memberlist['rulername']; ?> of <?echo $rec_memberlist['planetname'];?></a></td>

                                                                <td align="right"><?echo parseInteger($rec_memberlist['score']);?></td>
                                                                <td align="right"><?echo parseInteger($totalroids);?></td>
                                                                <td align="center"><font color="<?echo $status_color;?>"><?echo $status;?></font></td>
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